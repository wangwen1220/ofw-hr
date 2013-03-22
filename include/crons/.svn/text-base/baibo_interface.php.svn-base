<?php

if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit", '1024M');

global $_CFG,$timestamp;

include_once(QISHI_ROOT_PATH.'/include/zt_xml_class.php');

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
 	
	$time_limit = ($timestamp - 6*30*24*60*60);
	
	$option = " WHERE 1 AND (audit=1 OR audit=4)  AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) AND (refreshtime >= $time_limit) ";

	$sql = "SELECT * FROM ".table('jobs')." $option ";
	$res = $db->getall($sql);
//	e($res);
	$xml = new BaiboXMLGenerator('http://hr.ofweek.com/', QISHI_ROOT_PATH.'/');
	$xml->addUrls($res,  $timestamp,  'daily',  '1');
	$xml->createSitemap();
	$xml->writeSitemap();


?>
