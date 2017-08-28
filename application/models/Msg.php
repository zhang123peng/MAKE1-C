<?php
/**
 * @name MsgModel
 * @desc 短信发送Model
 * @author Bill Zhang
 * @see blog.make3.cn
 */
class MsgModel  extends BaseModel{
    /**
     * 数据库连接池
     */
    public function __construct()
    {
        $this->_dao = new Db_Msg();
    }

    /**
     * 短信发送操作
     * @param int $phone 手机号码
     * @param int $verif 验证码
     * @param int $type 使用方式
     * @return bool true|false 发送成功|发送失败
     */
    public function send($phone , $verif ,$type="register"){
        /*检查是否已经过了60秒*/
        /*if(!$this->_dao->checkOut($phone,$verif,$type)){
            $this->code = $this->_dao->code();
            return false;
        }*/
        switch($type){
            case "register":
                /*检查用户是否已经注册*/
                if(!$this->_dao->checkExists($phone)){
                    $this->code = $this->_dao->code();
                    return false;
                }
                break;
            case "forgotpassword":
                /*检查用户是否已经注册*/
                if($this->_dao->checkExists($phone)){
                    $this->code = 217;
                    return false;
                }
                break;
        }
        /*调用短信发送类发送短信*/
        if(!true){
           $this->code = 102;
            return false;
        }
        /*发送记录写入数据库*/
        $ip = Common_Func::getIp();
        if(!$this->_dao->add($phone,$verif,$ip,$type)){
            $this->code = $this->_dao->code();
            return false;
        }
        return true;
    }
}
