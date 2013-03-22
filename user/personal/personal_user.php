<?php
/*
 * �������ϼ����� 2012��10��11�� 11:13:24
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');

$action = array('sms','pwd','feedback','logout');

if ($act == 'sms'){
	
	$uid = $_SESSION['uid'];
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$num = isset($_GET['num']) ? max(intval($_GET['num']), 10) : 10;
	$limit_start = ($page - 1) * $num;
	
	//����
	$sql = "SELECT COUNT(*) num FROM hr_feedback WHERE uid='$uid' AND infotype=9";
	$total = $db->get_total($sql);
	$smarty->assign('total', $total);
	
	//�б�����	
	$sql = "SELECT * FROM hr_feedback 
		WHERE uid='$uid' AND infotype=9
		ORDER BY addtime DESC
		LIMIT $limit_start,$num";
	$messageList = $db->getall($sql);
	foreach ($messageList as $key=>$value) {
		$messageList[$key]['addtime_cn'] = date('Y-m-d', $value['addtime']);
		
	}
	$smarty->assign('messageList', $messageList);
	
	//��ҳ
	$pageHTML = dPage_2($total, $page, 'personal_user.php?act=sms', $num);
	$smarty->assign('pageHTML', $pageHTML);
	$smarty->assign('page_num', $page);
	$smarty->assign('title','ϵͳ��Ϣ - ��Ա���� - '.$_CFG['site_name']);
	$html_body = 'personal_sms';
	$smarty->assign('page_title','��������-ϵͳ��Ϣ�б�');
}

//��ϸ����
elseif ($act == 'sms_view') {
	$uid = $_SESSION['uid'];
	$id = intval($_GET['id']);
	
	$message = z_feedback($id, $uid);
	if (empty($message)) {
		$link[0]['text'] = "����";
		$link[0]['href'] = "personal_user.php?act=sms";
		showmsg("����Ϣ�����ڣ�",2,$link);
	}
	
	//������
	updatetable('hr_feedback', array('readed'=>1), array('id'=>$id));	
	
	$smarty->assign('title','ϵͳ��Ϣ - ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('message',$message);
	$html_body = 'personal_sms_view';
	$smarty->assign('page_title','��������-ϵͳ��Ϣ��ϸ');
}

//ɾ��
elseif ($act == 'sms_del') {
	$uid = $_SESSION['uid'];
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$id = intval($_GET['id']);
	
	$db->query("DELETE FROM hr_feedback WHERE id=$id AND uid=$uid");
	$link[0]['text'] = "����";
	$link[0]['href'] = "personal_user.php?act=sms&page=$page";
	showmsg("ɾ���ɹ���",2,$link);
}

elseif ($act == 'pwd'){
	$html_body = 'personal_pwd';
	$smarty->assign('page_title','��������-�޸�����');
}
elseif ($act == 'feedback'){

/*	
	$get_feedback=get_feedback($_SESSION['uid']);
	if (count($get_feedback)>=5) 
	{
	showmsg('������Ϣ���ܳ���5����',1);
	exit();
	}
	$setsqlarr['infotype']=intval($_POST['infotype']);
	$setsqlarr['feedback']=trim($_POST['feedback'])?trim($_POST['feedback']):showmsg('����д���ݣ�',1);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['usertype']=$_SESSION['utype'];
	$setsqlarr['username']=$_SESSION['username'];
	$setsqlarr['addtime']=$timestamp;
	write_memberslog($_SESSION['uid'],2,7001,$_SESSION['username'],"��ӷ�����Ϣ");
	!inserttable(table('feedback'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա�ظ���",2);
*/	
	if ($_POST['submit']){
		$setsqlarr['title']=intval($_POST['title']);
		$setsqlarr['infotype']=intval($_POST['infotype']);
		$setsqlarr['feedback']=trim($_POST['feedback']);
		$setsqlarr['uid']=$_SESSION['uid'];
		$setsqlarr['usertype']=$_SESSION['utype'];
		$setsqlarr['username']=$_SESSION['username'];
		$setsqlarr['addtime']=$timestamp;
		
		//������
		$setsqlarr['file'] = file_upload('file', $_SESSION['uid'], 'gif/jpg/bmp/png',4, 1*1024);
		!inserttable(table('feedback'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա�ظ���",2);
	}
	
	$html_body = 'personal_feedback';
	$smarty->assign('page_title','��������-�������');
}
elseif ($act == 'logout'){
#	echo 'ע��';
	header("Location: http://www.ofweek.com/user/userLogout.do?returnurl=".$_CFG['site_domain']."/");	
#	exit;
}

$smarty->assign('html_body',$html_body);
$smarty->display('member_personal/personal_index.htm');
unset($smarty);
?>