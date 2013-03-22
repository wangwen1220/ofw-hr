<?php
define('IN_QISHI', true);
$alias="QS_index";
require_once(dirname(__FILE__).'/include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

$act = $_GET['act'];
$no = $_GET['no'];
$returnurl = !empty($_GET['returnurl']) ? trim($_GET['returnurl']) : $_CFG['site_dir'];

$smarty->assign('act', $act);
$smarty->assign('no', $no);
$smarty->assign('returnurl', $returnurl);
$smarty->display("notice.htm");
exit();