<?php
/*
 1.	简历超过3个月未登陆的简历
 2.	已发送过的用户，在发送通知后三个月依然未更新，重新发送。否则不再发送。 
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
//$three_month = $t - (1*60*60);
$three_month = $t - (3*30*24*60*60);

//列表
$sql = "SELECT m.uid,m.username,m.email,r.fullname
	FROM hr_members m
	LEFT JOIN hr_resume r ON r.uid=m.uid 
	WHERE m.utype=2 AND ((m.mail_refresh=0 AND (m.last_login_time>0 AND m.last_login_time<$three_month)) OR (m.mail_refresh=1 AND m.mail_refresh_time<$three_month))
	LIMIT 100";
$list = $db->getall($sql);

//发邮件
foreach ($list as $key=>$member) {
	//+send email 简历更新
	$mailArr = array(
		'to'=>$member['email']
	);
	$data = array(
		'fullname'=>$member['fullname'],
		'username'=>$member['username']
	);
	z_mail('resume_refresh', $mailArr, $data);
	//end
	
	$db->query("UPDATE hr_members SET `mail_refresh`=`mail_refresh`+1,`mail_refresh_time`=$t WHERE uid=".$member['uid']);
}
