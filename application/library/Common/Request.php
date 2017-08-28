<?php

/**
 * 公共请求方法
 * User: Bill Zhang
 * Date: 2017/8/9
 * Time: 16:57
 */
class Common_Request
{
    /**
     * 数据返回
     * 编码成json字符串
     *
     * @param int $code 错误code
     * @param string $description 状态码描述
     * @param array $response  响应的结果集
     * @return string json化后的数据
     */
     public static function response($code , $response = array()){
         $arr = array(
           "code" => $code,
           "description" => Err_Map::get($code),
           "timestamp"=>time(),
           "requestId"=> "b438f340-d73b-a77a-cde8-629a77cd"
         );
         if(count($response)!==0){
             $arr['response'] = $response;
         }
         return json_encode($arr);
     }
}