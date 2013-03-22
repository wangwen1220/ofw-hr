<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');

$alias = 'industry-2013';
$cached_id=$_CFG['subsite_id']."|".$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');

$smarty->cache = true;
$smarty->cache_lifetime = 300;

$page = 'jobfair_led/index.htm';

if(!$smarty->is_cached($page,$cached_id)) {
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);

	$sql = "SELECT uid FROM hr_company_profile WHERE trade LIKE ',1,'";
	$tmp = $db->getall($sql);
	$uidArr = array();
	foreach ($tmp as $key=>$value) {
		$uidArr[] = $value['uid'];
	}
	
	$option = " WHERE 1 AND (uid IN(".implode(',', $uidArr).") OR trade LIKE ',1,') AND (audit=1 OR audit=4)  AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
	$order = " ORDER BY refreshtime DESC ";

	$sql = "SELECT id,uid,jobs_name,companyname,district_cn,education_cn,sex_cn,experience_cn,wage_min,wage_max,refreshtime FROM ".table('jobs')." $option $order LIMIT 1000";
	$qry = $db->query($sql);
	$total_num = 30;//
	$job_list = array();
	while($row = $db->fetch_array($qry)){
		if($total_num <= 0) break;
		if(!isset($job_list[$row['uid']])){
			$job_list[$row['uid']] = $row;
			$total_num--;
		}
	}
	
	$smarty->assign('job_list', $job_list);


	$smarty->display($page,$cached_id);
}
else {
	$smarty->display($page,$cached_id);
}
unset($smarty);

?>

