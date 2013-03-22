<?php
 /*
 * 74cms 发送邮件
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
ignore_user_abort(true);
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
$uid=intval($_GET['uid']);
$key=trim($_GET['key']);

if (empty($uid) || empty($key))
{
 exit("error1");
}
$asyn_userkey=asyn_userkey($uid);
if ($asyn_userkey<>$key)exit("error2");
$mailconfig=get_cache('mailconfig');

//all mail subject
$subject = array(
	'enterprise_reg' => '感谢您注册OFweek人才网企业会员',
	'talent_reg'	 => 'OFweek人才网注册激活邮件',
	'invite'	 => '面试通知--{companyname}',
	'recommend'	 => '您的好友{personalfullname}给您推荐的职位-- {jobsname}',
	'licenseallow'	 => '审核通过：恭喜您，您的企业已经通过我们的审核',
	'licensenotallow'	 => '审核未通过：您的企业资料未通过审核，请及时进行修改',
	'joballow'	 => '通过审核：恭喜您，您的职位{jobsname}已通过审核',
	'jobnotallow'	 => '未通过审核：您的职位{jobsname}未通过审核，请及时进行修改',
	'resume'	 => '{personal_fullname}-应聘 {jobsname} 来自OFweek人才网的申请',
	'resume_audit_allow'	 => '审核通过：恭喜您！您的简历已经通过审核',
	'resume_audit_notallow'	 => '未通过审核：您的简历未通过审核，请及时进行修改',
);
//发送企业注册邮件
/*
 * $arr = array(
 * 		'sendemail' => $sendemail,//企业注册邮箱
 * 		'sendusername' => $senduseranme,//用户名
 * 		'sendpassword' => $sendpassword,//密码
 * 		'companyname' => $companyname,//公司名称
 * );
 * sdopen('enterprise_reg',$uid,$arr);
 * 
 */
if($act == 'enterprise_reg'){
	if ($_GET['sendemail'] && $_GET['sendusername'] && $mailconfig['set_reg']=="1")
	{
			$userinfo=get_user_inid($uid);
			if ($userinfo['username']==$_GET['sendusername'] && $userinfo['email']==$_GET['sendemail'])
			{
				$mail_template=get_mail_template('company_register.htm');
				$templates=label_replace($mail_template);
				$templates_title=label_replace($subject['enterprise_reg']);
				smtp_mail($_GET['sendemail'],$templates_title,$templates);
			}
	}
}
//发送个人注册邮件
/*
 * $arr = array(
 * 		'sendemail' => $sendemail,//个人注册邮箱
 * 		'sendusername' => $senduseranme,//个人注册用户名
 * 		'sendpassword' => $sendpassword,//个人注册密码
 * 		'personalfullname' => $personalfullname,//个人全名
 * );
 * sdopen('talent_reg',$uid,$arr);
 * 
 */
if($act == 'talent_reg'){
	if ($_GET['sendemail'] && $_GET['sendusername'] && $_GET['sendpassword'] && $mailconfig['set_reg']=="1")
	{
			$userinfo=get_user_inid($uid);
			if ($userinfo['username']==$_GET['sendusername'] && $userinfo['email']==$_GET['sendemail'])
			{ 
				$mail_template=get_mail_template('person_register.htm');
				$templates=label_replace($mail_template);
				$templates_title=label_replace($subject['talent_reg']);
				smtp_mail($_GET['sendemail'],$templates_title,$templates);
			}
	}
}
//申请职位发送邮件（个人投递简历）系统发送给企业
/*
 * $arr = array(
 * 		'sendemail' => '774928735@qq.com',//企业邮箱
 * 		'jobs_name' => $jobs_name,//工作名称
 * 		'personal_fullname' => $personalfullname,//个人全名
 * );
 * sdopen('resume',$uid,$arr);
 * 
 */
elseif($act == 'resume')
{  
	assing_resume($uid,1);
	$templates = $smarty->fetch('mail_template/outward.htm'); 
	$templates_title=label_replace($subject['resume']);
	$templates_title=str_replace('{personal_fullname}',$_GET['personal_fullname'],$templates_title);
	smtp_mail($_GET['sendemail'],$templates_title,$templates);
}
//职位推荐邮件
/*
 * $arr = array(
 * 		'sendemail' => $sendemail,//好友邮箱
 * 		'friend_name' => $friend_name,// 好友姓名
 *		'command_company' => $command_company,//推荐公司
 * 		'job_name' => $job_name, //工作名称
 * 		'personal_fullname' => $personal_fullname;//个人全名
 * );
 * sdopen('recommend',$uid,$arr);
 * 
 */
