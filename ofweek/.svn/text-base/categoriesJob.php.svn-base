<?php
/*
 * OFweek财经网人才网数据
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$time = time();

$debug = isset($_GET['debug']) ? intval($_GET['debug']) : 0;
$num = isset($_GET['num']) ? intval($_GET['num']) : 9;
$_list = array();

$list = $db->getall("SELECT * FROM 
	(SELECT id,uid,jobs_name,companyname,addtime,refreshtime
	FROM hr_jobs
	WHERE  (audit=1 OR audit=4) AND display=1 AND ($time<=deadline OR deadline=0) AND $time<=setmeal_deadline AND (company_audit=1 OR company_audit=4)  
	ORDER BY refreshtime DESC
	LIMIT 200) t
GROUP BY t.uid
ORDER BY t.refreshtime DESC
LIMIT $num");

foreach ($list as $key=>$value) {
	$jobs_url = $_CFG['site_domain'].'/jobs/jobs-show.php?id='.$value['id']; 
	
	$tmp['jobs_name'] = iconv('gbk', "utf-8//IGNORE", $value['jobs_name']);
	$tmp['company_name'] = iconv('gbk', "utf-8//IGNORE", $value['companyname']);
	$tmp['jobs_url'] = iconv('gbk', "utf-8//IGNORE", $jobs_url);
	$_list[] = $tmp;
}

if ($debug) {
	print_r($_list);die;
}

echo json_encode($_list);