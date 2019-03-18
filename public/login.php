<?php

/**
 * 登陆页面
 */
//包含初始化文件
include_once '../sys/core/init.inc.php';

//载入顶部
$page_title = '登陆';
include_once 'assets/common/header.inc.php';

?>

<div id="content">
	<form id="login" action="assets/inc/process.inc.php" method="post">
		<fieldset>
			<legend>登陆</legend>
			<label for="user_name">用户</label>
			<input type="text" name="user_name" id="user_name" value="" />
			<label for="user_pass">密码</label>
			<input type="password" name="user_pass" id="user_pass" value="" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
			<input type="hidden" name="action" value="user_login" />
			<input type="submit" name="login_submit" value="登陆" />
			<a href="./">取消</a>
		</fieldset>
	</form>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>