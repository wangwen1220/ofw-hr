<?php
 /*
 * 74cms 企业详细页面
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_companyshow";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
        $smarty->cache =true;
		$smarty->cache_lifetime=$mypage['caching'];
	}else{
		$smarty->cache = false;
	}
$cached_id=$_CFG['subsite_id']."|".$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

if (!is_numeric(trim($_GET['id']))) {
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit;
}

$act = $_GET['action']?$_GET['action']:'';


$option = ' WHERE 1 ';
$option .= "AND uid=".intval($_GET['id']);
//查询下拉单的筛选条件 tab2页的数据

if($act == 'list'){
$select_area = array();
$sql = "SELECT district,sdistrict,district_cn FROM ".table('jobs')." $option GROUP BY district_cn ORDER BY COUNT(*) DESC";
#echo $sql;
$res = $db->getall($sql);
#e($res);
if (!empty($res)){
	foreach ($res as $v){
		if ($v['district_cn']){
			if ($v['sdistrict'] == 0){
				$select_area[$v['district']] = $v['district_cn'];
			}
			else{
				$select_area[$v['sdistrict']] = substr($v['district_cn'],strpos($v['district_cn'], '/')+1);
			}
		}
	}
#	e($select_area);
	$smarty->assign('options_area', $select_area);
}
//
$select_edu = array();
$res = get_category_zt('QS_education');
foreach ($res as $v){
	$select_edu[$v['c_id']] = $v['c_name'];
}
$smarty->assign('options_edu',array_reverse($select_edu,1));
}
//END



$mypage['tpl']='../tpl_company/default/company_profile.htm';

$smarty->display($mypage['tpl'],$cached_id);
$db->close();
unset($smarty);
?>
