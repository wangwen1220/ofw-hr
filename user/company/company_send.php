<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"info");

$resume_uid = $_GET['uid'];
$smarty->assign('uid', $resume_uid);

if (!empty($_POST)) {
	$uid = $_SESSION['uid'];
	$resume_uid = intval($_POST['uid']);
	
	if (empty($resume_uid)) {
		showmsg('简历不存在！',0);
	}
	
	//是否下载简历
	$downInfo = $db->get_total("SELECT COUNT(*) num FROM hr_company_down_resume WHERE resume_uid='$resume_uid' AND company_uid='$uid'");
	$downed = empty($downInfo) ? 2 : 3;#2未下载、3已下载
	
	$resume = z_resume($resume_uid);
	if (!empty($resume)) {
		$t = assing_resume($resume_uid, 2);
		if ($t > 0) {

			$memberInfo = z_member($uid);
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
				'from'=>$memberInfo['email'],
				'fromName'=>$memberInfo['realname']
			);
			$data = array(
				'downed'=>$downed,
				'resume_uid'=>$resume_uid
			);
			z_mail('free_resume_re', $mailArr, $data);
			//end
			
			$link[0]['text'] = "返回";
			$link[0]['href'] = 'company_index.php';
			showmsg("发送成功！",2,$link);
		} else {
			showmsg('简历不完整！',0);
		}
	} else {
		showmsg('简历不存在或已删除！',0);
	}
}

		

//转发简历
$company = get_company($_SESSION['uid']);
$company['contents_tmp'] && $company['contents'] = $company['contents_tmp'];
$smarty->assign('title','转发简历 - 企业会员中心 - '.$_CFG['site_name']);
$smarty->assign('company_profile',$company);
$smarty->display('member_company/company_send.htm');		
