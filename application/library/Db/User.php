<?php
/**
 * 数据库操作DAO
 * @author Bill Zhang
 * @desc 用户的DAO抽离
 * @see blog.make3.cn
 */
class Db_User extends Db_Base{

    /**
     * 检查用户是否存在
     * @param string $username 用户名(手机号)
     * @return bool false不存在|存在
     */
    public function checkExists( $username ){
        $query = self::getDb()->prepare("select count(*) as c from `mc_users` where `username` = ? ");
        $query->execute(array($username));
        $count = $query->fetchAll();
        if( $count[0]['c'] != 0 ){
            self::$code = 203;
            return false;
        }
        return true;
    }

    /**
     * 用户添加操作
     * @param string $username 用户名
     * @param string $password 用户密码
     * @param string $mac 注册手机mac
     * @param string $openid 第三方登录的openid
     * @param string $type 注册方式
     * @param string $nickname 用户昵称
     * @param string $figureurl 用户头像地址
     * @param string $gender 性别 boy|giry
     * @param string $country 国家
     * @param string $province 省份
     * @param string $city 城市
     * @return bool true|false 添加成功|添加失败
     */
    public function add(
        $username,$password,$mac,$openid="",$type="username",
        $nickname="美客玩用户",$figureurl="",
        $gender="boy",$country="", $province="",$city=""
    ){
       $registerIp = Common_Func::getIp();
        $query = self::getDb()->prepare(
            "insert into `mc_users` (`username`,`password`,`mac`,`figureurl`,`qq_openid`,`wechat_openid`,
             `gender`,`nickname`,`country`,`province`,`city`,`register_ip`) VALUES  (?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        if(!$query){
            self::$code = 112;
            return false;
        }
        $ret = false;
        switch($type){
            case "username":
                $ret = $query->execute(array( $username,$password,$mac,$figureurl,'','',$gender,$nickname,$country,$province,$city,$registerIp));
                break;
            case "qq":
                $ret = $query->execute(array( $username,$password,$mac,$figureurl,$openid,'',$gender,$nickname,$country,$province,$city,$registerIp));
                break;
            case "wechat":
                $ret = $query->execute(array( $username,$password,$mac,$figureurl,'',$openid,$gender,$nickname,$country,$province,$city,$registerIp));
                break;
        }
        if( !$ret ){
            self::$code = 105;
            return false;
        }
        return true;
    }

    /**
     * find查找用户
     * @param string $username 用户名
     * @return array $username $password ...
     */
    public function find( $username ){
        $query = self::getDb()->prepare("select `password`,`id`from `mc_users` where `username` = ? ");
        if(!$query->execute(array($username))){
            self::$code = 106;
            return false;
        }
        if( !$ret = $query->fetchAll() ){
            self::$code = 217;
            return false;
        }
        return $ret[0];
    }

    /**
     * 查找用户
     * @param string $openid openid
     * @param string $type 登录方式
     * @return array $username $uid
     */
    public function thirdFind( $openid , $type){
        if($type == "qq"){
            $query = self::getDb()->prepare("select `username`,`id`from `mc_users` where `qq_openid` = ? ");
        }
        if($type == "wechat"){
            $query = self::getDb()->prepare("select `username`,`id`from `mc_users` where `wechat_openid` = ? ");
        }

        if(!$query->execute(array($openid))){
            self::$code = 106;
            return false;
        }
        if( !$ret = $query->fetchAll() ){
            self::$code = 217;
            return false;
        }
        $data = array("uid"=>$ret[0]['id'],"username"=>$ret[0]['username']);
        return $data;
    }

    /**
     * 获取用户所有信息
     * @param int $uid 用户id
     * @return array $userInfo
     */
    public function getUserInfo( $uid ){
        $query = self::getDb()->prepare(
            "select `username`,`nickname`,`figureurl`,`gender`,`status`,`create_time` from `mc_users` where `id` = ? "
        );
        if(!$query->execute(array($uid))){
            self::$code = 106;
            return false;
        }
        if( !$ret = $query->fetch() ){
            self::$code = 217;
            return false;
        }
        $data = array(
            'uid'=>$uid,
            'username'=>$ret['username'],
            'nickname'=>$ret['nickname'],
            'figureurl'=>$ret['figureurl'],
            'gender'=>$ret['gender'],
            'status'=>$ret['status'],
            'create_time'=>$ret['create_time'],
        );
        return $data;
    }

    /**
     * 用户修改密码
     * @param int $username 用户名（手机号）
     * @param string $password 密码
     * @return bool true|false
     */
    public function fixPassword( $username ,$password ){
        $query = self::getDb()->prepare("UPDATE  `mc_users` SET `password`=? WHERE `username`=?");
        if(!$query->execute(array($password,$username))){
            self::$code = 107;
            return false;
        }
        return true;
    }

}