elseif ($act == 'recommend'){
	$mail_template=get_mail_template('recommend.htm');
	$templates=label_replace($mail_template);
	$templates_title=label_replace($subject['recommend']);
	
	foreach ($_GET as $key=>$value) {
		$templates = str_replace('{'.$key.'}', $value, $templates);
	}
	
	smtp_mail($_GET['sendemail'],$templates_title,$templates);
}
//邀请面试发送邮件
/*
 * $arr = array(
 * 		'sendemail' => $sendemail, //面试者邮箱
 * 		'interview_time' => $interview_time,//面试时间
 * 		'companyname'   => $companyname,//面试公司名称
 * 		'company_addr' => $company_addr,//公司地址
 * 		'company_hr' => $company_hr,//公司联系人
 * 		'company_tel' => $company_tel,//公司联系电话
 * 		'personal_fullname' => $personalfullname,//个人姓名
 * );
 * sdopen('invite',$uid,$arr);
 * 
 */
elseif($act == 'invite')
{
			$template=get_mail_template('interview.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['invite']);
			$templates=str_replace('{interview_time}',$_GET['interview_time'],$templates);
			$templates=str_replace('{company_addr}',$_GET['company_addr'],$templates);
			$templates=str_replace('{company_hr}',$_GET['company_hr'],$templates);
			$templates=str_replace('{company_tel}',$_GET['company_tel'],$templates);
			smtp_mail($_GET['sendemail'],$templates_title,$templates);
}
//职位审核通过，发送邮件
/*
 * $arr = array(
 * 		'companyname' => $companyname, //公司名
 * 		'jobs_name'   => $jobs_name, // 职位名
 * );
 * sdopen('joballow',$uid,$arr);
 * 
 */
elseif($act == 'joballow'){
			$template=get_mail_template('joballow.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['joballow']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//职位未审核通过，发送邮件
/*
 * $arr = array(
 * 		'companyname' => $companyname, //公司名
 * 		'jobs_name'   => $jobs_name, // 职位名
 * 		'reason'   => $reason,  // 审核未通过原因
 * );
 * sdopen('jobnotallow',$uid,$arr);
 * 
 */
elseif($act == 'jobnotallow'){
			$template=get_mail_template('jobnotallow.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['jobnotallow']);
			$templates=str_replace('{reason}',$_GET['reason'],$templates);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//企业认证通过，发送邮件
/*
 * $arr = array(
 * 		'companyname'   => $companyname, //公司名
 * );
 * sdopen('licenseallow',$uid,$arr);
 * 
 */
elseif($act == 'licenseallow'){
			$template=get_mail_template('company_licenseallow.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['licenseallow']);
			$templates=str_replace('{reason}',$_GET['reason'],$templates);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//企业认证未通过，发送邮件
/*
 * $arr = array(
 * 		'companyname'   => $companyname, //公司名称
 * );
 * sdopen('licensenotallow',$uid,$arr);
 * 
 */
elseif($act == 'licensenotallow'){
			$template=get_mail_template('company_licensenotallow.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['licensenotallow']);
			$templates=str_replace('{reason}',$_GET['reason'],$templates);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//简历通过审核，发送邮件
/*
 * $arr = array(
 * 		'personal_fullname'   => $personalfullname,// 个人姓名
 * );
 * sdopen('resume_audit_allow',$uid,$arr);
 * 
 */
elseif($act == 'resume_audit_allow'){
			$template=get_mail_template('resume_audit_allow.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['resume_audit_allow']);
			$templates=str_replace('{reason}',$_GET['reason'],$templates);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//简历未通过审核，发送邮件
/*
 * $arr = array(
 * 		'personalfullname'   => $personalfullname,// 个人姓名
 * 		'reason' => $reason, // 未审核通过原因
 * );
 * sdopen('resume_audit_notallow',$uid,$arr);
 * 
 */
elseif($act == 'resume_audit_notallow'){
			$template=get_mail_template('resume_audit_notallow.htm');
			$templates=label_replace($template);
			$templates_title=label_replace($subject['resume_audit_notallow']);
			$templates=str_replace('{reason}',$_GET['reason'],$templates);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
?>