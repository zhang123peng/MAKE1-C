<?php
/**
 * 数据库操作DAO
 * @author Bill Zhang
 * @desc 短信的DAO抽离
 * @see blog.make3.cn
 */
class Db_Msg extends Db_Base{
    /**
     * 添加短信发送记录
     * @param int $phone 手机号码
     * @param int $verif 验证码
     * @param string $ip 请求地址
     * @return bool true|false
     */
    public function add($phone , $verif , $ip,$type){
        $query = self::getDb()->prepare(
            "insert into `mc_msg` (`phone`,`verif`,`ip`,`type`) VALUES  (?,?,?,?)"
        );
        if(!$query->execute(array($phone,$verif,$ip,$type))){
            self::$code = 105;
            return false;
        }
        return true;
    }


    /**
     * 检查是否超过60秒
     * @param int $phone 手机号
     * @param int $verif 验证码
     * @param string $type 动作方式
     * @return bool true操作60秒 | false没有超过60秒
     */
    public function checkOut($phone,$verif,$type){
        $query = self::getDb()->prepare(
            "select `crete_time` as c from `mc_msg` where `phone` = ?  AND `verif`=? AND `type`=?"
        );
        if(!$query->execute(array($phone,$verif,$type))){
            self::$code = 106;
            return false;
        }
        $ret = $query->fetchAll();
        return true;
    }

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
}