<?php
/**
 * 数据库操作DAO
 * @author Bill Zhang
 * @desc 意见反馈的DAO抽离
 * @see blog.make3.cn
 */
class Db_Feedback extends Db_Base{

    /**
     * 添加建议
     * @param string $content 建议内容
     * @param string $email 邮件地址
     * @param string $ip IP地址
     * @param int $uid 用户id
     * @return bool true|false
     */
    public function add($content , $email , $ip , $uid){
        $query = self::getDb()->prepare(
            "insert into `mc_feedback` (`content`,`email`,`ip`,`uid`) VALUES (?,?,?,?)"
        );
        if(!$query->execute(array($content,$email,$ip,$uid))){
            self::$code = 105;
            return false;
        }
        return true;
    }
}