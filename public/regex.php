<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>正则表达式</title>
	<style type="text/css">
		em{
			background-color: #FF0;
			border-top: 1px solid #000;
			border-bottom: 1px solid #000;
		}
	</style>
</head>
<body>
<?php
// $string = <<<EOF
// 	<h2>Regular Expreesion Testing</h2>
// 	<p>
// 		In this document, there is a lot of text that can be matched using regex. The benefit of using a regular expreesion is much more flexible  &mdash; albeit complex &mdash; syntax for text pattern matching.
// 	</p>
// 	<p>
// 		After you get the hang of regular expressions, also called regexes, they will become a powerful tool for pattren matching.
// 	</p>
// 	<hr />
// EOF;

// 	/**
// 	 * 
// 	 */
// 	$pattern = "/(reg(ular\s)?ex(pressions?|es)?)/i";
// 	echo preg_replace($pattern,"<em>$1</em>",$string);
// 	echo "\n<p><strong>$pattern</strong></p>";
$date[] = '2010-01-14 12:00:00';
$date[] = 'Saturday, May 14th at 7pm';
$date[] = '02/03/10 10:00pm';
$date[] = '2010-01-14 102:00:00';

$pattern = "/^(\d{4}(-\d{2}){2} (\d{2})(:\d{2}){2})$/";
foreach ($date as $day) {
	echo "<p>", preg_replace($pattern, "<em>$1</em>", $day), "</p>";
}
?>
</body>
</html>