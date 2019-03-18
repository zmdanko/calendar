<?php

//包含初始化文件
include_once '../sys/core/init.inc.php';
//载入当月日历
$adm = new Admin($dbo);

$salt = '0d67d47bc81ba69a9abd49cf512eb2e948f99bb8619fc1e';
$pass = $adm->testSaltedHash('admin',"$salt");
echo 'Hash<br/>'.$pass.'<br/>';



?>