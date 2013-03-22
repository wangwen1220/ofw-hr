<?php
/*
 * OFweek子站人才网数据
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$time = time();

//参数
$cateId = intval($_GET['categorid']);#5工控
$width1 = intval($_GET['width1']);
$width2 = intval($_GET['width2']);
$style1 = empty($_GET['width1']) ? '' : ' style="width:'.$_GET['width1'].'px;"';
$style2 = empty($_GET['width2']) ? '' : ' style="width:'.$_GET['width2'].'px;"';

//对应表
$cateMap = array(
	'1'=>'8',#智能电网-smartgrids
	'2'=>'4',#激光-laser
	'3'=>'1',#LED照明 -lights
	'5'=>'6',#工控-gongkong
	'6'=>'2',#太阳能光伏-solar
	'7'=>'3',#光通讯-fiber
	'8'=>'5',#电子工程-ee
	'9'=>'7',#光电/光学-display
	'22'=>'9',#仪表仪器-instruments
	'23'=>'10',#节能-energy-saving
);
$cateId = $cateMap[$cateId];

//列表
/*取7条最新通过的未过期招聘信息.uid唯一*/
$list = $db->getall("SELECT * FROM 
	(SELECT id,uid,jobs_name,companyname,addtime,refreshtime,district_cn 
	FROM hr_jobs
	WHERE (audit=1 OR audit=4) AND display=1 AND trade LIKE '%,$cateId,%' AND ($time<=deadline OR deadline=0) AND $time<=setmeal_deadline AND (company_audit=1 OR company_audit=4) 
	ORDER BY refreshtime DESC
	LIMIT 200) t
GROUP BY t.uid
ORDER BY t.refreshtime DESC
LIMIT 7");

//不足
$fill_num = 7 - count($list);
if ($fill_num) {
	$idArr = array();
	foreach ($list as $value) {
		$idArr[] = $value['id'];
	}
	$ids = implode(',', $idArr);
	$addSql = $ids ? " AND id NOT IN($ids)" : "";
	
	$list2 = $db->getall("SELECT * FROM 
		(SELECT id,uid,jobs_name,companyname,addtime,refreshtime,district_cn 
		FROM hr_jobs
		WHERE (audit=1 OR audit=4) AND display=1$addSql AND ($time<=deadline OR deadline=0) AND $time<=setmeal_deadline AND (company_audit=1 OR company_audit=4) 
		ORDER BY refreshtime DESC
		LIMIT 200) t
	GROUP BY t.uid
	ORDER BY t.refreshtime DESC
	LIMIT $fill_num");
}
foreach ($list2 as $value) {$list[] = $value;}

$list_html = '';
foreach ($list as $key=>$value) {
	$jobs_name = cut_str($value['jobs_name'], 9);
	$companyname = cut_str($value['companyname'], 15);
	$district_cn = cut_str($value['district_cn'], 7);
	$jobs_url = $_CFG['site_domain'].'/jobs/jobs-show.php?id='.$value['id']; 
	$refreshtime = date('Y-m-d', $value['refreshtime']);
	
	if ($cateId==6) {
		$list_html .= '<tr>'.
						  '<td width="40%" height="24" align="left" style="padding-left:5px;"><a target="_blank" title="'.$jobs_name.'" href="'.$jobs_url.'">'.$jobs_name.'</a></td>'.
						  '<td width="45%" height="24" align="left">'.$companyname.'</td>'.
						  '<td width="15%" height="24" align="right" style="padding-right:5px;">'.$refreshtime.'</td>'.
					  '</tr>';
	} else {
		$list_html .= '<tr>'.
						  '<td width="35%" height="24" align="left" style="padding-left:5px;"><a target="_blank" title="'.$jobs_name.'" href="'.$jobs_url.'">'.$jobs_name.'</a></td>'.
						  '<td width="38%" height="24" align="left">'.$companyname.'</td>'.
						  '<td width="15%" height="24" align="left">'.$district_cn.'</td>'.
						  '<td width="75" height="24" align="left" >'.$refreshtime.'</td>'.
					  '</tr>';
	}
}

$cssName = $cateId==6 ? 'ofweek_gongkong.css' : 'ofweek.css';
$html = '<link type="text/css" href="'.$_CFG['site_domain'].'/ofweek/css/'.$cssName.'" rel="stylesheet" />'.
		'<div class="hr_rcbiaoti"'.$style1.'>'.
			'<span class="more1" style="padding-top:8px;"><a target="_blank" href="'.$_CFG['site_domain'].'/jobs/">更多 >></a></span>'.
			'人才招聘'.
		'</div>'.
		'<div class="hr_rckuang"'.$style2.'>'.
			'<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 13px;">'.
			$list_html.
			'</table>'.
		'</div>';
$html = iconv('gbk', "utf-8//IGNORE", $html);			
echo $html;