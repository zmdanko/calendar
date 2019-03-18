<?php
/**
 * 数据库操作
 */
class DB_Connect{
	// 保存数据库对象
	protected $db;
	//检查数据库对象，不存在则生成
	protected function __construct($dbo=NULL){
		if(is_object($dbo)){
			$this->db = $dbo;
		}else{
			//在/sys/config/db-cred.inc.php中定义常量
			$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
			try{
				$this->db = new PDO($dsn,DB_USER,DB_PASS);
			}
			catch(Exception $e){
				die($e->getMessage());
			}
		}
	}
}
?>