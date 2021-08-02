<?php

//power by hackerform1991
include '../system/db.class.php';
include '../system/user.php';
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: text/html;charset=" . IN_CHARSET);
$GLOBALS['userlogined'] or exit('-1');
$bc = SafeRequest("ac", "get");
if ($bc == 'del') {
	$id = intval(SafeRequest("id", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $id);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	$GLOBALS['db']->query("delete from " . tname('app') . " where in_id=" . $id);
	$GLOBALS['db']->query("delete from " . tname('salt') . " where in_aid=" . $id);
	$GLOBALS['db']->query("delete from " . tname('signlog') . " where in_aid=" . $id);
	$GLOBALS['db']->query("update " . tname('user') . " set in_spaceuse=in_spaceuse-" . $row['in_size'] . " where in_userid=" . $row['in_uid']);
	@unlink(IN_ROOT . './data/attachment/' . str_replace('.png', '.mobileprovision', substr($row['in_icon'], -36)));
	@unlink(IN_ROOT . './data/attachment/' . $row['in_icon']);
	@unlink(IN_ROOT . './data/attachment/' . $row['in_app']);
	echo '1';
} elseif ($bc == 'edit') {
	$id = intval(SafeRequest("id", "get"));
	$link = SafeRequest("link", "get");
	$name = unescape(SafeRequest("name", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $id);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	in_array($link, array('data', 'source', 'static')) and exit('-4');
	is_numeric($link) and exit('-4') or is_numeric(auth_codes($link, 'de')) and exit('-4');
	$one = $GLOBALS['db']->getone("select in_id from " . tname('app') . " where in_link='{$link}' and in_id<>" . $id);
	$link and $one and exit('-5');
	$link and !IN_REWRITE and exit('-6');
	$GLOBALS['db']->query("update " . tname('app') . " set in_name='{$name}',in_link='{$link}' where in_id=" . $id);
	echo '1';
} elseif ($bc == 'info') {
	$mobile = SafeRequest("mobile", "get");
	$qq = SafeRequest("qq", "get");
	$firm = unescape(SafeRequest("firm", "get"));
	$job = unescape(SafeRequest("job", "get"));
	updatetable('user', array('in_mobile' => $mobile, 'in_qq' => $qq, 'in_firm' => $firm, 'in_job' => $job), array('in_userid' => $GLOBALS['erduo_in_userid']));
	echo '1';
} elseif ($bc == 'pwd') {
	$old = substr(md5(SafeRequest("old", "get")), 8, 16);
	$new = substr(md5(SafeRequest("new", "get")), 8, 16);
	$old == $GLOBALS['erduo_in_userpassword'] or exit('-2');
	updatetable('user', array('in_userpassword' => $new), array('in_userid' => $GLOBALS['erduo_in_userid']));
	echo '1';
} elseif ($bc == 'send_verify') {
	$nick = unescape(SafeRequest("nick", "get"));
	$card = SafeRequest("card", "get");
	$old = IN_ROOT . './data/tmp/' . $GLOBALS['erduo_in_userid'] . '-';
	$new = IN_ROOT . './data/attachment/avatar/' . $GLOBALS['erduo_in_userid'] . '-';
	updatetable('user', array('in_nick' => $nick, 'in_card' => $card, 'in_verify' => 2), array('in_userid' => $GLOBALS['erduo_in_userid']));
	@rename($old . 'prev.jpg', $new . 'prev.jpg');
	@rename($old . 'after.jpg', $new . 'after.jpg');
	@rename($old . 'hand.jpg', $new . 'hand.jpg');
	echo '1';
} elseif ($bc == 'add_space') {
	$mb = intval(SafeRequest("mb", "get"));
	$mb > 0 or exit('-2');
	$points = $mb * IN_SPACEPOINTS;
	$GLOBALS['erduo_in_points'] < $points and exit('-3');
	$GLOBALS['db']->query("update " . tname('user') . " set in_spacetotal=in_spacetotal+" . $mb * 1048576 . " where in_userid=" . $GLOBALS['erduo_in_userid']);
	$GLOBALS['db']->query("update " . tname('user') . " set in_points=in_points-{$points} where in_userid=" . $GLOBALS['erduo_in_userid']);
	echo '1';
} elseif ($bc == 'each_del') {
	$bid = intval(SafeRequest("aid", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $bid);
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-2');
	updatetable('app', array('in_kid' => 0), array('in_id' => $bid));
	updatetable('app', array('in_kid' => 0), array('in_id' => $row['in_kid']));
	echo '1';
} elseif ($bc == 'each_add') {
	$bid = intval(SafeRequest("aid", "get"));
	$kid = intval(SafeRequest("kid", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $bid);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	getfield('app', 'in_uid', 'in_id', $kid) == $GLOBALS['erduo_in_userid'] or exit('-3');
	getfield('app', 'in_form', 'in_id', $kid) == $row['in_form'] and exit('-4');
	updatetable('app', array('in_kid' => $kid), array('in_id' => $bid));
	updatetable('app', array('in_kid' => $bid), array('in_id' => $kid));
	echo '1';
} elseif ($bc == 'high_speed') {
	$id = intval(SafeRequest("id", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $id);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	IN_SPEEDPOINTS > 0 or exit('-4');
	$GLOBALS['erduo_in_points'] < IN_SPEEDPOINTS and exit('-5');
	$GLOBALS['db']->query("update " . tname('user') . " set in_points=in_points-" . IN_SPEEDPOINTS . " where in_userid=" . $GLOBALS['erduo_in_userid']);
	updatetable('app', array('in_highspeed' => 1), array('in_id' => $id));
	echo '1';
} elseif ($bc == 'signed') {
	$id = intval(SafeRequest("id", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $id);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	!getapp($row['in_app']) and preg_match('/(\\.mobileconfig)$/', $row['in_app']) or exit('-4');
	PHP_OS == 'Linux' or exit('-5');
	extension_loaded('openssl') or exit('-6');
	function_exists('exec') or exit('-7');
	is_file(IN_ROOT . './data/cert/chain.crt') or exit('-8');
	is_file(IN_ROOT . './data/cert/domain.crt') or exit('-9');
	is_file(IN_ROOT . './data/cert/domain.key') or exit('-10');
	$dir = '../../data/attachment/';
	$file = str_replace('.mobileconfig', '-signed.mobileconfig', $row['in_app']);
	exec('openssl smime -sign -in ' . $dir . $row['in_app'] . ' -out ' . $dir . $file . ' -signer ../../data/cert/domain.crt -inkey ../../data/cert/domain.key -certfile ../../data/cert/chain.crt -outform der -nodetach');
	updatetable('app', array('in_app' => $file), array('in_id' => $id));
	@unlink($dir . $row['in_app']);
	echo '1';
} elseif ($bc == 'downapp') {
	$id = intval(SafeRequest("id", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $id);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	preg_match('/(\\.mobileconfig)$/', $row['in_app']) and exit(getapp($row['in_app'], 1));
	IN_DENIED or exit(getapp($row['in_app'], 1));
	$ext = $row['in_form'] == 'iOS' ? '.ipa' : '.apk';
	$salt = md5($id . '|' . time() . '|' . rand(2, pow(2, 24)));
	inserttable('salt', array('in_aid' => $id, 'in_salt' => $salt, 'in_time' => time()));
	echo IN_PATH . 'source/pack/upload/install/proxy.php/' . $salt . $ext;
} elseif ($bc == 'remove_ad') {
	$id = intval(SafeRequest("id", "get"));
	$row = $GLOBALS['db']->getrow("select * from " . tname('app') . " where in_id=" . $id);
	$row or exit('-2');
	$row['in_uid'] == $GLOBALS['erduo_in_userid'] or exit('-3');
	IN_ADPOINTS > 0 or exit('-4');
	$GLOBALS['erduo_in_points'] < IN_ADPOINTS and exit('-5');
	$GLOBALS['db']->query("update " . tname('user') . " set in_points=in_points-" . IN_ADPOINTS . " where in_userid=" . $GLOBALS['erduo_in_userid']);
	updatetable('app', array('in_removead' => 1), array('in_id' => $id));
	echo '1';
}