<?php
//User info class

class user {
	
	private $db;
	
	public $user_info;
	
	public $login_error;
	
	private $valid = false;
	
	public $user_type = PER_USER;
	
	
	//Set DB
	public function __construct($db=false){
		if(!$db){
			$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
			$db->debug = SHOW_SQL_ERROR;
			$db->connect();
		}
		$this->db = $db;
	}
	
	//Check valid login and if login in or returning
	public function valid_login(){

		if(isset($_POST["btnLogin"])) $this->login();
		else $this->check_session();
		
		//Unset all session and cookies if not calid
		if(!$this->valid) {
			unset($_COOKIE['cookname']);	
			unset($_COOKIE['password']);
			unset($_COOKIE['sec_code']);
			unset($_COOKIE['user_groupid']);			
			//foreach($_COOKIE as $key => $val) setcookie($key, '', time()-3600, "");				
			session_unset();
			//session_destroy();
		}
		
		//Return valid true/false
		return $this->valid;
	
	}
	
	
	private function login(){
				
		$db = $this->db;
		
		$sess_code = $_COOKIE['secure_code'];
		unset($_COOKIE['secure_code']);
		
		$post_code = hash("sha512", $_POST['security_code']."34hjhFDSFKj5g&uh34545");
		$remember = isset($_POST["remember"]);
		$username = $_POST["txtUser"];
		$password = $_POST["txtPass"];
	
		
		//$Password=md5(md5($_POST["txtPass"]).$user_salt);
		
		//Check login details		
		//if($sess_code == $post_code && !empty($sess_code)) {
			
			$user=$db->query_first("SELECT * FROM `".TABLE_USERS."` WHERE `user_name` = ?", $username);

			if(empty($user)) {
				$this->login_error = 1;
				return;
			}
			
			//Check password
			if(strlen($user['user_salt'])<100){
				//old login detected, check and upgrade if correct
				if($user['user_pass']!=md5(md5($password).$user['user_salt'])){
					$this->login_error = 1;
					return;
				}
				
				//Correct pass, upgrade to new login alg
				$salt = $this->new_salt();
				$password = $this->password_crypt($password, $salt);
				$data = array(
								"user_pass" => $password,
								"user_salt"	=> $salt,
							  );
				$db->update(TABLE_USERS, $data, '`user_id` = ?', $user['user_id']);
			} 
			//Check with new login alg
			elseif($user['user_pass']!=$this->password_crypt($password, $user['user_salt'])){
				$this->login_error = 1;
				return;
			}
			
			//Check group ids
			if ($user['user_groupid']<=$this->user_type) {
				
				//Checks done, log user in
				session_regenerate_id(true);
				$_SESSION['user_id'] = $user['user_id'];
				$_SESSION['user_name'] = $user['user_name'];
				$_SESSION['hgrty67'] = $this->sec_code();
				$_SESSION['user_groupid'] = $user['user_groupid'];
				if ($remember) {
					setcookie("cookname", $_SESSION['user_name'], time()+60*60*REMEMBER_ME_TIMEOUT, "/");
					setcookie("cookpass", $user['user_pass'], time()+60*60*REMEMBER_ME_TIMEOUT, "/");
					setcookie("hgrty67", $_SESSION['hgrty67'], time()+60*60*REMEMBER_ME_TIMEOUT, "/");
					setcookie("user_groupid", $_SESSION['user_groupid'], time()+60*60*REMEMBER_ME_TIMEOUT, "/");
				}
				
				//Update details
				$data = array(
								"last_activity" => time(),
								"last_logged_in" => time(),
								"sec_code" => $_SESSION['hgrty67'],
								"user_agent" => $_SERVER['HTTP_USER_AGENT']
								);
				$db->update(TABLE_USERS, $data, '`user_id` = ?', $user['user_id']);
				//Success
				$this->login_error = 0; 
				$this->valid=true;
				$this->user_info = $db->query_first("SELECT * FROM `".TABLE_USERS."` WHERE `user_id` = ?", $user['user_id']);
			} else {
				if ($record['user_groupid'] == PER_UNCONFIRM) 
					$this->login_error = 5; //Not confirm email address
				 else
					$this->login_error = 3; //Not have permission
			}
		
		//} else $this->login_error = -1; //Bad code
		
		return;		
	}
	
	//gen sec code
	private function sec_code(){
		
		$sec_code = substr(hash("sha512",hash("sha512", rand(rand(1, 500), time()).'65ghdfg3675hg84hd'.rand(rand(1, 500), time()))), 10, 40);
		
		if($this->db->num_rows("SELECT * FROM `".TABLE_USERS."` WHERE `sec_code` = ?", $sec_code)>0) $sec_code = $this->sec_code();
		
		return $sec_code;	
		
		
	}
	
