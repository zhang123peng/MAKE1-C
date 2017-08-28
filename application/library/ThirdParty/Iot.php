<?php
include_once(dirname(__FILE__)."/../../../vendor/autoload.php");
class ThirdParty_Iot {
	/**
	* 请求地址
	*/
	const API_URL = 'develop.eeioe.com';

	/**
	* appid
	* 
	* @var string
	*/
	protected $appid;

	/**
	* appsecret
	* 
	* @var string
	*/
	protected $appsecret;

	/**
	*时间戳
	* @var string
	*/
	protected $timestamp;

	/**
	 * 发送的数据集合
	 *
	 */
	protected $equipmentParams;

	/**
	 * token
	 */
	protected $token;

	/**
	 * 接收结果
	 */
	protected $resultMsg;

	/**
	 * 地址接收
	 */
	protected $apiURL;

	public $code;

	protected $errno=array(
			3001 => '301',
			3002 => '302',
			3003 => '303',
			3004 => '304',
			3005 => '305',
			3006 => '306',
			3007 => '307',
			3008 => '308',
			3009 => '309',
			3010 => '310',
			3011 => '311',
			3012 => '312',
			3013 => '313',
			3014 => '314',
			3015 => '315',
			3016 => '316',
			3017 => '317',
			3018 => '318',
			3019 => '319',
			3020 => '320'
	);
	/**
	* 构造方法
	* 
	* @param string $appid 产品APPID
	* @param string $appsecret 产品APPSECRET
	*/
    public function __construct($appid = '', $appsecret = '')
    {
		//用户和密码可直接写在类里
		$def_appid = '912e1602f20b9de1eae900ccbe149845';
		$def_appsecret = 'e51412af11447c8929f0d39fce1cbec5';
        $this->appid	= $def_appid;
        $this->appsecret	= $def_appsecret;
		$this->timestamp  = time();
        $this->apiURL = self::API_URL;
		$this->token  = $this->getToken();
    }
	/**
	* 公共参数
	* @return array 
	*/
    protected function publicParams()
    {
        return array(
            'appid'		=> $this->appid,
            'appsecret'	=> $this->appsecret,
			'timestamp'   => $this->timestamp
        );
    }
	/**
	 * 获取token
	 */
	protected function getToken(){
		$curl = new \Curl\Curl();
		$ret  = $curl->post(self::API_URL."/etoken/get?submit=1",$this->publicParams());
		$ret  = $ret->response;
		$arr  = $this->json_to_array($ret,true);
		if($arr['errno']<0){
			return false;
		}else{
			return  $arr['data']['token'];
		}
	}

	/**
	* 添加设备
	*
	* @param string $mac 设备mac
	* @param string $anotherName 设备别名（手机号或者用户id）
	* @param string $cid 客户端id
	* @param string $remarks 短信模板ID
	* @return array
	*/
	public function addEquipment($mac, $anotherName,$cid,$remarks='') {
		//设备参数
		$this->equipmentParams = array(
			'mac'		        => $mac,
			'another_name'	=> $anotherName,
			'cid'	            => $cid,
			'remarks'       	=> $remarks
		);
		$this->apiURL = self::API_URL."/equipment/add?submit=1";
		return $this->request();
	}

	/**
	 * 删除设备
	 * @param string $eid 设备id
	 * @return bool true|false
	 */
	public function delEquiment($eid){
		$this->equipmentParams = array();
		$this->apiURL = self::API_URL."/equipment/del?submit=1&id=".$eid;
		return $this->request();
	}

	/**
	 * 设备详情
	 * @param string $eid 设备id
	 * @return array
	 */
	public function getEquiment($eid){
		$this->equipmentParams = array();
		$this->apiURL = self::API_URL."/equipment/get?submit=1&id=".$eid;
		return $this->request();
	}

	/**
	 * 设备修改
	 * @param string $eid 设备id
	 * @param string $remarks 设备备注
	 * @return bool true|false
	 */
	public function editEquiment($eid,$remarks){
		$this->equipmentParams = array(
			'remarks'=>$remarks
		);
		$this->apiURL = self::API_URL."/equipment/edit?submit=1&id=".$eid;
		return $this->request();
	}

	/**
	* 发送HTTP请求
	* @return string
	*/
	private function request()
	{
		if(!$this->token){
			return false;
		}
		$sign= md5($this->timestamp.$this->token);
		$curl = new \Curl\Curl();
		$curl->setHeader('token',$this->token);
		$curl->setHeader('timestamp',$this->timestamp);
		$curl->setHeader('sign',$sign);
		$ret  = $curl->post($this->apiURL,$this->equipmentParams);
		$data = $this->json_to_array($ret->response,true);
		if($data['errno']<0){
			$data['data']['code']=$this->errno[(-intval($data['errno']))];
			return $data;
		}else{
			$data['data']['code']=100;
			return $data;
		}
	}

	//把json字符串转数组
	function json_to_array($p)
	{
		if( mb_detect_encoding($p,array('ASCII','UTF-8','GB2312','GBK')) != 'UTF-8' )
		{
			$p = iconv('GBK','UTF-8',$p);
		}
		return json_decode($p, true);
	}



}

?>