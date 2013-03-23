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
if(!defined('IN_QISHI')) die('Access Denied!');
$page_select="user";
require_once(dirname(dirname(dirname(__FILE__))).'/include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	if ($_SESSION['uid']=='' || $_SESSION['username']=='')
	{
		$forward = '';
		$current_req = $_SERVER['REQUEST_URI'];
		if($current_req == '/user/company/company_info.php?act=company_profile_edit' 
			|| $current_req == '/user/company/company_jobs.php?act=addjobs'	
			|| $current_req == '/user/company/company_info.php?act=company_auth'	
			|| $current_req == '/user/company/company_info.php?act=company_logo'	
		){
			$forward = '?forward='.urlencode($_CFG['site_domain']). request_url();
		}
		z_goto($_CFG['site_domain'].'/hrlogin/'. $forward);
	}
	elseif ($_SESSION['utype']!='1') 
	{
	$link[0]['text'] = "会员中心";
	$link[0]['href'] = url_rewrite('QS_login');
	showmsg('您访问的页面需要 企业会员 登录！系统将跳转到 个人会员 中心',1,$link);
	}
	$act = !empty($_GET['act']) ? trim($_GET['act']) : 'index';
	$smarty->cache = false;
	$user=get_user_info($_SESSION['uid']);
	if ($user['status']=="2" && $act!='index' && $act!='user_status'  && $act!='user_status_save') 
	{
		$link[0]['text'] = "设置账号状态";
		$link[0]['href'] = 'company_user.php?act=user_status';
		$link[1]['text'] = "返回会员中心首页";
		$link[1]['href'] = 'company_index.php?act=';
	exit(showmsg('您的账号处于暂停状态，请先设为正常后进行操作！',1,$link));	
	}
	elseif (empty($user))
	{
	unset($_SESSION['utype'],$_SESSION['uid'],$_SESSION['username']);
	z_goto($_CFG['site_domain']);
	}
	
$setmeal = z_setmeal($_SESSION['uid']);
$smarty->assign('setmeal',$setmeal);	
?>
