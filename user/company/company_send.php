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
		showmsg('���������ڣ�',0);
	}
	
	//�Ƿ����ؼ���
	$downInfo = $db->get_total("SELECT COUNT(*) num FROM hr_company_down_resume WHERE resume_uid='$resume_uid' AND company_uid='$uid'");
	$downed = empty($downInfo) ? 2 : 3;#2δ���ء�3������
	
	$resume = z_resume($resume_uid);
	if (!empty($resume)) {
		$t = assing_resume($resume_uid, 2);
		if ($t > 0) {

			$memberInfo = z_member($uid);
			//���ʼ�
			$to = trim($_POST['to']);//���͵�ַ
			$title = trim($_POST['title']);//�ʼ�����
			$content = $_POST['content'];//�ʼ���������
			//����
			
			//+send email �ⷢ����
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
			
			$link[0]['text'] = "����";
			$link[0]['href'] = 'company_index.php';
			showmsg("���ͳɹ���",2,$link);
		} else {
			showmsg('������������',0);
		}
	} else {
		showmsg('���������ڻ���ɾ����',0);
	}
}

		

//ת������
$company = get_company($_SESSION['uid']);
$company['contents_tmp'] && $company['contents'] = $company['contents_tmp'];
$smarty->assign('title','ת������ - ��ҵ��Ա���� - '.$_CFG['site_name']);
$smarty->assign('company_profile',$company);
$smarty->display('member_company/company_send.htm');		
