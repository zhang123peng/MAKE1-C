<?php
/**
 * @name UserController
 * @author Bill Zhang
 * @desc 用户模块:注册，登录，修改密码
 * @see blog.make3.cn
 */
include_once(dirname(__FILE__)."/../../vendor/autoload.php");
class UserController extends Yaf_Controller_Abstract {
	private $type;
    public function init(){
		/*方式*/
		$this->type = $this->getRequest()->get("type","username");
		/*验证机制*/
		$submit = $this->getRequest()->getQuery("submit","0");
		if($submit != 1){
			die(Common_Request::response(101));
		}
	}

	/**
	 * 用户注册接口
	 * 验证码验证
	 * @name register
	 * @param string $username 用户名（用户的手机号或者邮箱）
	 * @param string $password  用户密码（大于8位数）
	 * @param int $vcode  验证码（4位数字）
	 * @return mix | $username 失败则返回错误代码和错误信息，成功则返回用户名
	 */
	public function registerAction(){
		/*获取参数*/
		$username = $this->getRequest()->getPost("username",false);
		$password = $this->getRequest()->getPost("password",false);
		$vcode    = $this->getRequest()->getPost("vcode",false);
		$mac      = $this->getRequest()->getPost("mac","");
		$figureurl= $this->getRequest()->getPost("figureurl","");
		$openid   = $this->getRequest()->getPost("openid","");
		$gender   = $this->getRequest()->getPost("gender","boy");
		$country  = $this->getRequest()->getPost("country","");
		$province = $this->getRequest()->getPost("province","");
		$city     = $this->getRequest()->getPost("city","");

		/*参数验证*/
		switch($this->type){
			case "username":
				$nickname = $this->getRequest()->getPost("nickname",$username);
				break;
			case "qq":
				if(!$openid){
					echo Common_Request::response(213);
					return false;
				};
				$nickname = $this->getRequest()->getPost("nickname","美客玩用户");
				break;
			case "weichat":
				if(!$openid){
					echo Common_Request::response(213);
					return false;
				};
				$nickname = $this->getRequest()->getPost("nickname","美客玩用户");
				break;
			default:
				echo Common_Request::response(215);
				return false;
		}
		if(!$username){
			echo Common_Request::response(201);
			return false;
		}
		if(!Common_Func::checkPhone($username)){
			echo Common_Request::response(202);
			return false;
		}
		if(!$password){
			echo Common_Request::response(205);
			return false;
		}
		if(!Common_Func::checkPassword($password)){
			echo Common_Request::response(206);
			return false;
		}
		if(!$vcode){
			echo  Common_Request::response(211);
			return false;
		}
		//验证码验证
		if(!Common_Func::checkVerif($username,$vcode,"register")){
			echo  Common_Request::response(212);
			return false;
		};
		if(!$mac){
			echo Common_Request::response(214);
			return false;
		}
		if(!Common_Func::checkGender($gender)){
			echo Common_Request::response(216);
			return false;
		}
		/*调用Model*/
		$model = new UserModel();
		if($model->register(
				intval($username),trim($password),trim($mac),trim($openid),trim($this->type),
				trim($nickname),trim($figureurl),trim($gender),trim($country),
				trim($province),trim($city)
		)){
			$this->loginAction(intval($username),trim($password));
		}else{
			echo Common_Request::response($model->code);
		}
		return false;
	}


