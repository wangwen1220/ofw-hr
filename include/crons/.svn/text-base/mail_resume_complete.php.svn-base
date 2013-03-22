<?php
/*
 1.	注册会员后一周内未完善的简历，发送邮件通知其完善简历。
 2.	发送过的用户，三个月内未完善则重新发送。已完善则不再发送。
 */

if(!defined('IN_QISHI')) {
	die('Access Denied!');
}

ignore_user_abort(true);
set_time_limit(180);

global $_CFG,$timestamp;


	//更新任务时间表
	if ($crons['weekday']>=0)
	{
	$weekday=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$nextrun=strtotime("Next ".$weekday[$crons['weekday']]);
	}
	elseif ($crons['day']>0)
	{
	$nextrun=strtotime('+1 months'); 
	$nextrun=mktime(0,0,0,date("m",$nextrun),$crons['day'],date("Y",$nextrun));
	}
	else
	{
	$nextrun=time();
	}
	if ($crons['hour']>=0)
	{
	$nextrun=strtotime('+1 days',$nextrun); 
	$nextrun=mktime($crons['hour'],0,0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	if (intval($crons['minute'])>0)
	{
	$nextrun=strtotime('+1 hours',$nextrun); 
	$nextrun=mktime(date("H",$nextrun),$crons['minute'],0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	$setsqlarr['nextrun']=$nextrun;
	$setsqlarr['lastrun']=time();
	updatetable(table('crons'), $setsqlarr," cronid ='".intval($crons['cronid'])."'");
	//end
	

//变量
$t = time();
$one_week = $t - (3*24*60*60);//三天
$three_month = $t - (3*30*24*60*60);

//列表
$sql = "SELECT m.uid,m.username,m.email,r.fullname
	FROM hr_members m
	LEFT JOIN hr_resume r ON r.uid=m.uid
	WHERE m.utype=2 AND r.complete<4 AND ((m.mail_complete=0 AND m.reg_time<$one_week) || (m.mail_complete=1 AND m.mail_complete_time<$three_month))
	LIMIT 100";

$list = $db->getall($sql);

//最近岗位
$t = time();
$jobs_list_html = '';
$sql = "SELECT id,jobs_name,uid,companyname,district_cn,education_cn,experience_cn,wage_min,wage_max,refreshtime
	FROM hr_jobs
	WHERE (audit=1 OR audit=4) AND display=1 AND (deadline=0 OR deadline>$t) AND setmeal_deadline>$t
	ORDER BY refreshtime DESC
	LIMIT 12";
$jobs_list = $db->getall($sql);
$jobs_list_html = '';
foreach ($jobs_list as $key=>$job) {
	$style = ($key % 2 == 0) ? 'background:#fff' : 'background:#E6EFF9';
	$job['wage_cn'] = z_wage_cn($job['wage_min'], $job['wage_max']);
	$job['refreshtime'] = date('Y-m-d', $job['refreshtime']);
	$jobs_list_html .= '<tr style="'.$style.'">
				<td style="text-indent:2em; border-left:1px solid #eae4e4;"><a href="'.$_CFG['site_domain'].'/jobs/jobs-show.php?id='.$job['id'].'" target="_blank">'.$job['jobs_name'].'</a></td>
				<td style="text-indent:4em;"><a href="'.$_CFG['site_domain'].'/company/company-show.php?id='.$job['uid'].'" target="_blank">'.$job['companyname'].'</a></td>
				<td align="center">'.$job['district_cn'].' </td>
				<td align="center">'.$job['education_cn'].' </td>
				<td align="center">'.$job['experience_cn'].' </td>
				<td align="center">'.$job['wage_cn'].' </td>
				<td style="border-right:1px solid #eae4e4;">'.$job['refreshtime'].' </td>
		</tr>';
}

//发邮件
foreach ($list as $key=>$member) {
	//+send email 简历完善提醒
	$mailArr = array(
		'to'=>$member['email']
	);
	$data = array(
		'fullname'=>$member['fullname'],
		'jobs_list_html'=>$jobs_list_html
	);
	z_mail('resume_complete', $mailArr, $data);
	//end
	
	$db->query("UPDATE hr_members SET `mail_complete`=`mail_complete`+1,`mail_complete_time`=$t WHERE uid=".$member['uid']);
}
