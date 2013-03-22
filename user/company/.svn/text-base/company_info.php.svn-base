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
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"info");

//公司介绍
if ($act=='company_intro'){
	$company = get_company($_SESSION['uid']);
	$company['contents_tmp'] && $company['contents'] = $company['contents_tmp'];
	$smarty->assign('title','企业资料管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company);
	$smarty->display('member_company/company_intro.htm');		
}
elseif ($act=='company_intro_edit'){
	$uid=intval($_SESSION['uid']);
	$company = get_company($uid);
	if (isset($_POST['save']) || isset($_POST['save_next'])) {
		
		//z_check_banword($_POST['contents'])?showmsg('公司简介存在敏感词，请修改过再提交',0):'';
		
		//简介
		if (($company['audit'] == 1 && $_POST['contents'] != $company['contents']) || $company['audit'] == 4) {
			$setsqlarr['audit']=4;
			if ($_POST['contents'] == $company['contents']) {
				$setsqlarr['contents_tmp'] = '';
			} else {
				$setsqlarr['contents_tmp']=trim($_POST['contents'])?trim($_POST['contents']):showmsg('请填写公司简介！',1);
			}
		} elseif ($company['audit'] == 3) {
			$setsqlarr['audit']=2;
			$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):showmsg('请填写公司简介！',1);
			$setsqlarr['contents_tmp']='';
		} else {
			$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):showmsg('请填写公司简介！',1);
		}
		updatetable(table('company_profile'), $setsqlarr," uid='{$uid}'");
		updatetable('hr_jobs', array('audit'=>4), array('uid'=>$uid));
			
		if (isset($_POST['save_next'])) {
			header("location:?act=company_auth");
		} else {
			$link[0]['text'] = "查看修改结果";
			$link[0]['href'] = '?act=company_intro';
			showmsg("保存成功！",2,$link);
		}
	}
	
	$company['contents_tmp'] && $company['contents'] = $company['contents_tmp'];
	$smarty->assign('title','企业资料管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company);
	$smarty->display('member_company/company_intro_edit.htm');		
}

