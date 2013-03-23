<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
$act = trim($_GET['act']);
$time = time();

//�����б�
if ($act == 'list') {
	
	//Ȩ��
	check_permissions($_SESSION['admin_purview'], "com_show");
	
	//��ҵ����
	$tradeCate = get_category_zt('QS_trade');
	$smarty->assign('tradeCate', $tradeCate);
	
	//�ײ�
	$setmealCate = get_setmeal();
	$smarty->assign('setmealCate', $setmealCate);
	
	//����
	$companyList = array();
	$search = array();
	$from = isset($_GET['from']) ? trim($_GET['from']) : '';
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
	$limit_start = ($page - 1) * $num;
		
	$search['uid'] = isset($_GET['uid']) ? intval($_GET['uid']) : '';
	$search['username'] = isset($_GET['username']) ? trim($_GET['username']) : '';
	$search['companyname'] = isset($_GET['companyname']) ? trim($_GET['companyname']) : '';
	$search['regtime_from'] = isset($_GET['regtime_from']) ? trim($_GET['regtime_from']) : '';
	$search['regtime_to'] = isset($_GET['regtime_to']) ? trim($_GET['regtime_to']) : '';
	$search['endtime_from'] = isset($_GET['endtime_from']) ? trim($_GET['endtime_from']) : '';
	$search['endtime_to'] = isset($_GET['endtime_to']) ? trim($_GET['endtime_to']) : '';
	$search['trade'] = isset($_GET['trade']) ? intval($_GET['trade']) : 0;
	$search['setmeal'] = isset($_GET['setmeal']) ? intval($_GET['setmeal']) : 0;
	$search['expire'] = isset($_GET['expire']) ? intval($_GET['expire']) : 0;
	$search['sort'] = isset($_GET['sort']) ? trim($_GET['sort']) : '';
	$search['company_type'] = isset($_GET['company_type']) ? intval($_GET['company_type']) : 0;
	if ($from == 'audit') {
		$search['audit'] = 2;
	} else {
		$search['audit'] = isset($_GET['audit']) ? intval($_GET['audit']) : 0;
	}
	$addsql = '';
	
	if ($search['uid']) {
		$t = $search['uid'];
		$addsql .= " AND m.uid='$t'";
	}
	if ($search['username']) {
		$t = $search['username'];
		$addsql .= " AND m.username='$t'";
	}
	if ($search['companyname']) {
		$t = $search['companyname'];
		$addsql .= " AND cp.companyname LIKE '%$t%'";
	}
	if ($search['regtime_from']) {
		$t = strtotime($search['regtime_from']);
		$addsql .= " AND m.reg_time>='$t'";
	}
	if ($search['regtime_to']) {
		$t = strtotime($search['regtime_to'].' 23:59:59');
		$addsql .= " AND m.reg_time<='$t'";
	}
	if ($search['endtime_from']) {
		$t = strtotime($search['endtime_from']);
		$addsql .= " AND ms.endtime>='$t'";
	}
	if ($search['endtime_to']) {
		$t = strtotime($search['endtime_to'].' 23:59:59');
		$addsql .= " AND ms.endtime<='$t'";
	}
	if ($search['audit']) {
		$t = $search['audit'];
		$addsql .= $t == 2 ? " AND (cp.audit='$t' || cp.audit=4)" : " AND cp.audit='$t'";
	}
	if ($search['expire']) {
		$t = $search['expire'];
		$addsql .= $t == 2 ? " AND (ms.endtime>=$time)" : " AND (ms.endtime<$time)";
	}
	if ($search['trade']) {
		$t = $search['trade'];
		$addsql .= " AND cp.trade LIKE ',$t,'";
	}
	if ($search['setmeal']) {
		$t = $search['setmeal'];
		$addsql .= " AND ms.setmeal_id =$t";
	}
	
	if ($search['company_type']) {
		$t = $search['company_type'];
		$addsql .= " AND cp.company_type=$t";
	}
	
	if ($search['sort']) {
		$t = $search['sort'];
		$sortsql = "ORDER BY m.$t DESC";
	} else {
		$sortsql = "ORDER BY m.reg_time DESC";
	}
	
	
	//�б�
	//�û�����ע��ʱ�䣬��˾״̬����˾������ϵ�ˣ���ϵ�绰������ʱ��
	$total = $db->get_total("SELECT COUNT(m.uid) AS num
		FROM hr_members m
		LEFT JOIN hr_company_profile cp ON cp.uid=m.uid
		LEFT JOIN hr_members_setmeal ms ON ms.uid=m.uid
		WHERE m.utype=1$addsql");
	$sql = "SELECT m.uid,m.username,m.reg_time,cp.audit,cp.companyname,cp.contact,cp.telephone,cp.company_type,ms.setmeal_id,ms.setmeal_name,ms.endtime,cp.nature_cn,cp.certificate_img
		FROM hr_members m
		LEFT JOIN hr_company_profile cp ON cp.uid=m.uid
		LEFT JOIN hr_members_setmeal ms ON ms.uid=m.uid
		WHERE m.utype=1$addsql
		$sortsql
		LIMIT $limit_start,".$num;
	$companyList = $db->getall($sql);
	foreach ($companyList as $key=>$value) {
		#��˾״̬
		if ($value['audit']==1) {
			$companyList[$key]['audit_cn'] = '<span class="f_gre">���ͨ��</span>';
		} elseif ($value['audit']==2 || $value['audit']==4) {
			$companyList[$key]['audit_cn'] = '<span class="f_gray">�ȴ����</span>';
		} elseif ($value['audit']==3) {
			$companyList[$key]['audit_cn'] = '<span class="f_red">���δͨ��</span>';
		}
		#ע��ʱ��
		$companyList[$key]['reg_time'] = $value['reg_time'] ? date('Y-m-d', $value['reg_time']): '';
		#����ʱ��
		$companyList[$key]['endtime'] = $value['endtime'] ? date('Y-m-d', $value['endtime']): '';
		if ($value['setmeal_id'] == 3) {
			$companyList[$key]['setmeal_name_cn'] = '<span class="f_gre">'.$value['setmeal_name'].'</span>';
		} else {
			$companyList[$key]['setmeal_name_cn'] = '<span>'.$value['setmeal_name'].'</span>';
		}
	}
	
	//��ҳ
	$page_url = rebuild_url('page');
	$pageHTML = dPage_2($total, $page, $page_url, $num);
	$smarty->assign('pageHTML', $pageHTML);
		
	$returnurl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$smarty->assign('returnurl', base64_encode($returnurl));
	$smarty->assign('from', $from);
	$smarty->assign('search', $search);
	$smarty->assign('total', $total);
	$smarty->assign('num', $num);
	$smarty->assign('page', $page);
	$smarty->assign('num_url', rebuild_url('num'));
	$smarty->assign('sort_url', rebuild_url('sort'));
	$smarty->assign('companyList', $companyList);
	$smarty->assign('pageheader',"�����б�");
	$smarty->display('z/company_list.htm');
}

//����
if ($act == 'do') {
	$optype = $_POST['optype'];
	
	//���
	if ($optype == 'audit_1' || $optype == 'audit_3') {
		$audit = ($optype == 'audit_1') ? 1 : 3;
		$returnurl = base64_decode($_POST['returnurl']);
		
		if (empty($_POST['uid'])) {
			adminmsg("��ѡ���û�", 0);
		}
		
		z_admin_company_audit($_POST['uid'], $audit);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $returnurl;
		adminmsg('�����ɹ���',2);
	}
	
	//ɾ��
	elseif ($optype == 'del') {
		if (empty($_POST['uid'])) {
			adminmsg("��ѡ���û�", 0);
		}
		
		z_admin_company_del($_POST['uid']);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $returnurl;
		adminmsg('ɾ���ɹ���',2);
	}
}

//Ȩ���趨
elseif ($act == 'setmeal') {
	$company_user=get_user($_GET['uid']);
	$setmeal = z_setmeal($company_user['uid']);
	
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('setmeal', $setmeal);
	$smarty->assign('pageheader',"Ȩ���趨");
	$smarty->display('z/company_edit_setmeal.htm');
}

//����Ȩ���趨
elseif ($act == 'edit_setmeal_save') {
	$uid = intval($_POST['uid']);
	$days = intval($_POST['days']);
	$setmeal_id = intval($_POST['reg_service']);
	$setmeal = z_setmeal($uid);

	if ($setmeal['setmeal_id'] == $setmeal_id && time() < $setmeal['endtime']) {
		$link[0]['text'] = "����";
		$link[0]['href'] = $_CFG['site_domain'].'/admin/company.php?act=setmeal&uid='.$uid;
		adminmsg('�ײ�δ���ڣ��������¿�ͨ��',2);
	}
	
	if ($setmeal) {
		
		$reg_service = $setmeal_id;
		
		if ($reg_service) {#����ײ�
			set_members_setmeal($uid, $reg_service);
		} else {#�޸��ײ���Ϣ
			$days = intval($_POST['days']);
			$data['resume_search'] = intval($_POST['resume_search']);
			$data['jobs_ordinary'] = $_POST['jobs_ordinary'];
			$data['download_resume_ordinary'] = $_POST['download_resume_ordinary'];
			if ($days) {
				if ($setmeal['endtime'] < $setmeal['starttime']) {
					$setmeal['endtime'] = $setmeal['starttime'] + $setmeal['days'] * (24*60*60);
				}
				$data['endtime'] += $setmeal['endtime'] + $days * (24*60*60);
			}
			updatetable('hr_members_setmeal', $data, array('uid'=>$uid));
		}
		
		$link[0]['text'] = "����";
		$link[0]['href'] = $_CFG['site_domain'].'/admin/company.php?act=setmeal&uid='.$uid;
		adminmsg('�����ɹ���',2);
	}	
}

//�༭
elseif($act == 'edit')
{	
	$uid=intval($_GET['uid']);
	$company = z_company($uid);
	
	$tradeCate = get_category_zt('QS_trade');
	$smarty->assign('tradeCate',$tradeCate);
	
	//��˾����
	$companyType = get_category_zt('QS_company_type');
	$smarty->assign('companyType', $companyType);
	
	//������ҵ
	$tradeCate = get_category_zt('QS_trade');
	$tradeCate_Left = $tradeCate_Right = array();
	foreach ($tradeCate as $key=>$value) {
		$t = trim($company['trade'], ',');
		$t = explode(",", $t);
		if (in_array($value['c_id'], $t)) {
			$tradeCate_Right[] = $value;
		} else {
			$tradeCate_Left[] = $value;
		}
		
	}
	$tmp = '';
	if($company['contents_tmp'] != ''){//��˾���
		$tmp = $company['contents'];
		$company['contents'] = $company['contents_tmp'];
		$company['contents_tmp'] = $tmp;
	}
	if($company['address_tmp'] != ''){//��ַ
		$tmp = $company['address'];
		$company['address'] = $company['address_tmp'];
		$company['address_tmp'] = $tmp;
	}
	if($company['contact_tmp'] != ''){//��ϵ��
		$tmp = $company['contact'];
		$company['contact'] = $company['contact_tmp'];
		$company['contact_tmp'] = $tmp;
	}
	if($company['telephone_tmp'] != ''){//�绰
		$tmp = $company['telephone'];
		$company['telephone'] = $company['telephone_tmp'];
		$company['telephone_tmp'] = $tmp;
	}
	if($company['website_tmp'] != ''){//��ҳ
		$tmp = $company['website'];
		$company['website'] = $company['website_tmp'];
		$company['website_tmp'] = $tmp;
	}

	//����������˾
	$sql = "SELECT uid,companyname,audit FROM ".table('company_profile')." WHERE TRIM(companyname)='".trim($company[companyname])."' AND uid!=$uid";
	$res = $db->getall($sql);
	foreach($res as $k=>$v){
		if($v['audit'] == '1'){
			$res[$k]['audit_cn'] = '���ͨ��';
		}
		else if($v['audit'] == '2'){
			$res[$k]['audit_cn'] = '�����';
		}
		else if($v['audit'] == '3'){
			$res[$k]['audit_cn'] = '��˲�ͨ��';
		}
		else if($v['audit'] == '4'){
			$res[$k]['audit_cn'] = '�޸Ĵ���';
		}
	}
	empty($res) && $res = '';
	$smarty->assign('samename', $res);

	
	$smarty->assign('year_now',date('Y', time()));
	$smarty->assign('tradeCate_Left', $tradeCate_Left);
	$smarty->assign('tradeCate_Right', $tradeCate_Right);
	$smarty->assign('company_profile', $company);
	$smarty->assign('pageheader',"��˾�޸�");
	$smarty->display('z/company_edit.htm');
} 

elseif ($act == 'edit_save_base') {
	$companyType = get_category_zt('QS_company_type');
	$tradeCate = get_category_zt('QS_trade');
	$uid=$_POST['uid'];
	$setsqlarr['uid']=intval($uid);
	
	//��ҵ����
	$setsqlarr['nature']=trim($_POST['nature'])?intval($_POST['nature']):showmsg('��ѡ����ҵ���ʣ�',1);
	foreach ($companyType as $value) {
		if ($value['c_id'] == $setsqlarr['nature']) {
			$setsqlarr['nature_cn'] = $value['c_name'];
			break;
		}
	}
		
	#��ҵ
	if (!empty($_POST['value_industry'])) {
		$trade_id_arr = array();
		$trade_name_arr = array();
		foreach ($_POST['value_industry'] as $key=>$value) {
			foreach ($tradeCate as $k=>$v) {
				if ($v['c_id'] == $value) {
					$trade_id_arr[] = $v['c_id'];
					$trade_name_arr[] = $v['c_name'];
				}
			}
		}
		$setsqlarr['trade'] = ",".implode(",", $trade_id_arr).",";
		$setsqlarr['trade_cn'] = ",".implode(",", $trade_name_arr).",";
	} else {
		showmsg('��û��ѡ����ҵ��',1);
	}
	//��˾����
	$setsqlarr['companyname'] = trim($_POST['companyname']);
	
	//��������
	$setsqlarr['register_year'] = intval($_POST['register_year']);
	$setsqlarr['register_month'] = intval($_POST['register_month']);
	$setsqlarr['register_day'] = intval($_POST['register_day']);
	
	//ע���ʽ�
	$setsqlarr['registered'] = intval($_POST['registered']);
	
	//��ϵ��
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('����д��ϵ�ˣ�',1);
	
	//��ϵ�绰
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('����д��ϵ�绰��',1);
	
	//��˾��ҳ
	$setsqlarr['website']=trim($_POST['website']);
	
	//ͨѶ��ַ
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('����дͨѶ��ַ��',1);
	
	//����
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):showmsg('����д��ϵ���䣡',1);
	
	//FAX
	$setsqlarr['fax']=trim($_POST['fax']);
	
	//��������
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	
	//����
	if (updatetable(table('company_profile'), $setsqlarr," uid='{$uid}'")) {
		z_change_companyname($uid, $setsqlarr['companyname']);			
		adminmsg("����ɹ���",2);
	} else {
		adminmsg("����ʧ�ܣ�",0);
	}
}

