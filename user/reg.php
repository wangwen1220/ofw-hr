<?php
/*企业注册*/

define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

if (!empty($_POST)) {
	
	//参数
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$password2 = trim($_POST['password2']);
	$email = trim($_POST['email']);
	$companyname = trim($_POST['companyname']);
	$contact = trim($_POST['contact']);
	$telephone = trim($_POST['telephone']);
	
	//检查
	if (empty($username) || empty($password) || empty($password2) || empty($email) || empty($companyname) || empty($contact) || empty($telephone)) {
		showmsg("注册失败,数据不完整！",0);
	}
	//用户名是否存在	
	$exist = z_member_exist($username, 1);
	if ($exist) {
		showmsg("注册失败,用户名已存在！",0);
	}
	if ($password != $password2) {
		showmsg("注册失败,两次密码输入不相同！",0);
	}
	if (empty($_POST['readitem'])) {
		showmsg("请先阅读注册协议！",0);
	}
	
	//写数据库
	//members会员表
	$code = md5(z_randstr(6));
	$time = time();
	$ip = getip();
	$members['username'] = $username;
	$members['email'] = $email;
	$members['mobile'] = $telephone;
	$members['pwd_hash'] = '';
	$members['password'] = md5($password);
	$members['reg_time'] = $time;
	$members['reg_ip'] = $ip;
	$members['last_login_time'] = $time;
	$members['last_login_ip'] = '';
	$members['qq_openid'] = '';
	$members['status'] = 1;
	$members['login_num'] = 1;
	$members['utype'] = 1;
	$members['code_email'] = $code;
	$uid = inserttable(table("members"), $members, 1);
	
	if (!$uid) {
		$link[0]['text'] = "注册";
		$link[0]['href'] = $_CFG['site_domain'].'/user/reg.php';
		showmsg("注册失败！",0,$link);
	}
	
	//members_info会员个人资料表
	$membersInfo['uid'] = $uid;
	$membersInfo['realname'] = '';
	$membersInfo['sex'] = '';
	$membersInfo['birthday'] = '';
	$membersInfo['addresses'] = '';
	$membersInfo['phone'] = $telephone;
	$membersInfo['qq'] = '';
	$membersInfo['msn'] = '';
	$membersInfo['profile'] = '';
	$membersInfo['avatars'] = '';
	inserttable(table("members_info"), $membersInfo);
	
	//company_profile公司基本信息表
	$companyProfile['uid'] = $uid;
	$companyProfile['tpl'] = '';
	$companyProfile['companyname'] = $companyname;
	$companyProfile['nature'] = 0;
	$companyProfile['nature_cn'] = '';
	$companyProfile['trade'] = 0;
	$companyProfile['trade_cn'] = '';
	$companyProfile['district'] = 0;
	$companyProfile['sdistrict'] = 0;
	$companyProfile['district_cn'] = '';
	$companyProfile['street'] = 0;
	$companyProfile['street_cn'] = '';
	$companyProfile['officebuilding'] = 0;
	$companyProfile['officebuilding_cn'] = '';
	$companyProfile['scale'] = 0;
	$companyProfile['scale_cn'] = '';
	$companyProfile['registered'] = '';
	$companyProfile['currency'] = '';		
	$companyProfile['address'] = '';
	$companyProfile['contact'] = $contact;
	$companyProfile['telephone'] = $telephone;
	$companyProfile['email'] = $email;
	$companyProfile['website'] = '';
	$companyProfile['license'] = '';
	$companyProfile['certificate_img'] = '';
	$companyProfile['logo'] = '';
	$companyProfile['contents'] = '';
	$companyProfile['audit'] = 2;
	$companyProfile['map_x'] = '';
	$companyProfile['map_y'] = '';
	$companyProfile['map_zoom'] = 0;
	$companyProfile['addtime'] = $time;
	$companyProfile['refreshtime'] = $time;
	$companyProfile['contents_tmp'] = '';
	$companyProfile['fax'] = '';
	inserttable(table("company_profile"), $companyProfile);	
	
	//members_setmeal会员服务套餐表
	//$setmeal_id = $_CFG['reg_service'];
	$setmeal_id = 2;
	$setmeal = z_config_setmeal($setmeal_id);
	
	if (!empty($setmeal)) {
		$membersSetmeal['effective'] = 1;
		$membersSetmeal['uid'] = $uid;
		$membersSetmeal['setmeal_id'] = $setmeal['id'];
		$membersSetmeal['setmeal_name'] = $setmeal['setmeal_name'];
		$membersSetmeal['days'] = $setmeal['days'];
		$membersSetmeal['expense'] = $setmeal['expense'];
		$membersSetmeal['jobs_ordinary'] = $setmeal['jobs_ordinary'];
		$membersSetmeal['download_resume_ordinary'] = $setmeal['download_resume_ordinary'];
		$membersSetmeal['download_resume_senior'] = $setmeal['download_resume_senior'];
		$membersSetmeal['interview_ordinary'] = $setmeal['interview_ordinary'];
		$membersSetmeal['interview_senior'] = $setmeal['interview_senior'];
		$membersSetmeal['talent_pool'] = $setmeal['talent_pool'];
		$membersSetmeal['added'] = $setmeal['added'];
		$membersSetmeal['resume_search'] = $setmeal['resume_search'];
		$membersSetmeal['starttime'] = time();
		$membersSetmeal['endtime'] = $membersSetmeal['starttime'] + $membersSetmeal['days']*24*60*60;			
		inserttable(table("members_setmeal"), $membersSetmeal);	
	}
	
	//+send mail 企业注册成功
	$mailArr = array(
		'to'=>$email
	);
	$data = array(
		'companyname'=>$companyProfile['companyname'],
		'username'=>$members['username'],
		'password'=>$password,
		'activeurl'=>'',
	);
	z_mail('company_register', $mailArr, $data);
	//end
	//+sene message 企业注册成功
	z_message('company_register', $uid);
	//end
	
	//写session
	$_SESSION['uid'] = $uid;
	$_SESSION['username'] = $username;
	$_SESSION['utype'] = 1;
	
	//注册成功
	$link[0]['text'] = "完善资料";
	$link[0]['href'] = $_CFG['site_domain'].'/user/company/company_info.php?act=company_profile_edit';
	showmsg("注册成功！",2,$link);
}

$smarty->assign('title','企业注册 - '.$_CFG['site_name']);
$smarty->display('user/reg_company.htm');