	//Password encryption
	public function password_crypt($password, $salt){
		return hash("sha512",hash("sha512", $password.$salt.'DAFDf454fdfSRghRWY565'));
	}
	
	public function new_salt(){
		
		$salt = hash("sha512",hash("sha512", rand(rand(1, 500), time()).'DAFDf454fdfSRghRWY565'.rand(rand(1, 500), time())));
		
		if($this->db->num_rows("SELECT * FROM `".TABLE_USERS."` WHERE `user_salt` = ?", $salt)>0) $salt = $this->new_salt();
		
		return $salt;	
		
	}
	
	//Check login session
	private function check_session(){
		
		$db = $this->db;
		
		//Check cookies first
		$username = $_COOKIE["cookname"];
		$password = $_COOKIE["password"];
		$sec_code = $_COOKIE["sec_code"];
		$user_groupid = $_COOKIE["user_groupid"];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if(!empty($username)&&!empty($password)&&!empty($sec_code)&&!empty($user_groupid)){
			
			$user = $db->query_first("SELECT * FROM `".TABLE_USERS."` WHERE `user_name` = ? AND `user_pass` = ? AND `sec_code` = ? AND `user_groupid` = ? AND `user_agent` = ?", array($username, $password, $sec_code, $user_groupid, $user_agent));
			//Check empty and last time frame
			if(!empty($user)){
				session_regenerate_id(true);
				$_SESSION['user_id'] = $user['user_id'];
				$_SESSION['user_name'] = $user['user_name'];
				$_SESSION['hgrty67'] = $this->sec_code();
				$_SESSION['user_groupid'] = $record['user_groupid'];
				
				//Update DB
				//Update details
				$data = array(
								"last_activity" => time(),
								"sec_code" => $_SESSION['hgrty67']
								);
				$db->update(TABLE_USERS, $data, '`user_id` = ?', $user['user_id']);
				$this->valid=true;
				$this->user_info = $db->query_first("SELECT * FROM `".TABLE_USERS."` WHERE `user_id` = ?", $user['user_id']);
				return;
			}
		
		}
		
		//Cookies not set or invalid

		//Check seesion vars
		$username = $_SESSION['user_name'];
		$sec_code = $_SESSION['hgrty67'];
		$user_groupid = $_SESSION['user_groupid'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if(!empty($username)&&!empty($user_agent)&&!empty($sec_code)&&!empty($user_groupid)){
						
			$user = $db->query_first("SELECT * FROM `".TABLE_USERS."` WHERE `user_name` = ? AND `sec_code` = ? AND `user_groupid` = ? AND `user_agent` = ?", array($username, $sec_code, $user_groupid, $user_agent));
			
			//Check empty and last time frame
			if(!empty($user)&&$user['last_activity']+60*60*24>=time()){
				
				//User ok, update time stamps and continue
				$_SESSION['user_id'] = $user['user_id'];	
				$_SESSION['user_name'] = $user['user_name'];
				$_SESSION['hgrty67'] = $this->sec_code();
				$_SESSION['user_groupid'] = $user['user_groupid'];
				
				//Update DB
				//Update details
				$data = array(
								"last_activity" => time(),
								"sec_code" => $_SESSION['hgrty67']
								);
				$db->update(TABLE_USERS, $data, '`user_id` = ?', $user['user_id']);
				$this->user_info = $db->query_first("SELECT * FROM `".TABLE_USERS."` WHERE `user_id` = ?", $user['user_id']);
				$this->valid=true;
			}
		}
		return;
	}
	
	//Register user
	public function register(){
		
		
		
	}
	
	
	
	
	public function logout(){
		foreach($_SESSION as $key => $val) $_SESSION[$key] = '';
		//foreach($_COOKIE as $key => $val) setcookie($key, '', time()-3600, "");
		unset($_COOKIE['cookname']);	
		unset($_COOKIE['cookname']);	
		unset($_COOKIE['password']);
		unset($_COOKIE['sec_code']);
		unset($_COOKIE['user_groupid']);	
		
		session_unset();
		session_destroy();
		
		//Update DB to remove info about login
		$data = array(
						"sec_code" => '',
						"user_agent" => ''
						
					  );
		if(!empty($this->user_info['user_id'])) $this->db->update(TABLE_USERS, $data, '`user_id` = ?', $this->user_info['user_id']);

		return true;		
	}
}
?>