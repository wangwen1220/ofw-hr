<?php
/*企业登录*/

define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

$ajax = isset($_GET['ajax']) ? intval($_GET['ajax']) : 0;

if (!empty($_POST)) {
	
	//参数
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$returnurl = trim($_POST['returnurl']);
	
	if ($username && $password) {
		$member = z_member_username($username, 1);
		if (!empty($member) && $member['password']==md5($password)) {
			
			//写session
			$_SESSION['uid'] = $member['uid'];
			$_SESSION['username'] = $member['username'];
			$_SESSION['utype'] = $member['utype'];
			
			if ($ajax) {
				echo 1;die;
			} else {
				z_goto($returnurl);
			}
		}
	}
	
	if ($ajax) {
		echo 0;die;
	} else {
		z_goto($_CFG['site_domain'].'/notice.php?act=login&no=-1&returnurl='.$returnurl);
	}
}