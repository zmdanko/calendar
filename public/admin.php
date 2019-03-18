<?php

/**
 * 编辑活动信息
 */

//用户未登录则返回首页
include_once '../sys/core/init.inc.php';
if(!isset($_SESSION['user'])){
	header("Location:./");
	exit;
}

$page_title = '编辑活动';
include_once 'assets/common/header.inc.php';
$cal = new Calendar($dbo);

?>

<div id='content'>
	<?php echo $cal->displayForm(); ?>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>