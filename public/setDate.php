<?php

/**
 * 设定日历日期
 */

//用户未登录则返回首页
include_once '../sys/core/init.inc.php';

$page_title = '设置日期';
include_once 'assets/common/header.inc.php';

?>
		
<div id="content">
	<form action="index.php" method="post">
		<fieldset>
			<legend>选择日期</legend>
			<ul class="setDate">
				<li class="setDate">
					<label for="year">年</label>
					<select name="year">
						<?php
						for($i=date('Y')-20;$i<=date('Y')+20;$i++){
							$selected = $i==date('Y') ? "selected" : "";
							echo "<option value=\"$i\" $selected>$i</option>";
						}
						?>
					</select>
				</li>
				<li class="setDate">
					<label for="month">月</label>
					<select name="month">
					<?php
					for($i=1;$i<=12;$i++){
						$selected = $i==date('m') ? "selected" : "";
						echo "<option value=\"$i\" $selected>$i</option>";
					}
					?>
					</select>
				</li>
			</ul>
			<input type="hidden" name="select_date" />
			<input type="submit" name="date_submit" value="确定" />
			<a href="./">取消</a>
		</fieldset>
	</form>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>