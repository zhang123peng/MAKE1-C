<?php
/**
 * @name EquipmentController
 * @author Bill Zhang
 * @desc 设备模块【设备添加，删除，列表查询，详情查询，修改】
 * @see blog.make3.cn
 */
class EquipmentController extends Common_Ebase{
    /**
     * 设备添加
     * @param string $mac 设备mac
     * @param string $autherName 设备别名
     * @param string $remarks 设备备注
     */
    public function addAction(){
        $uid         = $this->uid;
        $mac         = $this->getRequest()->getPost("mac",false);
        $anotherName = $this->getRequest()->getPost("another_name",$uid);
        $remarks     = $this->getRequest()->getPost("remarks",$mac);
        $cid         = $this->getRequest()->getPost("cid",$anotherName);
        /*验证数据*/
        if(!$mac){
            echo Common_Request::response(301);
            return false;
        }
        if(!$cid){
            echo Common_Request::response(320);
            return false;
        }
        $ThirdParty_Iot= new  ThirdParty_Iot();
        $addEquipment=$ThirdParty_Iot->addEquipment($mac,$anotherName,$cid,$remarks);
       if($addEquipment['data']['code']>300){
           echo Common_Request::response($addEquipment['data']['code']);
           return false;
       }
        $equipmentId=$addEquipment['data']['id'];
        if(!$equipmentId){
            echo Common_Request::response(301);
            return false;
        }
        /*调用model*/
        $model = new EquipmentModel();
        if($model->add(intval($equipmentId),trim($uid))){
            echo Common_Request::response(100,array(['eid'=>$equipmentId]));
        }else{
            echo Common_Request::response($model->code);
        }
        return false;
    }

    /**
     * 设备编辑
     * @param string $remarks 设备备注
     * @return bool true 修改成功|修改失败
     */
    public function editAction(){
        /*接收参数*/
        $equipmentId = $this->getRequest()->get('id',false);
        $remarks = $this->getRequest()->getPost('remarks',false);
        /*验证参数*/
        if(!$equipmentId){
            echo Common_Request::response(312);
            return false;
        }
        $model = new EquipmentModel();
        if($model->edit($equipmentId,$remarks,$this->uid)){
            echo Common_Request::response(100);
        }else{
            echo Common_Request::response($model->code);
        }
        return false;
    }

    /**
     * 获取设备列表
     * @param int $pageNo 页码
     * @param int $pageSize 每页个数
     * @return array $data 返回设备数组
     */
    public function listAction(){
        /*接收参数*/
        $pageNo = $this->getRequest()->get("pageNo","0");
        $pageSize = $this->getRequest()->get("pageSize","10");
        $model = new EquipmentModel();
        if($data = $model->list( $pageNo , $pageSize, $this->uid)){
            echo Common_Request::response(0,$data);
        }else{
            echo Common_Request::response($model->code);
        }
        return FALSE;
    }

    /**
     * 获取详情
     * @param int $id 设备ID
     *
     */
    public function getAction(){
        /*接收参数*/
        $equimentId = $this->getRequest()->get("id",false);
        /*验证数据*/
        if(!$equimentId){
            echo Common_Request::response(312);
            return false;
        }
        $model = new EquipmentModel();
        if($data = $model->get($equimentId , $this->uid)){
            echo Common_Request::response(100,$data);
        }else{
            echo Common_Request::response($model->code);
        }
        return false;
    }

    /**
     * 设备删除
     * @param int $id 设备ID
     */
    public function delAction(){
        /*接收参数*/
        $equimentId = $this->getRequest()->get("id",false);
        if(!$equimentId){
            echo Common_Request::response(312);
            return false;
        }
        $model = new EquipmentModel();
        if($model->del($equimentId , $this->uid)){
            echo Common_Request::response(100);
        }else{
            echo Common_Request::response($model->code);
        }
        return false;
    }

}