elseif ($act == 'edit_save_intro') {
	$uid = $_POST['uid'];
	$contents = $_POST['contents'];
	
	$company = z_company($uid);
	if ($company['audit'] == 4) {
		$setsqlarr['contents_tmp'] = $contents;
	} else {
		$setsqlarr['contents'] = $contents;
	}
	updatetable(table('company_profile'),$setsqlarr,array('uid'=>$uid));
	adminmsg('����ɹ���',2);
}

elseif ($act == 'edit_save_auth') {
	$uid = $_POST['uid'];
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	//$setsqlarr['license']=trim($_POST['license'])?trim($_POST['license']):adminmsg('��û������Ӫҵִ��ע��ţ�',1);
	!$_FILES['certificate_img']['name']?adminmsg('���ϴ�ͼƬ��',1):"";
	$certificate_dir="../data/".$_CFG['updir_certificate']."/".date("Y/m/d/");
	make_dir($certificate_dir);
	$setsqlarr['certificate_img']=_asUpFiles($certificate_dir, "certificate_img",$_CFG['certificate_max_size'],'gif/jpg/bmp/png',true);
	if ($setsqlarr['certificate_img'])
	{
	$setsqlarr['certificate_img']=date("Y/m/d/").$setsqlarr['certificate_img'];
	$auth=z_company($uid);//��ȡԭʼͼƬ
	@unlink("../data/".$_CFG['updir_certificate']."/".$auth['certificate_img']);
	$wheresql="uid='".$uid."'";
	!updatetable(table('company_profile'),$setsqlarr,$wheresql)?adminmsg('����ʧ�ܣ�',1):adminmsg('����ɹ���',2);
	}
	else
	{
 	adminmsg('����ʧ�ܣ�',1);
	}
}

