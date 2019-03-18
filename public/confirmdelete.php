<?php

/**
 * 确认删除界面
 */
include_once '../sys/core/init.inc.php';

if(isset($_SESSION['user'])&&isset($_POST['event_id'])){
	$id = (int) $_POST['event_id'];
}else{
	header("Location:./");
	exit;
}

$cal = new Calendar($dbo);
$return = $cal->confirmDelete($id);
if(TRUE === $return){
	header('Location:./');
	exit;
}
$page_title = "删除活动";
include_once 'assets/common/header.inc.php';

?>

<div id="content">
	<?php echo $return; ?>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>