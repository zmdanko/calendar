<?php

//包含初始化文件
include_once '../sys/core/init.inc.php';

if(isset($_POST['select_date'])){
	$year = $_POST['year'];
	$month = $_POST['month'];
	$useDate = mktime(0,0,0,$month,1,$year);
}else{
	$useDate = NULL;
}

//载入当月日历
$cal = new Calendar($dbo,$useDate);
//载入顶部
$page_title = '活动日历';
include_once 'assets/common/header.inc.php';

?>

<div id='content'>
	<?php echo $cal->buildCalendar(); ?>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>