<?php
 /*
 * 74cms 管理中心左侧
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
$act=!empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if($act){

	//待审核公司
	$audit2_com_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_company_profile WHERE audit=2 OR audit=4");
	$smarty->assign('audit2_com_num', $audit2_com_num);
	
	//待审核职位
	$audit2_job_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE (audit=2 OR audit=4) AND (company_audit=1 OR company_audit=4)");
	$smarty->assign('audit2_job_num', $audit2_job_num);
	
	$smarty->display("sys/admin_left_{$act}.htm");
}
?>