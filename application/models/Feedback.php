<?php
/**
 * @name FeedbackModel
 * @desc 意见反馈Model
 * @author Bill Zhang
 * @see blog.make3.cn
 */
class FeedbackModel  extends BaseModel{
    /**
     * 数据库连接池
     */
    public function __construct()
    {
        $this->_dao = new Db_Feedback();
    }

    /**
     * 添加建议
     * @param string $content 建议类容
     * @param string $email 邮件地址
     * @param int  $uid 用户id
     * @return bool true|false
     */
    public function add($content , $email , $uid){
        /*获取ip地址*/
        $ip = Common_Func::getIp();
        if(!$this->_dao->add($content , $email , $ip , $uid))
        {
            $this->code = $this->_dao->code();
            return false;
        }
        return true;
    }


}
