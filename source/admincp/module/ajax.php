<?php

//power by hackerform1991
if (!defined('IN_ROOT')) {
	exit('Access denied');
}
Administrator(1);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: text/html;charset=" . IN_CHARSET);
global $update_api;
$bc = SafeRequest("ac", "get");
if ($bc == 'build') {
	$file = 'data/tmp/build.xml';
	if (empty($_COOKIE['in_build'])) {
		setcookie("in_build", "have", time() + 1800);
		creatdir(dirname($file));
		fwrite(fopen($file, 'w+'), @file_get_contents($update_api . '?auth=build&site=' . $_SERVER['HTTP_HOST']));
	}
	if ($xml = @simplexml_load_file($file)) {
		$build = intval(trim($xml->item['build']));
		if ($build > IN_BUILD) {
			echo $build;
		}
	}
} elseif ($bc == 'grade') {
	if ($xml = @simplexml_load_file('data/tmp/build.xml')) {
		$grade = intval(trim($xml->item['grade']));
		$grade and fwrite(fopen('data/install.lock', 'wb+'), time() + 2592000);
		echo $grade;
	}
}