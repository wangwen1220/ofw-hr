<?php
/*
 * OFweek首页人才网数据
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

$time = time();

$act = isset($_GET['act']) ? trim($_GET['act']) : '';
$debug = isset($_GET['debug']) ? intval($_GET['debug']) : 0;

//获取职位
	/*
	http://hr.ofweek.com/api/getdata.php?act=jobs&cateid=1&num=10[&debug=1]
	1.LED照明 -lights
	2.太阳能光伏-solar
	3.光通讯-fiber
	4.激光-laser
	5.电子工程-ee
	6.工控-gongkong
	7.光电/光学-display
	8.智能电网-smartgrids
	9.仪表仪器-instruments
	10.节能-energy-saving
	*/
if ($act == 'jobs') {
	
	//参数
	$cateId = intval($_GET['cateid']);
	$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
	$addsql = '';
	
	//条件
	if ($cateId) {
		$addsql .= "AND trade LIKE '%,$cateId,%'";
	}
		
	$list = $db->getall("SELECT id,uid,jobs_name,companyname,refreshtime,district_cn,education_cn,experience_cn,wage_min,wage_max FROM 
			(SELECT id,uid,jobs_name,companyname,addtime,refreshtime,district_cn,education_cn,experience_cn,wage_min,wage_max
			FROM hr_jobs
			WHERE (audit=1 OR audit=4) AND display=1 AND ($time<=deadline OR deadline=0) AND $time<=setmeal_deadline AND (company_audit=1 OR company_audit=4)$addsql  
			ORDER BY refreshtime DESC
			LIMIT 800) t
		GROUP BY t.uid
		ORDER BY t.refreshtime DESC
		LIMIT $num");
	
	foreach ($list as $key=>$value) {
		$value['wage_cn'] = z_wage_cn($value['wage_min'], $value['wage_max']);
		$value['refreshtime_cn'] = date('Y-m-d', $value['refreshtime']);
		$value['jobs_url'] = $_CFG['site_domain'].'/jobs/jobs-show-'.$value['id'].'.html';
		$value['company_url'] = $_CFG['site_domain'].'/company/company-show-'.$value['uid'].'.html';
		unset($value['id'], $value['uid'], $value['wage_min'], $value['wage_max']);
		
		//转UTF-8
		foreach ($value as $k=>$v) {
			$value[$k] = iconv('gbk', "utf-8//IGNORE", $v);
		}
		
		$list[$key] = $value;
	}
	
	if($debug){header("Content-type:text/html;charset=utf-8");print_r($list);die;};
	
	echo json_encode($list);
}