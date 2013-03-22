<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"info");

//反馈
if ($act == 'feedback') {
	$smarty->assign('title','意见反馈 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company);
	$smarty->display('member_company/company_feedback.htm');
}

//保存反馈
if ($act == 'feedback_save') {
	$setsqlarr['title']=trim($_POST['title']);
	$setsqlarr['infotype']=intval($_POST['infotype']);
	$setsqlarr['feedback']=trim($_POST['feedback']);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['usertype']=$_SESSION['utype'];
	$setsqlarr['username']=$_SESSION['username'];
	$setsqlarr['addtime']=$timestamp;
	
	//处理附件
	$setsqlarr['file'] = file_upload('file', $_SESSION['uid'], 'gif/jpg/bmp/png',4, 1*1024);
	!inserttable('hr_feedback',$setsqlarr)?showmsg("添加失败！",0):showmsg("添加成功，请等待管理员回复！",2);
}

//列表
elseif ($act == 'list') {
	$uid = $_SESSION['uid'];
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$num = isset($_GET['num']) ? max(intval($_GET['num']), 10) : 10;
	$limit_start = ($page - 1) * $num;
	
	//总数
	$sql = "SELECT COUNT(*) num FROM hr_feedback WHERE uid='$uid' AND infotype=9";
	$total = $db->get_total($sql);
	$smarty->assign('total', $total);
	
	//列表数据	
	$sql = "SELECT * FROM hr_feedback 
		WHERE uid='$uid' AND infotype=9
		ORDER BY addtime DESC
		LIMIT $limit_start,$num";
	$messageList = $db->getall($sql);
	foreach ($messageList as $key=>$value) {
		$messageList[$key]['addtime_cn'] = date('Y-m-d', $value['addtime']);
		
	}
	$smarty->assign('messageList', $messageList);
	
	//分页
	$pageHTML = dPage_2($total, $page, 'company_message.php?act=list', $num);
	$smarty->assign('pageHTML', $pageHTML);
	$smarty->assign('page', $page);
	$smarty->assign('title','系统消息 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company);
	$smarty->display('member_company/company_message_list.htm');
}

//删除
elseif ($act == 'del') {
	$uid = $_SESSION['uid'];
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$id = intval($_GET['id']);
	
	$db->query("DELETE FROM hr_feedback WHERE id=$id AND uid=$uid");
	$link[0]['text'] = "返回";
	$link[0]['href'] = "company_message.php?act=list&page=$page";
	showmsg("删除成功！",2,$link);
}

//详细内容
elseif ($act == 'view') {
	$uid = $_SESSION['uid'];
	$id = intval($_GET['id']);
	
	$message = z_feedback($id, $uid);
	if (empty($message)) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = "company_message.php?act=list";
		showmsg("改消息不存在！",2,$link);
	}
	
	//已阅览
	updatetable('hr_feedback', array('readed'=>1), array('id'=>$id));	
	
	$smarty->assign('title','系统消息 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('message',$message);
	$smarty->display('member_company/company_message_view.htm');
}