<?php
/*
 * 74cms ��ҵ��Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='index') {
	
	$setmeal = z_setmeal($_SESSION['uid']);
	$company = get_company($_SESSION['uid']);
	$t = time();
	
	//�ѷ���ְλ����
	$count_jobs = count_jobs($_SESSION['uid']);
	//�յ���ְλ��������
	$count_apply = $user['c_resume_num'];;
	//�����ؼ�������
	$count_down_resume = $user['c_down_num'];
	$count_down_resume_t = count_down_resume($_SESSION['uid']);
	//�ղؼ���
	$count_favorites = count_favorites($_SESSION['uid']);
	//ʣ��ְλ��
	$count_jobs_leave = max(0, $setmeal['jobs_ordinary'] - $count_jobs);
	//ʣ�����
	$count_down_resume_leave = max(0, $setmeal['download_resume_ordinary'] - $count_down_resume_t);
	//����֪ͨ
	$count_interview = $user['c_interview_num'];
	//��Ƹ��Ϣ-״̬��
	//��Ƹ��
	//$count_jobs_display_1 = count_jobs_display($_SESSION['uid'], 1);
	$count_jobs_display_1 = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE uid=".$_SESSION['uid']." AND audit=1 AND display=1 AND (deadline=0 OR deadline>$t) AND setmeal_deadline>$t");
	//��ͣ��Ƹ
	$count_jobs_display_2 = count_jobs_display($_SESSION['uid'], 2);
	//2�����
	$count_jobs_audit_2 = count_jobs_audit($_SESSION['uid'], 2);
	
	//��˾״̬
	switch ($company['audit']) {
		case 1: $audit_cn = '���ͨ��'; $audit_style = 'f_blue'; break;
		case 2: 
		case 4: $audit_cn = '�����'; $audit_style = 'f_black'; break;
		case 3: $audit_cn = 'δͨ��'; $audit_style = 'f_red'; break;
	}
	$smarty->assign('audit_cn', $audit_cn);
	$smarty->assign('audit_style', $audit_style);
	
	//�Ƿ����
	$t = time() < $setmeal['endtime'] ? 0 : 1;
	$smarty->assign('expire', $t);
	
	//δ����Ϣ
	$msg_unread_num = z_message_unread_num($_SESSION['uid']);
	$smarty->assign('msg_unread_num', $msg_unread_num);
	
	//�Ƿ��һ��
	if ($company['showed'] == 0) {
		updatetable('hr_company_profile', array('showed'=>1), array('uid'=>$company['uid']));
	}
	
	//���������Ƿ�����
	$baseinfo_complete = ($company['trade'] && $company['contact'] && $company['telephone'] && $company['email'] && $company['address'] && $company['website']) ? 1 : 0;
	$smarty->assign('baseinfo_complete', $baseinfo_complete);
	
	$smarty->assign('title','��ҵ��Ա���� - '.$_CFG['site_name']);
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