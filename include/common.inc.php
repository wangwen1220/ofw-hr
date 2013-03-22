<?php
 /*
 * 74cms 共用配置文件
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
session_save_path(dirname(__FILE__).'/../data/session/');
session_start();
if(!defined('IN_QISHI')) exit('Access Denied!');
define('QISHI_ROOT_PATH',dirname(dirname(__FILE__)).'/');
ini_set('display_errors', 0);
error_reporting(E_ALL^E_NOTICE);
require_once(QISHI_ROOT_PATH.'data/config.php');
require_once(QISHI_ROOT_PATH.'data/mail_config.php');
header("Content-Type:text/html;charset=".QISHI_CHARSET);
require_once(QISHI_ROOT_PATH.'include/common.fun.php');
require_once(QISHI_ROOT_PATH.'include/74cms_version.php');
$QSstarttime=exectime();
if (!empty($_GET))
{
$_GET  = addslashes_deep($_GET);
}
if (!empty($_POST))
{
$_POST = addslashes_deep($_POST);
}
$_COOKIE = addslashes_deep($_COOKIE);


if ($_SESSION['admin_id'] && $_SESSION['auth_uid']) {//授权用户
	$_SESSION['uid'] = $_SESSION['auth_uid'];
	$_SESSION['username'] = $_SESSION['auth_username'];
	$_SESSION['utype'] = $_SESSION['auth_utype'];
	
	$z_auth = 1;
} elseif ($_SESSION['utype'] == 1) {
	;//企业用户
} else {//个人用户-正常登录
	//先清除
	$_SESSION['uid'] = '';
	$_SESSION['username'] = '';
	$_SESSION['utype'] = '';
	
	//OFweek Cookie登录
	$_SESSION['uid'] = $_SESSION['username'] = $_SESSION['utype'] = '';
	$ofweek_member = explode('NP',$_COOKIE['www_ofweekmember']);
	$ofweek_username = $ofweek_member[0];
	$ofweek_pwd_all = explode('ofweek',$ofweek_member[14]);
	$ofweek_pwd = explode('=',$ofweek_pwd_all[1]);
	$ofweek_password = strtr($ofweek_pwd[0]," ","+");//读取COOKIE的时候会把加号转换为空格
	
	$username = $ofweek_member[0];
	$password = base64_decode($ofweek_password);
	
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	$userInfo = $db->getone("SELECT * FROM hr_members WHERE username='$username'");
	if (!empty($userInfo)) {
		if (time() - $userInfo['last_login_time'] > 10*60) {
			$db->query("UPDATE hr_members SET last_login_time='".time()."',mail_refresh=0,mail_refresh_time=0 WHERE uid='".$userInfo['uid']."' LIMIT 1");
		}
		$_SESSION['uid'] = $userInfo['uid'];
		$_SESSION['username'] = $userInfo['username'];
		$_SESSION['utype'] = $userInfo['utype'];
	}
	$db->close();
#	unset($db);
	//end
}


$_REQUEST  = addslashes_deep($_REQUEST);
PHP_VERSION > '5.1'?date_default_timezone_set("PRC"):'';
$timestamp = time();
$online_ip=getip();
$_NAV=get_cache('nav');
$_PAGE=get_cache('page');
$_CFG=get_cache('config');
$_CFG['version']=QISHI_VERSION;
$_CFG['web_logo']=$_CFG['web_logo']?$_CFG['web_logo']:'logo.gif';
$_CFG['upfiles_dir']=$_CFG['site_dir']."data/".$_CFG['updir_images']."/";
$_CFG['thumb_dir']=$_CFG['site_dir']."data/".$_CFG['updir_thumb']."/";
$_CFG['resume_photo_dir']=$_CFG['site_dir']."data/".$_CFG['resume_photo_dir']."/";
$_CFG['resume_photo_dir_thumb']=$_CFG['site_dir']."data/".$_CFG['resume_photo_dir_thumb']."/";
$_CFG['site_template']=$_CFG['site_dir'].'templates/'.$_CFG['template_dir'];
$_CFG['subsite_id']=0;

//+A zcj
$_CFG['cur_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$_CFG['referer'] = $_SERVER['HTTP_REFERER'];
//end

$mypage=$_PAGE[$alias];
$mypage['tag']?$page_select=$mypage['tag']:'';
require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
	if ($_CFG['isclose'])
	{
				$smarty->assign('info',$_CFG['close_reason']=$_CFG['close_reason']?$_CFG['close_reason']:'站点暂时关闭...');
				$smarty->display('warning.htm');
				exit();
	}
	if ($_CFG['filter_ip'] && check_word($_CFG['filter_ip'],$online_ip))
	{
			$smarty->assign('info',$_CFG['filter_ip_tips']);
			$smarty->display('warning.htm');
			exit();
	}
	
//+A zcj
$smarty->assign('member_uid',$_SESSION['uid']);
$smarty->assign('member_username',$_SESSION['username']);
$smarty->assign('member_utype',$_SESSION['utype']);
$smarty->assign('z_auth', intval($z_auth));
//end


require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$cats = get_parent_cagegory('category_jobs');
$smarty->assign('jobcategory',$cats);


//+zcj 2013.3.5
//计划任务
execution_crons();
//end
unset($db);
?>