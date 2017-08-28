<?php
/**
 * 数据库操作DAO
 * @author Bill Zhang
 * @desc DAO的公共部分
 * @see blog.make3.cn
 */
class Db_Base{
    public static $code = 0;
    public static $db = null;

    /**
     * 数据库连接
     * 通过PDO进行数据库连接
     * @return object null|PDO 返回一个PDO对象
     */
    public function getDb(){
        if(self::$db == null){
            self::$db = new PDO("mysql:host=127.0.0.1;dbname=make1-c","root","root");
            self::$db->query("SET NAMES utf8");
            /**
             * 不设置下面这一行的话，PDO会在拼SQL时候，把int 0 转成 string 0
             */
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

            self::$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);//设置警告模式错误处理报告，开发完成后关闭
        }
        return self::$db;
    }

    /**
     * 获取错误code
     * @return int 错误的code
     */
    public function code(){
        return self::$code;
    }


}