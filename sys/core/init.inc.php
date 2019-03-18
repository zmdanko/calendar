<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('PRC');
/**
 * 创建初始化文件
 */
//启用session
session_start();
if(!isset($_SESSION['token'])){
	$_SESSION['token'] = sha1(uniqid(mt_rand(),TRUE));
}
//包含配置信息
include_once dirname(__FILE__).'/../config/db.inc.php';
//为配置信息定义常量
foreach($connect as $key => $val){
	define($key,$val);
}
//生成PDO对象
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
$dbo = new PDO($dsn,DB_USER,DB_PASS);
//自动加载类
spl_autoload_register(function ($class) {
	include_once dirname(__FILE__).'/../class/class.' . $class . '.inc.php';
});

?>