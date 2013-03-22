<?php
/*
 * 74cms 企业会员中心
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='index') {
	
	$setmeal = z_setmeal($_SESSION['uid']);
	$company = get_company($_SESSION['uid']);
	$t = time();
	
	//已发布职位总数
	$count_jobs = count_jobs($_SESSION['uid']);
	//收到的职位申请总数
	$count_apply = $user['c_resume_num'];;
	//已下载简历总数
	$count_down_resume = $user['c_down_num'];
	$count_down_resume_t = count_down_resume($_SESSION['uid']);
	//收藏简历
	$count_favorites = count_favorites($_SESSION['uid']);
	//剩余职位数
	$count_jobs_leave = max(0, $setmeal['jobs_ordinary'] - $count_jobs);
	//剩余简历
	$count_down_resume_leave = max(0, $setmeal['download_resume_ordinary'] - $count_down_resume_t);
	//面试通知
	$count_interview = $user['c_interview_num'];
	//招聘信息-状态数
	//招聘中
	//$count_jobs_display_1 = count_jobs_display($_SESSION['uid'], 1);
	$count_jobs_display_1 = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE uid=".$_SESSION['uid']." AND audit=1 AND display=1 AND (deadline=0 OR deadline>$t) AND setmeal_deadline>$t");
	//暂停招聘
	$count_jobs_display_2 = count_jobs_display($_SESSION['uid'], 2);
	//2审核中
	$count_jobs_audit_2 = count_jobs_audit($_SESSION['uid'], 2);
	
	//公司状态
	switch ($company['audit']) {
		case 1: $audit_cn = '审核通过'; $audit_style = 'f_blue'; break;
		case 2: 
		case 4: $audit_cn = '待审核'; $audit_style = 'f_black'; break;
		case 3: $audit_cn = '未通过'; $audit_style = 'f_red'; break;
	}
	$smarty->assign('audit_cn', $audit_cn);
	$smarty->assign('audit_style', $audit_style);
	
	//是否过期
	$t = time() < $setmeal['endtime'] ? 0 : 1;
	$smarty->assign('expire', $t);
	
	//未读消息
	$msg_unread_num = z_message_unread_num($_SESSION['uid']);
	$smarty->assign('msg_unread_num', $msg_unread_num);
	
	//是否第一次
	if ($company['showed'] == 0) {
		updatetable('hr_company_profile', array('showed'=>1), array('uid'=>$company['uid']));
	}
	
	//基本资料是否完善
	$baseinfo_complete = ($company['trade'] && $company['contact'] && $company['telephone'] && $company['email'] && $company['address'] && $company['website']) ? 1 : 0;
	$smarty->assign('baseinfo_complete', $baseinfo_complete);
	
	$smarty->assign('title','企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('count_jobs',$count_jobs);
	$smarty->assign('count_apply',$count_apply);
	$smarty->assign('count_down_resume',$count_down_resume);
	$smarty->assign('count_favorites',$count_favorites);
	$smarty->assign('count_jobs_leave',$count_jobs_leave);
	$smarty->assign('count_down_resume_leave',$count_down_resume_leave);
	$smarty->assign('count_interview',$count_interview);
	$smarty->assign('count_jobs_display_1',$count_jobs_display_1);
	$smarty->assign('count_jobs_display_2',$count_jobs_display_2);
	$smarty->assign('count_jobs_audit_2',$count_jobs_audit_2);
	
	$smarty->assign('user',$user);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('notice',get_notice(0,6," WHERE type_id='2' AND is_display='1'"));
	$smarty->assign('userprofile',get_userprofile($_SESSION['uid']));
	$smarty->assign('concern_id',get_concern_id($_SESSION['uid']));
	$smarty->assign('company',$company);
	$smarty->assign('setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',date('Y-m-d',$setmeal['endtime']));
	
	$smarty->display('member_company/company_index.htm');
}
unset($smarty);
?>