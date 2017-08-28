<?php
/**
 * 数据库操作DAO
 * @author Bill Zhang
 * @desc 日志的DAO抽离
 * @see blog.make3.cn
 */
class Db_Logs extends Db_Base{
    /**
     * 日志记录添加
     * @param int $phone 手机号码
     * @param int $verif 验证码
     * @param string $ip 请求地址
     * @return bool true|false
     */
    public function add($code ,$uid, $description , $requestId,$response,$ip){
        $query = self::getDb()->prepare(
            "insert into `mc_logs` (`requestid`,`uid`,`code`,`description`,`response`,`ip`) VALUES  (?,?,?,?,?,?)"
        );
        if(!$query->execute(array($requestId,$uid,$code,$description,json_encode($response,$ip)))){
            self::$code = 105;
            return false;
        }
        return true;
    }
}