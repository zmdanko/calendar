<?php
/**
 * 处理AJAX提交数据
 */
include_once '../../../sys/core/init.inc.php';

//创建关联数组
$actions = array(
	'event_view'=>array(
		'object'=>'Calendar',
		'method'=>'displayEvent'
	),
	'event_form'=>array(
		'object'=>'Calendar',
		'method'=>'displayForm'
	),
	'event_edit'=>array(
		'object'=>'Calendar',
		'method'=>'processForm'
	),
	'delete_event'=>array(
		'object'=>'Calendar',
		'method'=>'confirmDelete'
	),
	'confirm_delete'=>array(
		'object'=>'Calendar',
		'method'=>'confirmDelete'
	)
);
if(isset($actions[$_POST['action']])){
	$use_array = $actions[$_POST['action']];
	$obj = new $use_array['object']($dbo);
	if(isset($_POST['event_id'])){
		$id = (int) $_POST['event_id'];
	}else{
		$id = NULL;
	}
	$method = $use_array['method'];
	echo $obj->$method($id);
}