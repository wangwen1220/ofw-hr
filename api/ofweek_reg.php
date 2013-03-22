<?php
/**
 * 接收OFweek注册数据
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

$proviceMap = array(
	"CNANH"=>"安徽",
	"CNBEI"=>"北京",
	"CNFUJ"=>"福建",
	"CNGAN"=>"甘肃",
	"CNGUA"=>"广东",
	"CNGUN"=>"广西",
	"CNGUI"=>"贵州",
	"CNHAI"=>"海南",
	"CNHEB"=>"河北",
	"CNHEN"=>"河南",
	"CNHEI"=>"黑龙江",
	"CNHUB"=>"湖北",
	"CNHUN"=>"湖南",
	"CNJIL"=>"吉林",
	"CNJIA"=>"江苏",
	"CNJIN"=>"江西",
	"CNLIA"=>"辽宁",
	"CNNEI"=>"内蒙古",
	"CNNIN"=>"宁夏",
	"CNQIN"=>"青海",
	"CNSHN"=>"山东",
	"CNSHX"=>"山西",
	"CNSHA"=>"陕西",
	"CNSHG"=>"上海",
	"CNSIC"=>"四川",
	"CNTIA"=>"天津",
	"CNXIZ"=>"西藏",
	"CNXIN"=>"新疆",
	"CNYUN"=>"云南",
	"CNZHE"=>"浙江",
	"CNCHO"=>"重庆",
	"CNTAW"=>"台湾",
	"CNHNK"=>"香港",
	"CNMAC"=>"澳门"
);

if (!empty($_POST)) {
	
	$of_username    = isset($_POST['userName']) ? trim($_POST['userName']) : '';
	$of_email       = isset($_POST['email']) ? trim($_POST['email']) : '';
	$of_mobile      = isset($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : '';
	$of_password    = isset($_POST['passWord']) ? trim($_POST['passWord']) : '';
	$of_realname    = isset($_POST['real_name']) ? trim($_POST['real_name']) : '';
	$of_sex         = isset($_POST['sex']) ? intval($_POST['sex']) : 0;
	$of_address     = isset($_POST['address']) ? trim($_POST['address']) : '';
	$of_tel         = isset($_POST['tel']) ? trim($_POST['tel']) : '';
	$of_companyname = isset($_POST['companyname']) ? trim($_POST['companyname']) : '';
	$of_province    = isset($_POST['province']) ? (empty($proviceMap[$_POST['province']]) ? '国外' : $proviceMap[$_POST['province']]) : '';
	$of_city        = isset($_POST['sales']) ? trim($_POST['sales']) : '';
	$of_activeurl   = isset($_POST['activeurl']) ? trim($_POST['activeurl']) : '';
	$of_activeurl   = str_replace('registerValidate.do_', 'registerValidate.do?', $of_activeurl); 
	$of_activeurl   = str_replace('_', '&', $of_activeurl); 
	$of_activeurl   = str_replace('-', '=', $of_activeurl); 
	$of_operatetype = isset($_POST['operatetype']) ? intval($_POST['operatetype']) : 1;//1注册、2同步
	
	$logfile = dirname(__FILE__).'/reg.log';
	$logtxt = date('Y-m-d H:i:s', time());
	$logtxt .= '[reg]';
	foreach ($_POST as $key=>$value) {
		$logtxt .= "$key:$value,";
	}
	$logtxt .= "\r\n";
	error_log($logtxt, 3, $logfile);
	
	//判断用户名是否存在
	if (empty($of_username)) {
		die('fail');
	}
	$user_exist = z_member_exist($of_username);
	if ($user_exist) {
		die('fail');
	}
	
	//members会员表
	$time = time();
	$members['username'] = $of_username;
	$members['email'] = $of_email;
	$members['mobile'] = $of_mobile;
	$members['pwd_hash'] = pwd_randstr();
	$members['password'] = md5($of_password);
	$members['reg_time'] = $time;
	$members['reg_ip'] = '';
	$members['last_login_time'] = $time;
	$members['last_login_ip'] = '';
	$members['qq_openid'] = '';
	$members['status'] = 1;
	$members['login_num'] = 1;
	$members['operatetype'] = $of_operatetype;
	if ($_POST['talentNetworkLogin'] == 'enterpriseLogin') {
		die('fail');###########################企业注册接口关闭####################################
		$members['utype'] = 1;		
	} elseif ($_POST['talentNetworkLogin'] == 'talentNetworkLogin') {
		$members['utype'] = 2;
	} else {
		die('fail');
	}
	$uid = inserttable(table("members"), $members, 1);
	
	if (!$uid) {
		die('fail');
	}
	
	//members_info会员个人资料表
	$membersInfo['uid'] = $uid;
	$membersInfo['realname'] = $of_realname;
	$membersInfo['sex'] = $of_sex==1 ? '女' : '男';
	$membersInfo['birthday'] = '';
	$membersInfo['addresses'] = $of_address;
	$membersInfo['phone'] = $of_tel;
	$membersInfo['qq'] = '';
	$membersInfo['msn'] = '';
	$membersInfo['profile'] = '';
	$membersInfo['avatars'] = '';
	inserttable(table("members_info"), $membersInfo);
	
	//company_profile公司基本信息表
	if ($members['utype'] == 1) {
		$companyProfile['uid'] = $uid;
		$companyProfile['tpl'] = '';
		$companyProfile['companyname'] = $of_companyname;
		$companyProfile['nature'] = 0;
		$companyProfile['nature_cn'] = '';
		$companyProfile['trade'] = 0;
		$companyProfile['trade_cn'] = '';
		$companyProfile['district'] = 0;
		$companyProfile['sdistrict'] = 0;
		$companyProfile['district_cn'] = $of_province;
		if ($of_city) {
			$companyProfile['district_cn'] .= '/'.$of_city;
		}
		$companyProfile['street'] = 0;
		$companyProfile['street_cn'] = '';
		$companyProfile['officebuilding'] = 0;
		$companyProfile['officebuilding_cn'] = '';
		$companyProfile['scale'] = 0;
		$companyProfile['scale_cn'] = '';
		$companyProfile['registered'] = '';
		$companyProfile['currency'] = '';		
		$companyProfile['address'] = $of_address;
		$companyProfile['contact'] = $of_realname;
		$companyProfile['telephone'] = $of_tel;
		$companyProfile['email'] = $of_email;
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
		$companyProfile['edittime'] = $time;
		$companyProfile['refreshtime'] = $time;
		$companyProfile['contents_tmp'] = '';
		$companyProfile['fax'] = '';
		inserttable(table("company_profile"), $companyProfile);	
		
		//members_setmeal会员服务套餐表
		$setmeal_id = $_CFG['reg_service'];
		/*if ($_CFG['reg_service'] == 2) {
			 $total = $_CFG['month_freesetmeal_num'];
			 $now_num = z_month_freesetmeal_num();
			 if ($now_num >= $total) {
			 	$setmeal_id = 1;
			 }
		}*/
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
			'to'=>$of_email
		);
		$data = array(
			'companyname'=>$companyProfile['companyname'],
			'username'=>$members['username'],
			'password'=>$of_password,
			'activeurl'=>$of_activeurl
		);
		z_mail('company_register', $mailArr, $data);
		//end
		//+sene message 企业注册成功
		z_message('company_register', $uid);
		//end
	} 
	
	//resume简历表
	elseif ($members['utype'] == 2) {
		$resume['uid'] = $uid;
		$resume['display'] = 1;
		$resume['display_name'] = 1;
		$resume['audit'] = 2;
		$resume['title'] = '';
		$resume['fullname'] = $of_realname;
		$resume['sex'] = $of_sex==1 ? 2 : 1;
		$resume['sex_cn'] = $of_sex==1 ? '女' : '男';
		$resume['nature'] = 0;
		$resume['nature_cn'] = '';
		$resume['trade'] = 0;
		$resume['trade_cn'] = '';
		$resume['birthdate'] = 0;
		$resume['height'] = 0;
		$resume['marriage'] = 0;
		$resume['marriage_cn'] = '';
		$resume['experience'] = 0;
		$resume['experience_cn'] = '';
		$resume['district'] = 0;
		$resume['sdistrict'] = 0;
		$resume['district_cn'] = $of_province;
		if ($of_city) {
			$resume['district_cn'] .= '/'.$of_city;
		}
		$resume['wage'] = 55;
		$resume['wage_cn'] = '面议';
		$resume['householdaddress'] = '';
		$resume['education'] = 0;
		$resume['education_cn'] = '';
		$resume['tag'] = '';
		$resume['telephone'] = $of_mobile;
		$resume['email'] = $of_email;
		$resume['email_notify'] = 1;
		$resume['qq'] = '';
		$resume['address'] = $of_address;
		$resume['website'] = '';
		$resume['recentjobs'] = '';
		$resume['intention_jobs'] = '';
		$resume['specialty'] = '';
		$resume['photo'] = 0;
		$resume['photo_img'] = '';
		$resume['photo_audit'] = 1;
		$resume['photo_display'] = 1;
		$resume['addtime'] = $time;
		$resume['refreshtime'] = $time;
		$resume['talent'] = 1;
		$resume['complete'] = 1;
		$resume['user_status'] = 1;
		$resume['key'] = '';
		$resume['click'] = 0;
		$resume['tpl'] = '';
		inserttable(table("resume"), $resume);	

		//+send mail 个人注册成功
		$mailArr = array(
			'to'=>$of_email
		);
		$data = array(
			'realname'=>$membersInfo['realname'],
			'username'=>$members['username'],
			'password'=>$_POST['passWord'],
			'activeurl'=>$of_activeurl
		);
		if($of_activeurl) {
			z_mail('person_register', $mailArr, $data);
		} else {
			z_mail('person_register_noactive', $mailArr, $data);
		}		
		//end
		//+sene message 个人注册成功
		z_message('person_register', $uid);
		//end
	}
	
	echo 'ok';die;
}

if (@$_GET['username']) {
	$sql = "select * from ".table('members')." where username = '".trim($_GET['username'])."' LIMIT 1";
	$user = $db->getone($sql);
	if ($user['utype'] == 1) {
		$link[0]['text'] = "完善资料";
		$link[0]['href'] = $_CFG['site_domain'].'/user/company/company_info.php?act=company_profile_edit';
		showmsg("注册成功！",2,$link);
	} elseif ($user['utype'] == 2) {
		$link[0]['text'] = "完善资料";
		$link[0]['href'] = $_CFG['site_domain'].'/user/user_complete_information.php';
		showmsg("注册成功！",2,$link);
	} else {
		$link[0]['text'] = "首页";
		$link[0]['href'] = $_CFG['site_domain'];
		showmsg("注册失败！",0,$link);
	}
}

//获取随机字符串
function pwd_randstr($length=6) {
	$hash='';
	$chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#!~?:-='; 
	$max=strlen($chars)-1;   
	mt_srand((double)microtime()*1000000);   
	for($i=0;$i<$length;$i++)   {   
		$hash.=$chars[mt_rand(0,$max)];   
	}   
	return $hash;   
}