<?php
/*
 * 外发简历 2012年10月10日 10:22:13
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');

if ($resume_status != 1){
	$returnlink[] = array('text'=>'返回','href'=>$_CFG['site_dir'].'user/personal/');
	showmsg('请等待简历审核通过', 2,$returnlink);
	exit;
}

if ($_POST['submit']){
	
	$resume = z_resume($_SESSION['uid']);
	//发邮件
	$to = trim($_POST['to']);//发送地址
	$title = trim($_POST['title']);//邮件标题
	$content = $_POST['content'];//邮件顶部内容
	//发送
	
	//+send email 外发简历
	$mailArr = array(
		'to'=>$to,
		'subject'=>$title,
		'body'=>$content,
		'from'=>$resume['email'],
		'fromName'=>$resume['fullname']
	);
	z_mail('free_resume', $mailArr);
	//end
	
	$link[0]['text'] = "返回";
	$link[0]['href'] = 'personal_send.php';
	showmsg("发送成功！",2,$link);
}


$html_body = 'personal_send';
$smarty->assign('html_body',$html_body);
$smarty->assign('page_title','个人中心-外发简历');
$smarty->display('member_personal/personal_index.htm');

unset($smarty);
?>