<?php
/**
 * @name UserModel
 * @desc 用户Model
 * @author Bill Zhang
 * @see blog.make3.cn
 */
class UserModel {
    public $code = 0;

    private $_dao = null;

    /**
     * 数据库连接池
     */
    public function __construct()
    {
        $this->_dao = new Db_User();
    }


    /**
     * register 用户注册Model
     * 检测用户是否存在-》加密密码-》写入数据库
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
     * @return bool true注册成功|false注册失败
     */
    public function register(
        $username,$password,$mac,$openid="",$type="username",
        $nickname="美客玩用户",$figureurl="",
        $gender="boy",$country="", $province="",$city=""
    ){
       /*检测用户是否存在*/
        if(!$this->_dao->checkExists(trim($username))){
            $this->code = $this->_dao->code();
            return false;
        }
        $password =Common_Func::password_generate($password);
        /*写入用户数据*/
        if( !$this->_dao->add(
            $username,$password,$mac,$openid,$type,
            $nickname,$figureurl,
            $gender,$country, $province,$city
        ) ){
            $this->code = $this->_dao->code();
            return false;
        }
        return true;
    }


    /**
     * login 用户登录Model
     * @name
     * @param string $username 用户名
     * @param string $password 用户密码
     * @return mix  bool|username  false登录失败|登录成功返回username
     */
     public function login($username , $password){
         /*检测用户是否存在*/
       if(!$userInfo=$this->_dao->find(trim($username))){
           $this->code = $this->_dao->code();
           return false;
       }
         if( Common_Func::password_generate($password) != $userInfo['password']){
             $this->code = 207;
             return false;
         }
         return intval($userInfo[1]);
     }


    /**
     * thirdLogin 第三方用户登录Model
     * @name
     * @param string $openid openid
     * @param string $type 登录方式
     * @return mix  bool|username  false登录失败|登录成功返回username
     */
    public function thirdLogin($openid , $type){
        /*检测用户是否存在*/
        if(!$userInfo=$this->_dao->thirdFind(trim($openid),$type)){
            $this->code = $this->_dao->code();
            return false;
        }
        return $userInfo;
    }

    /**
     * 获取用户信息
     * @name
     * @param string $uid 用户id
     * @return array $userInfo 用户信息
     */
    public function getUserInfo($uid){
        /*检测用户是否存在*/
        if(!$userInfo=$this->_dao->getUserInfo(intval($uid))){
            $this->code = $this->_dao->code();
            return false;
        }
        return $userInfo;
    }

    /**
     * 验证码验证
     * @param string $username 用户名
     * @param int $vcode 验证码
     * @return bool true|false 验证码正确|验证码错误
     */
    private function checkVcode( $username , $vcode){
        return true;
    }

    /**
     * 用户修改密码
     * @param int $username 用户名（手机号）
     * @param string $password 密码
     * @return bool true|false
     */
    public function fixPassword($username,$password){
        /*检测用户是否存在*/
        if($this->_dao->checkExists(trim($username))){
            $this->code = 217;
            return false;
        }
        $password =Common_Func::password_generate($password);
        /*写入用户数据*/
        if( !$this->_dao->fixPassword($username,$password)){
            $this->code = $this->_dao->code();
            return false;
        }
        return true;
    }
}
