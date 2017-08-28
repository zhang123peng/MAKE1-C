<?php
/**
 * @name EquipmentModel
 * @desc 设备Model
 * @author Bill Zhang
 * @see blog.make3.cn
 */
class EquipmentModel extends BaseModel{

    /**
     * 数据库连接池
     */
    public function __construct()
    {
        $this->_dao = new Db_Equipment();
    }
    /**
     * 设备添加
     *@param string $equipmentId 设备id
     * @param int $uid 用户id
     * @return bool ture | false
     */
    public function add( $equipmentId ,$uid){
        /*查询设备是否已经被添加*/
        if($this->_dao->checkExists($uid,$equipmentId)){
            $this->code = $this->_dao->code();
            return false;
        }
        /*写入数据库*/
        if(!$this->_dao->add($uid,$equipmentId)){
            $this->code = $this->_dao->code();
            return false;
        }
        return true;
    }

    /**
     * 设备编辑
     * @param int $equipmentId 设备ID
     * @param string $uid 设备id
     * @param string $remarks 设备备注
     * @return bool true 修改成功|修改失败
     */
    public function edit($equipmentId,$remarks,$uid){
        /*查询设备是否已经被添加*/
        if(!$this->_dao->checkExists($uid,$equipmentId)){
            $this->code = $this->_dao->code();
            return false;
        }
        $ThirdParty_Iot= new  ThirdParty_Iot();
        $addEquipment=$ThirdParty_Iot->editEquiment($equipmentId,$remarks);
        if($addEquipment['data']['code']>300){
            $this->code = $addEquipment['data']['code'];
            return false;
        }
        if(!isset($addEquipment['data'])){
            $this->code = $addEquipment['data']['code'];
        }
        return true;
    }


    /**
     *list 获取设备列表
     * @param int $pageNo 页码
     * @param int $pageSize 每页个数
     * @return array $data 返回设备数组
     */
    public function list( $pageNo =0  ,$pageSize = 10,$uid){
        if($pageNo>0){$pageNo=$pageNo-1;}
        $start = $pageNo * $pageSize + ($pageNo==0?0:1);
        /*获取该产品下所有设备*/
        if(!$data = $this->_dao->list($start,$pageSize,$uid))
        {
            $this->code = $this->_dao->code();
            return false;
        }
        $dataA = array();
        foreach($data as $v){
            $ThirdParty_Iot= new  ThirdParty_Iot();
            $dataE=$ThirdParty_Iot->getEquiment($v['equipment_id']);
            if($dataE['data']['code']>300){
                if($dataE['data']['code']==316){
                    $dataA[] = $dataE['data'];
                }else{
                    $this->code = $dataE['data']['code'];
                    return false;
                }
            }else{
                $dataA[] = $dataE['data'];
            }
        }
        return $dataA;
    }

    /**
     * 获取设备详情
     * @param int $equimentId 设备ID
     * @return array $data 返回设备详情
     */
    public function get( $equimentId = 0, $uid){
        /*查询是否有该产品*/
        if(!$this->_dao->checkExists($uid,$equimentId)){
            $this->code = $this->_dao->code();
            return false;
        }
        $ThirdParty_Iot= new  ThirdParty_Iot();
        $addEquipment=$ThirdParty_Iot->getEquiment($equimentId);
        if($addEquipment['data']['code']>300){
            $this->code = $addEquipment['data']['code'];
            return false;
        }
        if(!isset($addEquipment['data'])){
            $this->code = $addEquipment['data']['code'];
        }
        return $addEquipment['data'];
    }

    /**
     * del 设备删除
     * @param int $equimentId 设备ID
     * @param int $uid 用户id
     * @return bool 删除成功|删除失败
     */
    public function del( $equimentId = 0,$uid =0){
        /*查询是否有该产品*/
        if(!$this->_dao->checkExists($uid,$equimentId)){
            $this->code = $this->_dao->code();
            return false;
        }
        if(!$this->_dao->del($equimentId,$uid)){
            $this->code = $this->_dao->code();
            return false;
        }
        return true;
    }
}