elseif ($act=='edit_del_auth') {
	$uid = $_GET['uid'];
	$uplogo_dir="../data/certificate/";
	$auth=z_company($uid);//��ȡԭʼͼƬ
	@unlink($uplogo_dir.$auth['certificate_img']);//��ɾ��ԭʼͼƬ
	$setsqlarr['certificate_img']="";
	$wheresql="uid='".$uid."'";
		if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
		adminmsg('ɾ���ɹ���',2);
		}
		else
		{
		adminmsg('ɾ��ʧ�ܣ�',1);
		}
}

elseif ($act=='edit_save_logo') {
	$uid = $_POST['uid'];
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['logo']['name']?adminmsg('���ϴ�ͼƬ��',1):"";
	$uplogo_dir="../data/logo/".date("Y/m/d/");
	make_dir($uplogo_dir);
	$setsqlarr['logo']=_asUpFiles($uplogo_dir, "logo",$_CFG['logo_max_size'],'gif/jpg/bmp/png',$uid);
	if ($setsqlarr['logo'])
	{
	$setsqlarr['logo']=date("Y/m/d/").$setsqlarr['logo'];
	$logo_src="../data/logo/".$setsqlarr['logo'];
	$thumb_dir=$uplogo_dir;
	makethumb($logo_src,$thumb_dir,300,110);//��������ͼ
	$wheresql="uid='".$uid."'";
			if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
			{
			adminmsg('�ϴ��ɹ���',2);
			}
			else
			{
			adminmsg('����ʧ�ܣ�',1);
			}
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}

elseif ($act=='edit_del_logo') {
	$uid = $_GET['uid'];
	$uplogo_dir="../data/logo/";
	$auth=z_company($uid);//��ȡԭʼͼƬ
	@unlink($uplogo_dir.$auth['logo']);//��ɾ��ԭʼͼƬ
	$setsqlarr['logo']="";
	$wheresql="uid='".$uid."'";
		if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
		adminmsg('ɾ���ɹ���',2);
		}
		else
		{
		adminmsg('ɾ��ʧ�ܣ�',1);
		}
}

