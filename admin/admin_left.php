<?php
 /*
 * 74cms �����������
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act=!empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if($act){

	//����˹�˾
	$audit2_com_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_company_profile WHERE audit=2 OR audit=4");
	$smarty->assign('audit2_com_num', $audit2_com_num);
	
	//�����ְλ
	$audit2_job_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE (audit=2 OR audit=4) AND (company_audit=1 OR company_audit=4)");
	$smarty->assign('audit2_job_num', $audit2_job_num);
	
	$smarty->display("sys/admin_left_{$act}.htm");
}
?>