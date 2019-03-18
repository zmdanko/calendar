<?php
/**
 * 处理表单提交数据
 */
include_once '../../../sys/core/init.inc.php';
//创建关联数组
$actions = array(
	'event_edit'=> array(
		'object'=>'calendar',
		'method'=>'processForm',
		'header'=>'Location:../../'
	),
	'user_login'=>array(
		'object'=>'Admin',
		'method'=>'processLogin',
		'header'=>'Location:../../'
	),
	'user_logout'=>array(
		'object'=>'Admin',
		'method'=>'processLogout',
		'header'=>'Location:../../'
	)
);

if($_POST['token']==$_SESSION['token'] && isset($actions[$_POST['action']])){
	$use_array = $actions[$_POST['action']];
	$obj = new $use_array['object']($dbo);
	$method = $use_array['method'];
	$msg = $obj->$method();
	if(TRUE===$msg||is_numeric($msg)){
		header($use_array['header']);
		exit;
	}else{
		die($msg);
	}
}else{
	header('Location:../../');
	exit;
}
?>