<?php
/**
 * @name Base模块供Controllers
 * 公共部分使用
 * @author Bill Zhang
 * @desc 产品模块，增加，删除，查询，修改
 * @see blog.make3.cn
 */
class Common_Base extends Yaf_Controller_Abstract{
    public $uid;
    public function init(){
        /*验证机制*/
        $submit = $this->getRequest()->getQuery("submit","0");
        if($submit != 1){
            die(json_encode(Err_Map::get(1000)));
        }
        //======================================================登录状态检测
        /*接收参数*/
        $timestamp = $this->getRequest()->getServer("HTTP_TIMESTAMP","0");
        $sign = $this->getRequest()->getServer("HTTP_SIGN","0");
        $token = $this->getRequest()->getServer("HTTP_TOKEN","0");
        /*验证参数*/
        if(!$timestamp || !$sign || !$token){
           die(json_encode(Err_Map::get(2004)));
        }
        if(!$uid = Verify_Power::getUid(trim($sign),trim($token),intval($timestamp))){
            die(Common_Request::response(Verify_Power::$errno,Verify_Power::$errmsg)) ;
        }
        $this->uid = $uid;
        //======================================================登录状态监检测
    }
}