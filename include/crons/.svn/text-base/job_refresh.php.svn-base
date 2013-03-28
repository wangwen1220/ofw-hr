<?php
/*
 1.	自动刷新7天之内发布的职位
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
	

/*###刷新规则###
1.修改时间（3个月内），刷新时间为 00:00:01
2.修改时间（7天内），以及名企，刷新时间为00:00:02
3.发布时间（7天内），刷新时间为00:00:03
*/

/*时间*/
$today_start = strtotime(date('Y-m-d 00:00:00', time()));
$today_01 = $today_start + 1;
$today_02 = $today_start + 2;
$today_03 = $today_start + 3;
$addtime_7d  = $today_start - 7*24*60*60;
$edittime_7d = $today_start - 7*24*60*60;
$edittime_3m = $today_start - 3*30*24*60*60;

/*名企*/
$uidarr = array();
$uidarr[] = 0;
$mc = $db->getall("SELECT uid FROM hr_company_profile WHERE company_type=2");
foreach ($mc as $c) {
	$uidarr[] = $c['uid'];
}

/*刷新发布时间（7天内）的职位*/
$db->query("UPDATE hr_jobs 
	SET refreshtime=$today_03 
	WHERE addtime>=$addtime_7d AND addtime<$today_start
");

/*刷新修改时间（7天内）的职位*/
$db->query("UPDATE hr_jobs 
	SET refreshtime=$today_02 
	WHERE uid IN(".implode(',', $uidarr).") OR (addtime<$addtime_7d AND edittime>=$edittime_7d)
");

/*刷新修改时间（3个月内）的职位*/
$db->query("UPDATE hr_jobs 
	SET refreshtime=$today_01 
	WHERE uid NOT IN(".implode(',', $uidarr).") AND (addtime<$addtime_7d AND edittime<$edittime_7d AND edittime>=$edittime_3m)
");
