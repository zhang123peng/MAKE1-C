<?php
/**
 * @name FeedbackController
 * @author Bill Zhang
 * @desc 意见反馈模块 【意见反馈】
 * @see blog.make3.cn
 */
class FeedbackController extends Yaf_Controller_Abstract{
  /**
   * 意见反馈
   */
    public function commitAction(){
//======================================================
        /*接收参数*/
        $timestamp = $this->getRequest()->getPost("timestamp","0");
        $sign = $this->getRequest()->getPost("sign","0");
        $token = $this->getRequest()->getPost("token","0");
        /*验证参数*/
        if(!$timestamp || !$sign || !$token){
            die(Common_Request::response(103));
        }
        if(!$uId = Verify_Power::getUserId(trim($sign),trim($token),intval($timestamp))){
            die(Common_Request::response(Verify_Power::$code));
        }
        //======================================================

        /*接收参数*/
        $content = $this->getRequest()->getPost("content","");
        $email   = $this->getRequest()->getPost("email","");
        if(!$content){
            echo "<script type='text/javascript'> alert('请填写内容')</script>";
            return false;
        }
        if(!$email){
            echo "<script type='text/javascript'> alert('请填写邮箱')</script>";
            return false;
        }
        /*调用model*/
        $model = new FeedbackModel();
        if(!$model->add(trim($content),$email,$uId)){
//            echo Common_Request::response($model->code);exit;
            echo "<script type='text/javascript'> alert('请求失败');window.history.back(-1); </script>";
        }else{
            $this->getResponse()->setRedirect("http://h5.eeioe.com/feedback/commit.html");
        }
        return false;
    }
}