	/**
	 * 用户登录
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return mix | $username  登录失败返回错误信息，成功返回username
	 */
	public function loginAction( $username = '',$password = ''){
		/*获取参数*/
		$type     = $this->type;

		switch($type){
			case "username";
				$username = $username?$username:$this->getRequest()->getPost("username",false);
				$password = $password?$password:$this->getRequest()->getPost("password",false);
				/*参数验证*/
				if(!$username){
					echo Common_Request::response(201);
					return false;
				}
				if(!Common_Func::checkPhone($username)){
					echo Common_Request::response(202);
					return false;
				}
				if(!$password){
					echo Common_Request::response(205);
					return false;
				}
				if(!Common_Func::checkPassword($password)){
					echo Common_Request::response(206);
					return false;
				}
				/*调用Model*/
				$model = new UserModel();
				$uid   = $model->login(trim($username),trim($password));
				continue;
			case "qq";
				$openid = $this->getRequest()->getPost("openid",false);
				if(!$openid){
					echo Common_Request::response(213);
					return false;
				};
				/*调用Model*/
				$model = new UserModel();
				/*调用Model*/
				$model = new UserModel();
				if($userInfo   = $model->thirdLogin(trim($openid),trim($type))){
					$uid = $userInfo['uid'];
					$username = $userInfo['username'];
				}else{
					echo Common_Request::response($model->code);
					return false;
				}
				break;
			case "wechat":
				$openid = $this->getRequest()->getPost("openid",false);
				if(!$openid){
					echo Common_Request::response(213);
					return false;
				};
				/*调用Model*/
				$model = new UserModel();
				if($userInfo   = $model->thirdLogin(trim($openid),trim($type))){
					$uid = $userInfo['uid'];
					$username = $userInfo['username'];
				}else{
					echo Common_Request::response($model->code);
					return false;
				}
				break;
			default:
				echo Common_Request::response(215);
				return false;
		}
		if($uid){
			/*利用redis种token*/
			$redis = new Predis\Client();
			//检查是否已经登录了
			if($redisKey = $redis->keys('make1'.$uid."*")){
				foreach($redisKey as $v){
					if($redis->get($v)==$uid){
						echo Common_Request::response(100,array("uid"=>$uid,"username"=>$username,"token"=>$v));
						return false;
					}
				}
			}else{
				$token =  'make1'.$uid.md5("make1-c-login".time().$uid);//作为redisKey
				$redis->set($token , $uid);
				echo Common_Request::response(100,array("uid"=>$uid,"username"=>$username,"token"=>$token));
			}
		}else{
			echo Common_Request::response($model->code);
		}
		return false;
	}


	/**
	 * 忘记密码
	 * 查找是否有该用户，若有验证手机号是否有
	 * @username string $username 用户名(手机号码)
	 * @return bool trun|false 修改成功|修改失败
	 */
     public function forgotPasswordAction(){
		 /*获取参数*/
		 $username = $this->getRequest()->getPost("username",false);
		 $vcode = $this->getRequest()->getPost("vcode",false);
		 $password = $this->getRequest()->getPost("password",false);
		 /*参数验证*/
		 if(!$username){
			 echo Common_Request::response(201);
			 return false;
		 }
		 if(!Common_Func::checkPhone($username)){
			 echo Common_Request::response(202);
			 return false;
		 }
		 if(!$password){
			 echo Common_Request::response(205);
			 return false;
		 }
		 if(!Common_Func::checkPassword($password)){
			 echo Common_Request::response(206);
			 return false;
		 }
		 if(!$vcode){
			 echo  Common_Request::response(211);
			 return false;
		 }
		 //验证码验证
		 if(!Common_Func::checkVerif($username,$vcode,"forgotpassword")){
			 echo  Common_Request::response(212);
			 return false;
		 };
		 //找回密码操作
		 $model = new UserModel();
		 if($model->fixPassword($username,$password)){
			 echo Common_Request::response(100);
		 }else{
			 echo Common_Request::response($model->code,array('username'=>$username));
		 }
		 return false;
	 }

	/**
	 * 获取用户所有信息
	 */
	public function getAction(){
		//======================================================
		/*接收参数*/
		$timestamp = $this->getRequest()->getServer("HTTP_TIMESTAMP","0");
		$sign = $this->getRequest()->getServer("HTTP_SIGN","0");
		$token = $this->getRequest()->getServer("HTTP_TOKEN","0");
		/*验证参数*/
		if(!$timestamp || !$sign || !$token){
			die(Common_Request::response(103));
		}
		if(!$uId = Verify_Power::getUserId(trim($sign),trim($token),intval($timestamp))){
			die(Common_Request::response(Verify_Power::$code));
		}
		//======================================================
		$model = new UserModel();
		if(!$userInfo = $model->getUserInfo($uId)){
			echo Common_Request::response($model->code);
		}else{
			echo Common_Request::response(100,$userInfo);
		}
		return false;
	}

	/**
	 * 用户退出
	 */
	public function signOutAction(){
		//======================================================
		/*接收参数*/
		$timestamp = $this->getRequest()->getServer("HTTP_TIMESTAMP","0");
		$sign = $this->getRequest()->getServer("HTTP_SIGN","0");
		$token = $this->getRequest()->getServer("HTTP_TOKEN","0");
		/*验证参数*/
		if(!$timestamp || !$sign || !$token){
			die(Common_Request::response(103));
		}
		if(!$uId = Verify_Power::getUserId(trim($sign),trim($token),intval($timestamp))){
			die(Common_Request::response(Verify_Power::$code));
		}
		//======================================================
		$redis = new Predis\Client();
		if(!$redis->del($token)){
			echo Common_Request::response(102);
		}else{
			echo Common_Request::response(100);
		}
		return false;
	}
}
