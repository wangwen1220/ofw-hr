<?php
define('IN_QISHI', true);
$alias="QS_hrlogin";
require_once(dirname(__FILE__).'/../include/common.inc.php');

require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

if ($_SESSION[uid] && $_SESSION['utype']==1) {
	header("location:".$_CFG['site_domain']."/user/company/company_index.php");
}

$smarty->display('user/hrlogin.htm');
unset($smarty);