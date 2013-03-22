<?php
/*
 * OFweek首页人才网数据
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$time = time();

/*取9条最新通过的未过期招聘信息.uid唯一*/
$list = $db->getall("SELECT * FROM 
	(SELECT id,uid,jobs_name,companyname,addtime,refreshtime
	FROM hr_jobs
	WHERE  (audit=1 OR audit=4) AND display=1 AND ($time<=deadline OR deadline=0) AND $time<=setmeal_deadline AND (company_audit=1 OR company_audit=4)  
	ORDER BY refreshtime DESC
	LIMIT 200) t
GROUP BY t.uid
ORDER BY t.refreshtime DESC
LIMIT 9");


$list_html = '';
foreach ($list as $key=>$value) {
	$jobs_name = cut_str($value['jobs_name'], 9);
	$companyname = cut_str($value['companyname'], 15);
	$jobs_url = $_CFG['site_domain'].'/jobs/jobs-show.php?id='.$value['id']; 
	
	$list_html .= '<li class="jobname"><a target="_blank" title="'.$jobs_name.'" href="'.$jobs_url.'">'.$jobs_name.'</a></li>'.
				  '<li class="companyname">'.$companyname.'</li>';
}

$html = '<div id="dept_right">'.
			'<div id="dept_bar">'.
				'<ul>'.
				'<li class="ti"><span>人才招聘</span></li>'.
				'<li class="more"><a target="_blank" href="'.$_CFG['site_domain'].'/jobs/">更多&gt;&gt;</a></li>'.
				'</ul>'.
			'</div>'.
			'<div id="dept_jobinfo">'.
			'<ul>'.$list_html.'</ul>'.
			'</div>'.
		'</div>';
$html = iconv('gbk', "utf-8//IGNORE", $html);
echo $html;