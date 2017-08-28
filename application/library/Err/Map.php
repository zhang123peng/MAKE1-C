<?php
/**
 * 错误编码类
 * @author Bill Zhang
 * @desc 错误代码与错误提示
 * @see blog.make3.cn
 */
class Err_Map{
    private static $ERRMAP = array(
        //公共类
        100  => "请求成功",
        101  => "请求被禁止",
        102  => "未知错误",
        103  => "token,timestamp,sign 不能为空",
        104  => "无效签名",
        105  => "新增数据失败",
        106  => "读取数据失败",
        107  => "更新数据失败",
        108  => "删除数据失败",
        109  => "数据库连接失败",
        110  => "该接口已经过期",
        111  => "token无效",
        112  => "数据库语句错误",

        //用户类
        201  => '用户名必须',
        202  => '手机号码不正确',
        203  => '用户已存在',
        204  => '该手机未注册',
        205  => '密码必须',
        206  => '密码长度必须6~20位',
        207  => '无效密码',
        208  => '新密码长度必须6~20位',
        209  => '新密码和原密码不能相同',
        210  => '该用户已经被禁用',
        211  => '验证码必须',
        212  => '验证码不正确',
        213  => 'openid不能为空',
        214  => '手机mac不能为空',
        215  => 'type错误',
        216  => '没有该性别',
        217  => '用户不存在',

        //设备类
        301 => '设备MAC不能为空',
        302 => '设备别名不能为空',
        303 => '设备同步失败',
        303 => '设备备注不能为空',
        304 => '设备添加失败',
        305 => 'appid不能为空',
        306 => 'appsecret不能为空',
        307 => 'token存入redis失败',
        308 => 'appid错误',
        309 => 'appsecret错误',
        310 => 'token设置错误',
        311 => '请从新获取token',
        312 => '设备ID不存在',
        313 => '修改设备信息失败',
        314 => '设备已经被添加',
        315 => '数据表操作失败',
        316 => '没有更多数据了',
        317 => 'timestamp不能为空',
        318 => '设备ID不能为空',
        319 => '设备不存在',
        320 => 'cid不能为空',



    );

    /**
     * 获取错误提示
     * @param int $code 错误编码
     * @return array
     */
    public static function get( $code ){
        if(isset(self::$ERRMAP[$code])){
            return self::$ERRMAP[$code];
        }
        return "undefined this error number";
    }
}