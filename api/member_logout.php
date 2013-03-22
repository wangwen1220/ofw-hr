<?php
/**
 *  ำรปงอหณ๖
 * 
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');

$_SESSION['uid'] = '';
$_SESSION['username'] = '';
$_SESSION['utype'] = '';
$_SESSION['auth_uid'] = '';
$_SESSION['auth_username'] = '';
$_SESSION['auth_utype'] = '';

echo 'ok';