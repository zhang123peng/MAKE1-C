<?php
/**
 * 数据库操作DAO
 * @author Bill Zhang
 * @desc 设备DAO抽离
 * @see blog.make3.cn
 */
class Db_Equipment extends Db_Base{

    /**
     * 查找设备是否已经被添加
     * @param string $uid 用户名
     * @param string $equipmentId 设备ID
     * @return bool true 存在|不存在
     */
    public function checkExists($uid,$equipmentId){
        $query = self::getDb()->query(
            "select count(*) as c from `mc_equipment` where `uid` = ? AND `equipment_id`=?"
        );
        $query->execute(array($uid,$equipmentId));
        $ret = $query->fetch();
        if($ret[0]<=0){
            self::$code = 319;
            return false;
        }
        return true;
    }

    /**
     * 设备添加
     *@param string $equipmentId 设备id
     * @param int $uid 用户id
     * @return bool ture | false
     */
    public function add($uid,$equipmentId){
        /*添加设备*/
        $query = self::getDb()->prepare(
            "INSERT INTO  `mc_equipment` (`uid`,`equipment_id`) VALUE (?,?)"
        );
        $ret = $query->execute(array($uid,$equipmentId));
        if(!$ret){
           self::$code = 105;
        }
        return true;
    }




    /**
     * 获取设备列表
     * @param int $pageNo 页码
     * @param int $pageSize 每页个数
     * @param int $productId 产品ID
     * @return array $data 返回产品equipment_id数组
     */
    public function list( $start =0  ,$pageSize = 10 ,$uid){
        $query =self::getDb()->prepare(
            "SELECT equipment_id FROM  mc_equipment  where uid=? ORDER BY  create_time DESC LIMIT ?,?"
        );
        if(!$query->execute(array($uid,$start,$pageSize))){
           self::$code = 315;
            return false;
        }
        if(!$ret = $query->fetchAll()){
            self::$code = 316;
            return false;
        }
        $data = array();
        $arr = array();
        foreach($ret as $v){
            $arr['equipment_id'] = $v['equipment_id'];
            $data[]=$arr;
        }
        return $data;
    }

    /**
     * 删除设备
     * @param int $equipmentId 设备id
     * @param int $uid 用户id
     * @return bool 删除成功|删除失败
     */
    public function del($equipmentId,$uid){
        try{
            self::getDb()->beginTransaction();
            $ret = self::getDb()->prepare("DELETE FROM `mc_equipment` WHERE `equipment_id`=? AND `uid`=?");
            if(!$ret->execute(array($equipmentId,$uid))){
                throw new PDOException(102);
            }
            $ThirdParty_Iot= new  ThirdParty_Iot();
            $addEquipment=$ThirdParty_Iot->delEquiment($equipmentId);
            if($addEquipment['data']['code']>300){
                throw new PDOException($addEquipment['data']['code']);
            }
            if(!isset($addEquipment['data'])){
                throw new PDOException($addEquipment['data']['code']);
            }
            self::getDb()->commit();
            return true;
        }catch (Exception $e){
            self::getDb()->rollBack();
            self::$code = $e->getMessage();
            return false;
        }
    }
}