//��Ȩ
elseif($act == 'management')
{	
	$uid=intval($_GET['uid']);
	$u=get_user($uid);
	
	if (!empty($u))	{
		
		$_SESSION['auth_uid']=$u['uid'];
		$_SESSION['auth_username']=$u['username'];
		$_SESSION['auth_utype']=$u['utype'];
		
		z_goto($_CFG['site_domain'].'/user/company/company_index.php');
	}	
} 

//���
elseif ($act == 'audit') {
	$uid=intval($_POST['uid']);
	
	$company = z_company($uid);
	$member = z_member($uid);
	
	if (!empty($company)) {
		$audit = intval($_POST['audit']);
		$audit_reason = trim($_POST['audit_reason']);
		$audit_reason_other = trim($_POST['audit_reason_other']);
		$audit_email = intval($_POST['audit_email']);
		$reason = $audit_reason_other ? $audit_reason_other : $audit_reason;
		
		z_admin_company_audit($uid, $audit);
		
		//�����ʼ�
		if ($audit_email) {
			if ($audit == 1) {
				//+send email ��˾���ͨ��
				$mailArr = array(
					'to'=>$member['email']
				);
				$data = array(
					'companyname'=>$company['companyname'],
					'username'=>$member['username']
				);
				z_mail('company_audit_allow', $mailArr, $data);
				//end
			} elseif ($audit == 3) {
				//+send email ��˾��˲�ͨ��
				$mailArr = array(
					'to'=>$member['email']
				);
				$data = array(
					'companyname'=>$company['companyname'],
					'reason'=>$reason
				);
				z_mail('company_audit_notallow', $mailArr, $data);
				//end
			}
		}
		
		if ($audit == 1) {
			//+sene message ��˾���ͨ��
			z_message('company_audit_allow', $uid);
			//end
		} elseif ($audit == 3) {
			//+sene message ��˾��˲�ͨ��
			z_message('company_audit_notallow', $uid, array('reason'=>$reason));
			//end
		}
		
		adminmsg('�����ɹ���',2);
	}
}

//�����趨
elseif ($act == 'brand') {
	$uid = $_POST['uid'];
	
	$setsqlarr['company_type'] = intval($_POST['company_type']);
	$setsqlarr['company_type_addtime'] = time();
	
	updatetable(table('company_profile'),$setsqlarr,array('uid'=>$uid));
	adminmsg('����ɹ���',2);
}

else if($act == 'cid'){
	$uid = intval($_GET['uid']);
	
	//ְλ��
	updatetable(table('jobs'), array('company_certificate_status'=>intval($_GET['type'])),array('uid'=>$uid));
	
	//��˾��Ϣ��
	$setsqlarr['certificate_status'] = intval($_GET['type']);
	echo updatetable(table('company_profile'),$setsqlarr,array('uid'=>$uid));	
	exit;
}




