<?php
/**
 * @name Common_Ebase模块供Controllers
 * 公共部分使用
 * @author Bill Zhang
 * @desc 设备公共模块
 * @see blog.make3.cn
 */
class Common_Ebase extends Yaf_Controller_Abstract{
    public $uid;
    public function init(){
        /*验证机制*/
        $submit = $this->getRequest()->getQuery("submit","0");
        if($submit != 1){
            die(Common_Request::response(101));
        }
        //======================================================
        /*接收参数*/
        $timestamp = $this->getRequest()->getServer("HTTP_TIMESTAMP","0");
        $sign = $this->getRequest()->getServer("HTTP_SIGN","0");
        $token = $this->getRequest()->getServer("HTTP_TOKEN","0");
        /*验证参数*/
        if(!$timestamp || !$sign || !$token){
            die(Common_Request::response(103));
        }
        if(!$uId = Verify_Power::getUserId(trim($sign),trim($token),intval($timestamp))){
            die(Common_Request::response(Verify_Power::$code));
        }
        $this->uid = $uId;
        //======================================================
    }
}