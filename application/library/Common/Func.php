<?php
/**
 * 公共方法抽离
 * @author Bill Zhang
 * @desc 公共方法抽离 【密码加密】【APPID生成】
 * 【appsecret生成】
 * @see blog.make3.cn
 */
include_once(dirname(__FILE__)."/../../../vendor/autoload.php");
class Common_Func{
    /**
     * APPID生成
     * @param int $uid 用户id
     * @return string $appid 返回APPID
     */
    public static function getAppid( $uid ){
        return md5(time().$uid."appid".rand(1000,9999));
    }

    /**
     * appsecret 生成
     * @param int $uid 用户id
     * @return string $appsecret 返回appsecret
     */
    public static function getAppsecret( $uid ){
        return md5(time().$uid."appsecret".rand(1000,9999));
    }

    /**
     * 密码加密
     * @param string $password 用户密码
     * @return string $password 加密后的密码
     */
    public static function password_generate( $password ){
        $password = md5("salt-xxxxxxxx".$password);
        return $password;
    }

    /**
     * 验证是否是手机号
     * @param int $phone 手机号
     * @return bool true是手机号|不是手机号
     */
    public static function checkPhone($phone){
        if(preg_match("/^1[34578]{1}\d{9}$/",intval($phone))){
            return true;
        }
        return false;
    }

    /**
     * php正则验证密码规则
     * 只允许 数字、字母、下划线
     * 最短6位、最长24位
     * @param string $password 密码
     * @return bool true|false
     */
    public static function checkPassword($password) {
        if (preg_match('/^[_0-9a-z]{6,20}$/i',trim($password))){
            return true;
        }
        return false;
    }
    /**
     * 检查性别是否传输正确
     * @param int $gender 性别
     * @return bool true
     */
    public static function checkGender($gender){
        $genderArr = array('boy','girl');
        if(in_array($gender,$genderArr)){
            return true;
        }
        return false;
    }

    /**
     * 检查发送短信的类型type是否传输正确
     * @param int $type 性别
     * @return bool true
     */
    public static function checkSendType($type){
        $typeArr = array('register','forgotpassword');
        if($type && in_array($type,$typeArr)){
            return true;
        }
        return false;
    }

    /**
     * 生成6位数的验证码
     * @param int $long 长度
     * @return int 验证码
     */
    public static function getVerif($length=6){
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
    /**
     * 检查验证码是否正确
     * @param int $username 用户名（手机号）
     * @param int $verif 验证码
     * @param string $type 验证方式（register|forgotpassword）
     * @return int 验证码
     */
    public static function checkVerif($username,$verif,$type){
        $redis = new Predis\Client();
        $verifR=$redis->get($type."-".$username);
        if(!$verifR){
            return false;
        }
        if($verifR!=$verif){
            return false;
        }
        /*删除redis里面的验证码*/
        $redis->del(array($type."-".$username));
        return true;
    }


    /**
     * 获取请求的IP地址
     * @return string $ip IP地址
     */
    public static function getIp(){
        $user_IP = isset($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
        return $user_IP;
    }


}