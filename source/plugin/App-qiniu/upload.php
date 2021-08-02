<?php

require_once '../../system/db.class.php';
require_once 'autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
$auth = new Auth(IN_REMOTEAK, IN_REMOTESK);
$token = $auth->uploadToken(IN_REMOTEBK);
$bucketMgr = new BucketManager($auth);
$uploadMgr = new UploadManager();
@set_time_limit(0);
if($_GET['ac'] == 'icon'){
        $id = intval($_GET['id']);
        $uid = intval($_GET['uid']);
        $upw = SafeSql($_GET['upw']);
        $userid = $GLOBALS['db']->getone("select in_userid from ".tname('user')." where in_userid=$uid and in_userpassword='$upw'");
        $row = $GLOBALS['db']->getrow("select * from ".tname('app')." where in_id=".$id);
        $row or exit('-1');
        $row['in_uid'] == $userid or exit('-2');
        $dst = '../../../data/attachment/'.$row['in_icon'];
        $object = $row['in_icon'];
        $bucketMgr->delete(IN_REMOTEBK, $object);
        $uploadMgr->putFile($token, $object, $dst);
        @unlink($dst);
        $GLOBALS['db']->query("update ".tname('app')." set in_icon='".IN_REMOTEDK.$object."' where in_id=".$id);
        echo '1';
}elseif($_GET['ac'] == 'app'){
        $time = file_get_contents('../../../data/tmp/'.$_GET['time'].'.log');
        $uid = intval($_GET['uid']);
        $upw = SafeSql($_GET['upw']);
        $userid = $GLOBALS['db']->getone("select in_userid from ".tname('user')." where in_userid=$uid and in_userpassword='$upw'");
        $row = $GLOBALS['db']->getrow("select * from ".tname('app')." where in_icon='$time'");
        $row or exit('-1');
        $row['in_uid'] == $userid or exit('-2');
        $dst = '../../../data/attachment/'.$row['in_icon'];
        $object = $row['in_icon'];
        $uploadMgr->putFile($token, $object, $dst);
        @unlink($dst);
        $GLOBALS['db']->query("update ".tname('app')." set in_icon='".IN_REMOTEDK.$object."' where in_id=".$row['in_id']);
        $dst = '../../../data/attachment/'.$row['in_app'];
        $object = $row['in_app'];
        $uploadMgr->putFile($token, $object, $dst);
        @unlink($dst);
        $GLOBALS['db']->query("update ".tname('app')." set in_app='".IN_REMOTEDK.$object."' where in_id=".$row['in_id']);
        echo '1';
}
?>