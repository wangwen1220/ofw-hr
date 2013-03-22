<?php
 /*
 * 74cms 申请职位
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'app';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
if ($_SESSION['utype']!='2')
{
	exit("必须是个人会员才能申请职位！");
}
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	$str="<a href=\"".get_member_url(2,true)."personal_user.php?act=user_status\">[设置帐号状态]</a>";
	exit("您的账号处于暂停状态，请先设为正常后进行操作！".$str);
}
if ($act=="app")
{		
		$id=isset($_GET['id'])?$_GET['id']:exit("id 丢失");
		
		$uid=intval($_SESSION['uid']);
		//此处投简历操作还需要验证更多的限制条件  AND audit=1 AND complete=1
		$result = $db->getone("SELECT * FROM ".table('resume')." WHERE uid='{$uid}'  AND (audit=1 OR audit=2 OR audit=4)  AND complete>=4 ");
		if (empty($result)){//无简历信息,不允许投递
			$str="<a href=\"".get_member_url(2,true)."personal_resume.php\">[查看我的简历]</a>";		
			exit("申请职位失败，您没有填写简历或者简历不可见 $str");
		}
		$resumeid = $result['id'];
		
/*
		$jobs=app_get_jobs($id);//查询职位数据
		if (empty($jobs))//没有相应职位
		{
			exit("申请职位失败！");
		}
		$resume_list=get_auditresume_list($_SESSION['uid']);//查询简历信息
		if (empty($resume_list))
		{
		$str="<a href=\"".get_member_url(2,true)."personal_resume.php?act=resume_list\">[查看我的简历]</a>";		
		exit("申请职位失败，您没有填写简历或者简历不可见 $str");
		}
*/		
?>
<script type="text/javascript">
/*
//计算今天申请数量
var app_today="<?php echo get_now_applyjobs_num($_SESSION['uid']) ?>";
$(".ajax_app_tip > span").html(app_today);
*/
//验证
var tsTimeStamp= new Date().getTime();

$.post("<?php echo $_CFG['site_dir'] ?>user/user_apply_jobs.php", { "resumeid": <?php echo $resumeid;?>,"jobsid": "<?php echo $id;?>","time":tsTimeStamp,"act":"app_save"},
 	function (data,textStatus)
 	 {
		if (data=="ok")//职位申请成功
		{
			$('#app_ok').show();
			
		}
		else if(data=="repeat")
		{
			$('#app_repeat').show();
		}
		else
		{
			$('#app_false').show();
			//$("body").append(data);
			//alert(data);
		}
 	 }
);


</script>
<!-- <div class="ajax_app_tip">您今天已经申请了<span></span>个职位</div> -->

<table width="100%" border="0" cellspacing="8" cellpadding="0" id="app_ok" style="display:none;">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">职位申请成功!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;"  class="dialog_closed">
	<a href="<?php echo get_member_url(2,true)?>personal_apply.php?act=apply_jobs" >查看已申请的职位</a><br />

	<a href="javascript:void(0)"  class="DialogClose">关闭窗口</a>
	
	</div>
	</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="app_false" style="display:none;">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/11.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">职位申请失败!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;"  class="dialog_closed">
	<a href="<?php echo get_member_url(2,true)?>personal_apply.php?act=apply_jobs" >查看已申请的职位</a><br />

	<a href="javascript:void(0)"  class="DialogClose">关闭窗口</a>
	
	</div>
	</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="app_repeat" style="display:none;">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/12.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">重复申请该职位!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;"  class="dialog_closed">
	<a href="<?php echo get_member_url(2,true)?>personal_apply.php?act=apply_jobs" >查看已申请的职位</a><br />

	<a href="javascript:void(0)"  class="DialogClose">关闭窗口</a>
	
	</div>
	</td>
  </tr>
