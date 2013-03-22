<?php
/*企业找回密码*/

define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

	
$act = isset($_GET['act']) ? trim($_GET['act']) : (isset($_POST['act'])? trim($_POST['act']) : 'getpwd');

//发送邮件提示
if ($act == 'tip') {
	$tab = isset($_GET['tab']) ? trim($_GET['tab']) : '';
	$username = trim($_GET['username']);
	$member = z_member_username($username, 1);
	
	$smarty->assign('tab', $tab);
	$smarty->assign('member', $member);
	$smarty->assign('title','找回密码 - '.$_CFG['site_name']);
	$smarty->display('user/get_password_tip.htm');
}

//重置密码
elseif ($act == 'set') {
	
	if (!empty($_POST)) {
		$uid = intval($_POST['uid']);
		$code = trim($_POST['code']);
		if ($uid && $code) {
			$member = z_member($uid);
			if ($member) {
				if (!empty($member['code_password']) && $member['code_password'] == $code) {
					$password = trim($_POST['password']);
					$password2 = trim($_POST['password2']);
					
					if ($password && $password == $password2) {
						updatetable('hr_members', array('password'=>md5($password), 'code_password'=>''), array('uid'=>$uid));
						z_goto('get_password.php?act=tip&tab=set');
					} else {
						showmsg("两次密码输入不相同！",0,$link);
					}
				} else {
					showmsg('该链接已失效，请重新通过找回密码的方式，进行修改！', 0, array(0=>array('text'=>'重新获取', 'href'=>'get_password.php?act=getpwd')));
				}
			}
		}
		z_goto($_CFG['site_domain'].'/404.php');
	} else {
		$uid = intval($_GET['uid']);
		$code = trim($_GET['code']);
		if ($uid && $code) {
			$member = z_member($uid);
			if ($member) {
				if (!empty($member['code_password']) && $member['code_password'] == $code) {
					$smarty->assign('uid', $uid);
					$smarty->assign('code', $code);
					$smarty->assign('member', $member);
					$smarty->assign('title','重置密码 - '.$_CFG['site_name']);
					$smarty->display('user/get_password_set.htm');
				} else {
					showmsg('该链接已失效，请重新通过找回密码的方式，进行修改！', 0, array(0=>array('text'=>'重新获取', 'href'=>'get_password.php?act=getpwd')));
				}
			}
		}
		z_goto($_CFG['site_domain'].'/404.php');
	}
	
}

//忘记密码
elseif ($act == 'getpwd') {
	if (!empty($_POST)) {	
		//参数
		$username = trim($_POST['username']);
		$postcaptcha = strtolower(trim($_POST['postcaptcha']));
		$postcaptcha_sys = strtolower($_SESSION['imageCaptcha_content']);
		
		if (empty($username)) {
			showmsg('用户名不能为空！',0);
		}
		
		if (empty($postcaptcha) || ($postcaptcha != $postcaptcha_sys)) {
			showmsg('验证码不正确！',0);
		}
		
		$member = z_member_username($username, 1);
		if (empty($member)) {
			showmsg('用户名不正确！',0);
		}
		
		$code = z_randstr(6);
		updatetable('hr_members', array('code_password'=>$code), array('uid'=>$member['uid']));
		
		//发送邮件
		//+send mail 企业重置密码
		$mailArr = array(
			'to'=>$member['email'],
		);
		$data = array(
			'username'=>$member['username'],
			'url_code'=>'http://hr.ofweek.com/user/get_password.php?act=set&uid='.$member['uid'].'&code='.$code,
		);
		z_mail('get_password', $mailArr, $data);
		//end
		
		z_goto('get_password.php?act=tip&username='.$username);
	} else {
		$smarty->assign('title','找回密码 - '.$_CFG['site_name']);
		$smarty->display('user/get_password.htm');
	}
}
?>