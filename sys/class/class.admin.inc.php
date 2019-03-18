<?php

/**
 * 
 */
class Admin extends DB_Connect{
	//密码盐长度
	private $saltLength = 7;

	//连接数据库
	public function __construct($dbo=NULL,$saltLength=NULL){
		parent::__construct($dbo);
		if (is_int($saltLength)) {
			$this->saltLength = $saltLength;
		}
	}

	public function processLogin(){
		if ($_POST['action']!='user_login') {
			return '登陆错误';
		}
		//转义输入数据
		$user_name = htmlentities($_POST['user_name'],ENT_QUOTES);
		$user_pass = htmlentities($_POST['user_pass'],ENT_QUOTES);
		$sql = 'SELECT 
					`user_id`,`user_name`,`user_pass` 
				FROM `users` 
				WHERE	`user_name` = :user_name
				LIMIT 1';
		try{
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':user_name',$user_name,PDO::PARAM_STR);
			$stmt->execute();
			$user = $stmt->fetch();
			$stmt->closeCursor();
		}catch(Exception $e){
			die($e->getMessage());
		}
		if(!isset($user["user_id"])){
			return '用户不存在';
		}
		//生成散列后密码
		$hash = $this->getSaltedHash($user_pass,$user['user_pass']);
		//检查密码
		if($user['user_pass']==$hash){
			$_SESSION['user'] = array(
				'id' => $user['user_id'],
				'name' => $user['user_name']
			);
			return TRUE;
		}else{
			return '用户名或密码错误';
		}
	}

	//
	private function getSaltedHash($string,$salt=NULL){
		//没有盐则生成盐
		if($salt==NULL){
			$salt = substr(md5(time()), 0, $this->saltLength);
		}else{
			$salt = substr($salt, 0, $this->saltLength);
		}
		return $salt.sha1($salt.$string);
	}

	public function testSaltedHash($string,$salt=NULL){
		return $this->getSaltedHash($string, $salt);
	}

	public function processLogout(){
		if ($_POST['action']!='user_logout') {
			return '注销错误';
		}
		session_destroy();
		return TRUE;
	}
}