<?php

/**
 * 权限token验证
 * @author Bill Zhang
 * @see blog.make3.cn
 */
include_once(dirname(__FILE__)."/../../../vendor/autoload.php");
class Verify_Power
{
    /**
     * 错误代码
     * @var int 编码号
     */
    public static $code = 0;


    /**
     * getUserId 获取用户id
     * @param string $timestamp 11位的时间戳
     * @param string $sign 32位签名
     * @param string $token token验证码
     * @retrun mix bool|uid 查找失败|返回ID
     */
    public static function getUserId($sign ,$token , $timestamp){
        /*检测时间戳是否过期*/

        /*验证sign*/
        if(self::_getSign(trim($token),intval($timestamp))!==trim($sign)){
            self::$code = 104;
            return false;
        }
        /*根据token查询productId*/
        $redis = new Predis\Client();
        $uId = $redis->get($token);
        if(!$uId){
            self::$code = 111;
            return false;
        }
        return intval($uId);
    }

    /**
     * _getSign签名生成
     * @param string $token token
     * @param int $timestamp 时间戳
     * @return string $sign 签名
     */
    private static function _getSign($token ,$timestamp ){
        return strtoupper(md5($timestamp.$token));
    }

    /**
     * 获取错误code
     * @return int 错误的code
     */
    public function errno(){
        return self::$code;
    }

    /**
     * 获取错误信息
     * @return string 返回错误信息
     */
    public function errmsg(){
        return self::$errmsg;
    }

}