<?php
/**
 *  用户是否存在
 * 
 * $_GET['username']
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

$existed = 1;
$username = trim($_GET['username']);
if ($username) {
	$existed = $db->get_total("SELECT COUNT(*) AS num FROM hr_members WHERE username='$username'");
}

echo $existed ? 'exist' : 'no-exist';