//基本资料
elseif ($act=='company_profile'){
	$company = get_company($_SESSION['uid']);
	
	$company['trade_cn'] = trim($company['trade_cn'], ',');
	$company['register'] = '';
	if ($company['register_year']) {
		$company['register'] .= $company['register_year'].'年';
		if ($company['register_month']) {
			$company['register'] .= $company['register_month'].'月';
			if ($company['register_day']) {
				$company['register'] .= $company['register_day'].'日';
			}
		}
	}
	
	//显示改动后数据显示
	$company['address_tmp'] && $company['address'] = $company['address_tmp'];
	$company['contact_tmp'] && $company['contact'] = $company['contact_tmp'];
	$company['telephone_tmp'] && $company['telephone'] = $company['telephone_tmp'];
	$company['website_tmp'] && $company['website'] = $company['website_tmp'];
	
	$smarty->assign('title','企业资料管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company);
	$smarty->display('member_company/company_profile.htm');		
}

//编辑基本资料
elseif ($act=='company_profile_edit'){
	$companyType = get_category_zt('QS_company_type');
	$tradeCate = get_category_zt('QS_trade');
	$company = get_company($_SESSION['uid']);
	
	//行业处理
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
	
	//显示改动后数据显示
	$company['address_tmp'] && $company['address'] = $company['address_tmp'];
	$company['contact_tmp'] && $company['contact'] = $company['contact_tmp'];
	$company['telephone_tmp'] && $company['telephone'] = $company['telephone_tmp'];
	$company['website_tmp'] && $company['website'] = $company['website_tmp'];
	
	$smarty->assign('title','企业资料管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company);
	$smarty->assign('companyType',$companyType);
	$smarty->assign('tradeCate',$tradeCate);
	$smarty->assign('tradeCate_Right',$tradeCate_Right);
	$smarty->assign('tradeCate_Left',$tradeCate_Left);
	$smarty->assign('year_now',date('Y', time()));
	$smarty->display('member_company/company_profile_edit.htm');		
}

//保存
elseif ($act=='company_profile_save') {	
	$company = get_company($_SESSION['uid']);
	$companyType = get_category_zt('QS_company_type');
	$tradeCate = get_category_zt('QS_trade');
	$uid=intval($_SESSION['uid']);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	
	//企业性质
	$setsqlarr['nature']=trim($_POST['nature'])?intval($_POST['nature']):showmsg('您选择企业性质！',1);
	foreach ($companyType as $value) {
		if ($value['c_id'] == $setsqlarr['nature']) {
			$setsqlarr['nature_cn'] = $value['c_name'];
			break;
		}
	}
		
	#行业
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
		showmsg('您没有选择行业！',1);
	}
	
	//成立日期
	$setsqlarr['register_year'] = intval($_POST['register_year']);
	$setsqlarr['register_month'] = intval($_POST['register_month']);
	$setsqlarr['register_day'] = intval($_POST['register_day']);
	
	//注册资金
	$setsqlarr['registered'] = intval($_POST['registered']);
	
	//邮箱
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):showmsg('请填写联系邮箱！',1);
	
	//FAX
	$setsqlarr['fax']=trim($_POST['fax']);
	
	//所在区域
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	
	//通讯地址
	#$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('请填写通讯地址！',1);	
	//联系人
	#$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('请填写联系人！',1);	
	//联系电话
	#$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('请填写联系电话！',1);
	//公司主页
	#$setsqlarr['website']=trim($_POST['website']);	

	/*通讯地址address、联系人contact、联系电话telephone、公司主页website*/
	if (($company['audit'] == 1 && $_POST['address'] != $company['address']) || $company['audit'] == 4) {
		$setsqlarr['audit']=4;
		if ($_POST['address'] == $company['address']) {
			$setsqlarr['address_tmp'] = '';
		} else {
			$setsqlarr['address_tmp']=trim($_POST['address'])?trim($_POST['address']):showmsg('请填写通讯地址！',1);
		}
	} elseif ($company['audit'] == 3) {
		$setsqlarr['audit']=2;
		$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('请填写通讯地址！',1);
		$setsqlarr['address_tmp']='';
	} else {
		$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('请填写通讯地址！',1);
	}
	
	if (($company['audit'] == 1 && $_POST['contact'] != $company['contact']) || $company['audit'] == 4) {
		$setsqlarr['audit']=4;
		if ($_POST['contact'] == $company['contact']) {
			$setsqlarr['contact_tmp'] = '';
		} else {
			$setsqlarr['contact_tmp']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('请填写联系人！',1);
		}
	} elseif ($company['audit'] == 3) {
		$setsqlarr['audit']=2;
		$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('请填写联系人！',1);
		$setsqlarr['contact_tmp']='';
	} else {
		$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('请填写联系人！',1);
	}
	
	if (($company['audit'] == 1 && $_POST['telephone'] != $company['telephone']) || $company['audit'] == 4) {
		$setsqlarr['audit']=4;
		if ($_POST['telephone'] == $company['telephone']) {
			$setsqlarr['telephone_tmp'] = '';
		} else {
			$setsqlarr['telephone_tmp']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('请填写联系电话！',1);
		}
	} elseif ($company['audit'] == 3) {
		$setsqlarr['audit']=2;
		$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('请填写联系电话！',1);
		$setsqlarr['telephone_tmp']='';
	} else {
		$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('请填写联系电话！',1);
	}
	
	if (($company['audit'] == 1 && $_POST['website'] != $company['website']) || $company['audit'] == 4) {
		$setsqlarr['audit']=4;
		if ($_POST['website'] == $company['website']) {
			$setsqlarr['website_tmp'] = '';
		} else {
			$setsqlarr['website_tmp']=trim($_POST['website'])?trim($_POST['website']):showmsg('请填写公司主页！',1);
		}
	} elseif ($company['audit'] == 3) {
		$setsqlarr['audit']=2;
		$setsqlarr['website']=trim($_POST['website'])?trim($_POST['website']):showmsg('请填写公司主页！',1);
		$setsqlarr['website_tmp']='';
	} else {
		$setsqlarr['website']=trim($_POST['website'])?trim($_POST['website']):showmsg('请填写公司主页！',1);
	}
	
	//+zcj 2.26 公司名
	if (($company['audit']==2||$company['audit']==3) && !empty($_POST['companyname'])) {
		$setsqlarr['companyname']=trim($_POST['companyname']);
		z_change_companyname($_SESSION['uid'], $setsqlarr['companyname']);
	}
	//end
	
	//保存
	if (updatetable(table('company_profile'), $setsqlarr," uid='{$uid}'")) {			
		write_memberslog($_SESSION['uid'],$_SESSION['utype'],8001,$_SESSION['username'],"修改企业资料");
		if ($_POST['optype']=='save_next') {
			header("location:?act=company_intro_edit");
		} else {
			$link[0]['text'] = "查看修改结果";
			$link[0]['href'] = '?act=company_profile';
			showmsg("保存成功！",2,$link);
		}
	} else {
		showmsg("保存失败！",0);
	}
}
elseif ($act=='company_auth')
{
	$link[0]['text'] = "完善企业资料";
	$link[0]['href'] = '?act=company_profile';
	$link[1]['text'] = "管理首页";
	$link[1]['href'] = 'company_index.php';
	$company_profile=get_company($_SESSION['uid']);
	if (empty($company_profile['companyname'])) showmsg("请完善您的企业资料再上传营业执照！",1,$link);
	$smarty->assign('title','营业执照 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('company_profile',$company_profile);
	$smarty->display('member_company/company_auth.htm');
}
elseif ($act=='company_auth_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	//$setsqlarr['license']=trim($_POST['license'])?trim($_POST['license']):showmsg('您没有输入营业执照注册号！',1);
	!$_FILES['certificate_img']['name']?showmsg('请上传图片！',1):"";
	$certificate_dir="../../data/".$_CFG['updir_certificate']."/".date("Y/m/d/");
	make_dir($certificate_dir);
	$setsqlarr['certificate_img']=_asUpFiles($certificate_dir, "certificate_img",1*1024,'gif/jpg',true);
	if ($setsqlarr['certificate_img'])
	{
	$setsqlarr['certificate_img']=date("Y/m/d/").$setsqlarr['certificate_img'];
	$auth=get_company($_SESSION['uid']);//获取原始图片
	@unlink("../../data/".$_CFG['updir_certificate']."/".$auth['certificate_img']);
	$wheresql="uid='".$_SESSION['uid']."'";
	write_memberslog($_SESSION['uid'],1,8002,$_SESSION['username'],"上传了营业执照");
	!updatetable(table('company_profile'),$setsqlarr,$wheresql)?showmsg('保存失败！',1):showmsg('保存成功！',2);
	}
	else
	{
	showmsg('保存失败！',1);
	}
}
elseif ($act=='company_auth_del')
{
	$uplogo_dir="../../data/certificate/";
	$auth=get_company($_SESSION['uid']);//获取原始图片
	@unlink($uplogo_dir.$auth['certificate_img']);//先删除原始图片
	$setsqlarr['certificate_img']="";
	$wheresql="uid='".$_SESSION['uid']."'";
		if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
		write_memberslog($_SESSION['uid'],1,8004,$_SESSION['username'],"删除了营业执照");
		showmsg('删除成功！',2);
		}
		else
		{
		showmsg('删除失败！',1);
		}
}
elseif ($act=='company_logo')
{
	$link[0]['text'] = "完善企业资料";
	$link[0]['href'] = '?act=company_profile';
	$link[1]['text'] = "会员中心首页";
	$link[1]['href'] = 'company_index.php';
	$company_profile=get_company($_SESSION['uid']);
	if (empty($company_profile['companyname'])) showmsg("请完善您的企业资料再上传营业执照！",1,$link);
	$smarty->assign('title','企业LOGO - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',$company_profile);
	$smarty->assign('rand',rand(1,100));
	$smarty->display('member_company/company_logo.htm');
}
elseif ($act=='company_logo_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['logo']['name']?showmsg('请上传图片！',1):"";
	$uplogo_dir="../../data/logo/".date("Y/m/d/");
	make_dir($uplogo_dir);
	$setsqlarr['logo']=_asUpFiles($uplogo_dir, "logo",1*1024,'gif/jpg',$_SESSION['uid']);
	if ($setsqlarr['logo'])
	{
	$setsqlarr['logo']=date("Y/m/d/").$setsqlarr['logo'];
	$logo_src="../../data/logo/".$setsqlarr['logo'];
	$thumb_dir=$uplogo_dir;
	makethumb($logo_src,$thumb_dir,300,110);//生成缩略图
	$wheresql="uid='".$_SESSION['uid']."'";
			if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
			{
			$link[0]['text'] = "查看LOGO";
			$link[0]['href'] = '?act=company_logo';
			write_memberslog($_SESSION['uid'],1,8003,$_SESSION['username'],"上传了企业LOGO");
			showmsg('上传成功！',2,$link);
			}
			else
			{
			showmsg('保存失败！',1);
			}
	}
	else
	{
	showmsg('保存失败！',1);
	}
}
elseif ($act=='company_logo_del')
{
	$uplogo_dir="../../data/logo/";
	$auth=get_company($_SESSION['uid']);//获取原始图片
	@unlink($uplogo_dir.$auth['logo']);//先删除原始图片
	$setsqlarr['logo']="";
	$wheresql="uid='".$_SESSION['uid']."'";
		if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
		write_memberslog($_SESSION['uid'],1,8004,$_SESSION['username'],"删除了企业LOGO");
		showmsg('删除成功！',2);
		}
		else
		{
		showmsg('删除失败！',1);
		}
}
elseif ($act=='company_map')
{
	$link[0]['text'] = "填写企业资料";
	$link[0]['href'] = '?act=company_profile';
	$company_profile=get_company($_SESSION['uid']);
	if (empty($company_profile['companyname'])) showmsg("请完善您的企业资料再设置电子地图！",1,$link);
	if ($company_profile['map_open']=="1")//假如已经开通
	{
	header("Location: ?act=company_map_set");
	}
	else
	{
	$points=get_cache('points_rule');//获取积分消费规则
	$smarty->assign('title','开通电子地图 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('points',$points['company_map']['value']);
	$smarty->display('member_company/company_map_open.htm');
	}
}
elseif ($act=='company_map_open')
{
	
	$company_profile=get_company($_SESSION['uid']);
	$link[0]['text'] = "填写企业资料";
	$link[0]['href'] = '?act=company_profile';
	if (empty($company_profile['companyname'])) showmsg("请完善您的企业资料再设置电子地图！",1);
	$points=get_cache('points_rule');
	$user_points=get_user_points($_SESSION['uid']);
	if ($points['company_map']['type']=="2" && $points['company_map']['value']>$user_points)
	{
	showmsg("你的".$_CFG['points_byname']."不足，请充值后再进行相关操作！",0);
	}
	$wheresql="uid='".$_SESSION['uid']."'";
	$setsqlarr['map_open']=1;
		if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
			//发送邮件
			$mailconfig=get_cache('mailconfig');
			if ($mailconfig['set_addmap']=="1" && $user['email_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_addmap");
			}
			//sms
			$sms=get_cache('sms_config');
			if ($sms['open']=="1" && $sms['set_addmap']=="1"  && $user['mobile_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_addmap");
			}
			//sms
			$link[0]['text'] = "设置电子地图";
			$link[0]['href'] = '?act=company_map_set';
			$link[1]['text'] = "返回会员中心首页";
			$link[1]['href'] = 'company_index.php?act=';			
			write_memberslog($_SESSION['uid'],1,8005,$_SESSION['username'],"开通了电子地图");
			if ($points['company_map']['value']>0)
			{
			report_deal($_SESSION['uid'],$points['company_map']['type'],$points['company_map']['value']);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$points['company_map']['type']=="1"?"+":"-";
			write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"开通了电子地图({$operator}{$points['company_map']['value']})，(剩余:{$user_points})");
			}
			showmsg('成功开通！',2,$link);
		}
		else
		{
		showmsg('开通失败！',1);
		}
}
elseif ($act=='company_map_set')
{
	$smarty->assign('title','设置电子地图 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('company_profile',get_company($_SESSION['uid']));
	$smarty->display('member_company/company_map_set.htm');
}
elseif ($act=='company_map_set_save')
{
	$setsqlarr['map_x']=trim($_POST['x'])?trim($_POST['x']):showmsg('请先点击“在地图上标记我的位置”按钮，然后再点击保存我的位置进行保存！',1);
	$setsqlarr['map_y']=trim($_POST['y'])?trim($_POST['y']):showmsg('请先点击“在地图上标记我的位置”按钮，然后再点击保存我的位置进行保存！',1);
	$setsqlarr['map_zoom']=trim($_POST['zoom']);
	$wheresql=" uid='{$_SESSION['uid']}'";
	write_memberslog($_SESSION['uid'],1,8006,$_SESSION['username'],"设置了电子地图坐标");
	if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
	{
		$jobsql['map_x']=$setsqlarr['map_x'];
		$jobsql['map_y']=$setsqlarr['map_y'];
		updatetable(table('jobs'),$jobsql,$wheresql);
		updatetable(table('jobs_tmp'),$jobsql,$wheresql);
		showmsg('保存成功',2);
	}
	else
	{
	showmsg('保存失败',1);
	}
}
unset($smarty);
?>