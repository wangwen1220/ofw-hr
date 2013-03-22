<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');

$alias = 'industry-2013';
$cached_id=$_CFG['subsite_id']."|".$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');

$smarty->cache = false;
$smarty->cache_lifetime = 3600;

$page = 'jobfair_industry/index.htm';

if(!$smarty->is_cached($page,$cached_id)) {
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);

	$option = " WHERE 1 AND (audit=1 OR audit=4)  AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
	$order = " ORDER BY refreshtime DESC ";

	$sql = "SELECT id,uid,jobs_name,companyname FROM ".table('jobs')." $option $order LIMIT 2000";
	$qry = $db->query($sql);
	$total_num = 60;//
	$job_list = array();
	while($row = $db->fetch_array($qry)){
		if($total_num <= 0) break;
		if(!isset($job_list[$row['uid']])){
			$job_list[$row['uid']] = $row;
			$total_num--;
		}
	}
	
	$job_list_tmp = array();
	foreach ($job_list as $key=>$value) {
		$job_list_tmp[] = $value;
	}
	$job_list = $job_list_tmp;
	unset($job_list_tmp);
	
	$job_list2 = array();
	for ($i = 0; $i < count($job_list); $i += 2) {
		$t = isset($job_list[$i+1]) ? $job_list[$i+1] : array();
		$job_list2[] = array('a1'=>$job_list[$i], 'a2'=>$t);
	}
	$smarty->assign('job_list', $job_list2);


	$smarty->display($page,$cached_id);
}
else {
	$smarty->display($page,$cached_id);
}
unset($smarty);

?>

