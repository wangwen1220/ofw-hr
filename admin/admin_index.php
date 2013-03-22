<?php
 /*
 * 74cms 管理中心首页
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_flash_statement_fun.php');
$act=!empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if($act=='')
{
	$smarty->display('sys/admin_index.htm');
}
elseif($act=='top')
{
	$admininfo=get_admin_one($_SESSION['admin_name']);
	$smarty->assign('admin_rank', $admininfo['rank']);
	$smarty->assign('admin_name', $_SESSION['admin_name']);
	$smarty->display('sys/admin_top.htm');
}
elseif($act=='left')
{
	$smarty->display('sys/admin_left.htm');
}

elseif($act == 'main'){
	$time = time();
	$today_time = strtotime(date('Y-m-d 00:00:00', $time));
	$monday_time = strtotime(date('Y-m-d H:i:s', strtotime('monday')));
	$time_f1 = date('Y-m-d', $time - (24*60*60));
	$time_f2 = date('Y-m-d', $time);
	
	//昨日注册简历数
	$t1 = $today_time - (24*60*60);
	$total_1 = $db->get_total("SELECT COUNT(*) AS num FROM hr_members WHERE utype=2 AND reg_time>=$t1 AND reg_time<$today_time");
	$smarty->assign('total_1', $total_1);
	$total_1_url = "personal.php?act=list&regtime_from=$time_f1&regtime_to=$time_f1";
	$smarty->assign('total_1_url', $total_1_url);
	
	//今日注册简历数
	$total_2 = $db->get_total("SELECT COUNT(*) AS num FROM hr_members WHERE utype=2 AND reg_time>=$today_time");
	$smarty->assign('total_2', $total_2);
	$total_2_url = "personal.php?act=list&regtime_from=$time_f2&regtime_to=$time_f2";
	$smarty->assign('total_2_url', $total_2_url);
	
	//待审核简历数
	$total_3 = $db->get_total("SELECT COUNT(*) AS num FROM hr_resume WHERE audit=2 OR audit=4");
	$smarty->assign('total_3', $total_3);
	$total_3_url = "personal.php?act=list&from=audit";
	$smarty->assign('total_3_url', $total_3_url);
	
	//昨日注册企业数
	$t1 = $today_time - (24*60*60);
	$total_4 = $db->get_total("SELECT COUNT(*) AS num FROM hr_members WHERE utype=1 AND reg_time>=$t1 AND reg_time<$today_time");
	$smarty->assign('total_4', $total_4);
	$total_4_url = "company.php?act=list&regtime_from=$time_f1&regtime_to=$time_f1";
	$smarty->assign('total_4_url', $total_4_url);
	
	//今日注册企业数
	$total_5 = $db->get_total("SELECT COUNT(*) AS num FROM hr_members WHERE utype=1 AND reg_time>=$today_time");
	$smarty->assign('total_5', $total_5);
	$total_5_url = "company.php?act=list&regtime_from=$time_f2&regtime_to=$time_f2";
	$smarty->assign('total_5_url', $total_5_url);
	
	//本周投递简历数	
	$total_6 = $db->get_total("SELECT COUNT(*) AS num FROM hr_personal_jobs_apply WHERE apply_addtime>=$monday_time");
	$smarty->assign('total_6', $total_6);
	
	//上周投递简历数
	$t1 = $monday_time - (7*24*60*60);
	$total_7 = $db->get_total("SELECT COUNT(*) AS num FROM hr_personal_jobs_apply WHERE apply_addtime>=$t1 AND apply_addtime<$monday_time");
	$smarty->assign('total_7', $total_7);
	
	//待审核企业
	$total_8 = $db->get_total("SELECT COUNT(*) AS num FROM hr_company_profile WHERE audit=2 OR audit=4");
	$smarty->assign('total_8', $total_8);
	$total_8_url = "company.php?act=list&from=audit";
	$smarty->assign('total_8_url', $total_8_url);
	
	//过期企业
	$total_9 = $db->get_total("SELECT COUNT(*) AS num FROM hr_members_setmeal WHERE endtime<=$time");
	$smarty->assign('total_9', $total_9);
	$total_9_url = "company.php?act=list&expire=1";
	$smarty->assign('total_9_url', $total_9_url);
	
	//审核未通过企业
	$total_10 = $db->get_total("SELECT COUNT(*) AS num FROM hr_company_profile WHERE audit=3");
	$smarty->assign('total_10', $total_10);
	$total_10_url = "company.php?act=list&audit=3";
	$smarty->assign('total_10_url', $total_10_url);
	
	//昨日职位发布数
	$t1 = $today_time - (24*60*60);
	$total_11 = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE addtime>=$t1 AND addtime<$today_time");
	$smarty->assign('total_11', $total_11);
	$total_11_url = "job.php?act=list&addtime_from=$time_f1&addtime_to=$time_f1";
	$smarty->assign('total_11_url', $total_11_url);
	
	//今日职位发布数
	$total_12 = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE addtime>=$today_time");
	$smarty->assign('total_12', $total_12);
	$total_12_url = "job.php?act=list&addtime_from=$time_f2&addtime_to=$time_f2";
	$smarty->assign('total_12_url', $total_12_url);
	
	//待审核职位
	$total_13 = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE audit=2 OR audit=4");
	$smarty->assign('total_13', $total_13);
	$total_13_url = "job.php?act=list&from=audit";
	$smarty->assign('total_13_url', $total_13_url);
	
	//过期职位数
	$total_14 = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE setmeal_deadline<$time OR (deadline>0 AND deadline<$time)");
	$smarty->assign('total_14', $total_14);
	$total_14_url = "job.php?act=list&expire=1";
	$smarty->assign('total_14_url', $total_14_url);
	
	$config_num = z_config_value('month_freesetmeal_num');
	$num = z_month_freesetmeal_num();
	$num2 = $config_num - $num;
	
	//本月可设置免费企业会员数
	$smarty->assign('total_15', $config_num);
	
	//本月开通的免费企业会员数
	$smarty->assign('total_16', $num);
	
	$smarty->assign('pageheader',"管理中心 - 后台管理首页");
	$smarty->display('z/index.htm');
}
?>