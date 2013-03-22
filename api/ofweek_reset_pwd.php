<?php
/**
 * 接收OFweek修改密码数据
 * 
 * $_POST['username'],$_POST['oldpwd'],$_POST['newpwd']
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

if (!empty($_POST)) {
	
	$logfile = dirname(__FILE__).'/reset_pwd.log';
	$logtxt = date('Y-m-d H:i:s', time());
	$logtxt .= '[pwd]';
	foreach ($_POST as $key=>$value) {
		$logtxt .= "$key:$value,";
	}
	$logtxt .= "\r\n";
	error_log($logtxt, 3, $logfile);
	
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	if ($username && $password) {
		$members = $db->getone("SELECT * FROM ".table('members')." WHERE username='$username'");
		
		if (!empty($members)) {
			
			$md5password = md5($password);
			$db->query("UPDATE ".table('members')." SET password = '$md5password'  WHERE username='".$username."'");
			write_memberslog($members['uid'],$members['utype'],1004,$members['username'],"重置了密码");
			
			echo 1;//成功
		} else {
			echo 1;//用户不存在
		}
	} else {
		echo 0;//参数错误
	}
} else {
	echo 0;
}
	