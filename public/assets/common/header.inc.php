<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $page_title; ?></title>
	<?php 
		$css_files = array('style.css','admin.css','ajax.css');
		foreach ($css_files as $css): 
	?>
		<link rel="stylesheet" type="text/css" href="assets/css/<?php echo $css; ?>" />
	<?php endforeach; ?>
</head>
<body>