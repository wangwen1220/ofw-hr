<?php
/**
 *  检查密码是否正确
 * 
 * $_GET['password']
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

$ok = 0;
$password = trim($_GET['password']);
$uid = $_SESSION['uid'];
if ($uid && $password) {
	$member = z_member($uid);
	if ($member['password'] == md5($password)) {
		$ok = 1;
	}
}

echo $ok;