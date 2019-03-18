<?php

/**
 * 显示活动信息
 */
//确认传入活动ID
if(isset($_GET['event_id'])){
	$id = preg_replace('/[^0-9]/','',$_GET['event_id']);
	if(empty($id)){
		header('Location:./');
		exit;
	}
}else{
		header('Location:./');
		exit;
}

include_once '../sys/core/init.inc.php';
$page_title = '活动信息';
include_once 'assets/common/header.inc.php';

//连接数据库
$cal = new Calendar($dbo);

?>

<div id='content'>
	<?php echo $cal->displayEvent($id) ?>
	<a href="./">返回日历</a>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>