</table>
<?php
}
elseif ($act=="app_save")
{
	$jobsid=isset($_POST['jobsid'])?$_POST['jobsid']:exit("出错了");
	$resumeid=isset($_POST['resumeid'])?intval($_POST['resumeid']):exit("出错了");
	$notes=isset($_POST['notes'])?trim($_POST['notes']):"";
	$jobsarr=app_get_jobs($jobsid);
	if (empty($jobsarr))
	{
	exit("职位丢失");
	}
	$resume_basic=get_resume_basic($_SESSION['uid'],$resumeid);//验证是否存在简历及简历是否完善
	if (empty($resume_basic))
	{
	exit("简历丢失");
	}
	$i=0;
	foreach($jobsarr as $jobs)
	 {
	 		if (check_jobs_apply($jobs['id'],$resumeid,$_SESSION['uid']))//查询七天内是否投递
			{
			 continue ;
			}
			$personal_fullname=$resume_basic['display_name']=="1"?$resume_basic['fullname']:$resume_basic['number'];
	 		$addarr['resume_id']=$resumeid;
			$addarr['resume_name']=$personal_fullname;
			$addarr['personal_uid']=intval($_SESSION['uid']);
			$addarr['jobs_id']=$jobs['id'];
			$addarr['jobs_name']=$jobs['jobs_name'];
			$addarr['company_id']=$jobs['company_id'];
			$addarr['company_name']=$jobs['companyname'];
			$addarr['company_uid']=$jobs['uid'];
			$addarr['notes']= $notes;
			$addarr['education_cn']= $jobs['education_cn'];
			$addarr['wage_cn']= $jobs['wage_cn'];
			$addarr['experience_cn']= $jobs['experience_cn'];
			$addarr['audit']= $resume_basic['audit']==2 ? 2 : 1;
			if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
			{
			$addarr['notes']=iconv("utf-8",QISHI_DBCHARSET,$addarr['notes']);
			}
			$addarr['apply_addtime']=time();
			$addarr['personal_look']=1;
			if (inserttable(table('personal_jobs_apply'),$addarr))//插入数据
			{
				
				if ($addarr['audit'] == 1) {
					//+send email 申请职位
					$resume = z_resume($_SESSION['uid']);
					$jobsInfo = z_jobs($jobs['id']);
					$mailArr = array(
						'to'=>$jobsInfo['email'],
						'from'=>$resume['email'],
						'fromName'=>$resume['fullname']
					);
					$data = array(
						'fullname'=>$resume['fullname'],
						'jobs_name'=>$jobsInfo['jobs_name']
					);
					z_mail('resume', $mailArr, $data);
					//end
				}
				
				
					$mailconfig=get_cache('mailconfig');//邮件配置信息				
					$jobs['contact']=$db->getone("select * from ".table('jobs_contact')." where pid='{$jobs['id']}' LIMIT 1 ");//职位联系方式
//					$sms=get_cache('sms_config');//发短信接口	
					$comuser=get_user_info($jobs['uid']);//查询发布者信息
					
/*邮件发送,先不处理
					if ($mailconfig['set_applyjobs']=="1"  && $comuser['email_audit']=="1" && $jobs['contact']['notify']=="1")
					{	
						dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=jobs_apply&jobs_id={$jobs['id']}&jobs_name={$jobs['jobs_name']}&personal_fullname={$personal_fullname}&email={$comuser['email']}");
					}
*/					
					//公司收到的应聘数加1
					$sql = "UPDATE ".table('members')." SET c_resume_num=c_resume_num+1 WHERE uid=".$jobs['uid'];
					$db->query($sql);		
					//个人申请数加1			
					$sql = "UPDATE ".table('members')." SET p_apply_num=p_apply_num+1 WHERE uid=".$_SESSION['uid'];
					$db->query($sql);					
					
					//sms			
//					if ($sms['open']=="1"  && $sms['set_applyjobs']=="1"  && $comuser['mobile_audit']=="1")
//					{
//					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=jobs_apply&jobs_id=".$jobs['id']."&jobs_name=".$jobs['jobs_name']."&personal_fullname=".$personal_fullname."&mobile=".$comuser['mobile']);
//					}
					//sms
					//写入日志
//					write_memberslog($_SESSION['uid'],2,1301,$_SESSION['username'],"申请了职位:{$jobs['jobs_name']}");
			}
			$i=$i+1;
	 }
	 if ($i==0)
	 {
	 exit("repeat");
	 }
	 else
	 {
	 exit("ok");
	 }
}
?>
