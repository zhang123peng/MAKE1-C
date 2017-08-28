<?php
/**
 * @name MsgController
 * @author Bill Zhang
 * @desc 短信模块 【短信发送】
 * @see blog.make3.cn
 */
include_once(dirname(__FILE__)."/../../vendor/autoload.php");
class MsgController extends Yaf_Controller_Abstract{
    /**
     * 发送验证码
     * @param int $phone
     * @return bool true | fasle 发送成功|发送失败
     */
    public function sendVerifAction($phone = ''){
        $type = $this->getRequest()->get("type","register");
        $phone = $this->getRequest()->getPost("username",false);
        /*验证参数*/
        if(!$phone){
            echo Common_Request::response(201);
            return false;
        }
        if(!Common_Func::checkPhone($phone)){
            echo Common_Request::response(202);
            return false;
        }
        if(!Common_Func::checkSendType($type)){
            echo Common_Request::response(215);
            return false;
        }
        /*检查是否已经发送过*/
        $verifR=(new Predis\Client())->get($type."-".$phone);
        /*验证码生成*/
        $verif = $verifR?$verifR:Common_Func::getVerif();
        $verif = 111111;
        /*发送动作*/
        $model = new MsgModel();
        if(!$model->send(intval($phone),$verif,$type)){
            echo Common_Request::response($model->code);
        }else{
            /*写入redis*/
            $redis = new Predis\Client();
            $redis->setex($type."-".$phone,3600,$verif);
            echo Common_Request::response(100,array("username"=>$phone));
        }
        return false;
    }
}