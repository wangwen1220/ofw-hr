<?php
define('IN_QISHI', true);
$alias="QS_index";
require_once(dirname(__FILE__).'/include/common.inc.php');
$file = $_GET['page'];
$smarty->display('member_personal/'.$file.'.htm');
?>