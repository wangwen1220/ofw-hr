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
$smarty->assign('leftmenu',"recruitment");


//共同数据
$uid = $_SESSION['uid'];
if (in_array($act, array('apply_jobs', 'send_interview', 'interview_list', 'down_resume_list', 'favorites_list'))) {
	//应聘简历数
	$nav_resume_num = $db->get_total("SELECT COUNT(*) num
		FROM hr_personal_jobs_apply
		WHERE audit=1 AND del!=1 AND company_uid=$uid");
	$smarty->assign('nav_resume_num', $nav_resume_num);
	//已发面试通知数
	$nav_interview_num = $db->get_total("SELECT COUNT(*) num
		FROM hr_company_interview
		WHERE del!=1 AND company_uid=$uid");
	$smarty->assign('nav_interview_num', $nav_interview_num);
	//已下载简历
	$nav_down_interview = $db->get_total("SELECT COUNT(*) num
		FROM hr_company_down_resume
		WHERE company_uid=$uid");
	$smarty->assign('nav_down_interview', $nav_down_interview);
	//简历收藏
	$nav_favorites_interview = $db->get_total("SELECT COUNT(*) num
		FROM hr_company_favorites
		WHERE company_uid=$uid");
	$smarty->assign('nav_favorites_interview', $nav_favorites_interview);
	
	//教育经验
	$eduCate = get_category_zt('QS_education');
	$smarty->assign('eduCate',$eduCate);
	
	//职位
	$jobsnameList = $db->getall("SELECT id,jobs_name FROM hr_jobs WHERE uid='$uid' UNION ALL SELECT id,jobs_name FROM hr_jobs_tmp WHERE uid='$uid'");
	$smarty->assign('jobsnameList',$jobsnameList);
}
//end


//应聘简历管理
if ($act=='apply_jobs') {
		
	$education = isset($_GET['education']) ? intval($_GET['education']) : 0;
	$jobsid = isset($_GET['jobsid']) ? intval($_GET['jobsid']) : 0;
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$num = 10;
	$limit_start = ($page - 1) * $num;
	
	//条件
	$addsql = '';
	if ($education) {
		$addsql .= " AND r.education=$education";
	}
	if ($jobsid) {
		$addsql .= " AND ja.jobs_id=$jobsid";
	}
	
	//总数
	$total = $db->get_total("SELECT COUNT(*) num
		FROM hr_personal_jobs_apply ja
		LEFT JOIN hr_resume r ON ja.personal_uid=r.uid
		WHERE ja.audit=1 AND ja.del!=1 AND ja.company_uid=$uid$addsql");
	
	//列表
	$sql = "SELECT ja.*,r.fullname,r.sex,r.birthdate,r.education,r.education_cn,r.experience,r.experience_cn,r.district_cn,r.recentjobs,r.photo
		FROM hr_personal_jobs_apply ja
		LEFT JOIN hr_resume r ON ja.personal_uid=r.uid
		WHERE ja.audit=1 AND ja.del!=1 AND ja.company_uid=$uid$addsql
		ORDER BY ja.apply_addtime DESC
		LIMIT $limit_start,$num";
	$resumeList = $db->getall($sql);
	foreach ($resumeList as $key=>$value) {
		$resumeList[$key]['apply_addtime'] = date('Y-m-d', $value['apply_addtime']);
		$value['home_province'] or $value['home_province'] = 0;
		$value['home_city'] or $value['home_city'] = 0;
		$resumeList[$key]['district_cn'] = get_now_address($value['home_province'],$value['home_city']);
		if ($value['birthdate']) {
			$value['birthdate'] = ceil((time() - $value['birthdate'])/(365*24*60*60));
		}
		$resumeList[$key]['birthdate'] = $value['birthdate'];
	}
	
	//分页
	$pageHTML = dPage_2($total, $page, 'company_recruitment.php?act=apply_jobs', $num);
	$smarty->assign('pageHTML', $pageHTML);
	
	$smarty->assign('education', $education);
	$smarty->assign('jobsid', $jobsid);
	$smarty->assign('total', $total);
	$smarty->assign('resumeList', $resumeList);
	$smarty->assign('url_education', rebuild_url('education'));
	$smarty->assign('url_jobsid', rebuild_url('jobsid'));
	$smarty->assign('title','收到的职位申请 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_apply_jobs.htm');
}

//发面试通知页面
elseif ($act=='send_interview') {
	
	//删除
	if ($_POST['optype'] == 'delete') {
		if (!empty($_POST['applyid'])) {
			foreach ($_POST['applyid'] as $value) {
				$apply_resume = $db->getone("SELECT did,del FROM hr_personal_jobs_apply WHERE did='".$value."' AND company_uid=$uid");
				if (!empty($apply_resume)) {
					if ($apply_resume['del'] == 2) {
						$db->query("DELETE FROM hr_personal_jobs_apply WHERE did='".$apply_resume['did']."'");
					} else {
						$db->query("UPDATE hr_personal_jobs_apply SET del=1 WHERE did='".$apply_resume['did']."' LIMIT 1");
					}
				}
			}
			
			$link[0]['text'] = "应聘简历";
			$link[0]['href'] = '?act=apply_jobs';
			showmsg("删除成功",2,$link);
		} else {
			showmsg("请选择简历", 0);
		}
	} 
	
	//删除已下载简历
	elseif ($_POST['optype'] == 'delete_down') {
		if (!empty($_POST['downid'])) {
			foreach ($_POST['downid'] as $value) {
				$down_resume = $db->getone("SELECT * FROM hr_company_down_resume WHERE did='".$value."' AND company_uid=$uid");
				if (!empty($down_resume)) {
					$db->query("DELETE FROM hr_company_down_resume WHERE did='".$down_resume['did']."'");
				}
			}
			
			$link[0]['text'] = "已下载简历";
			$link[0]['href'] = '?act=down_resume_list';
			showmsg("删除成功",2,$link);
		} else {
			showmsg("请选择简历", 0);
		}
	}
	
	//发通知页面
	else {
		$resume_uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
		
		//简历页面过来
		if ($resume_uid) {
			$smarty->assign('interview_type', 'from_resume');
			$smarty->assign('applyuid', $resume_uid);
		}
		
		//下载简历过来
		elseif ($_POST['optype'] == 'interview_down') {
			if (empty($_POST['downid'])) {
				showmsg("请选择简历", 0);
			}
			
			$smarty->assign('interview_type', 'from_down_resume');
			$smarty->assign('downid', implode(',', $_POST['downid']));
		}
		
		//应聘简历过来
		else {			
			if (!empty($_POST['applyid'])) {
				$apply_resume = $db->getone("SELECT * FROM hr_personal_jobs_apply WHERE did='".$_POST['applyid'][0]."' AND company_uid=$uid");
			}
			
			if (empty($apply_resume)) {
				showmsg("请选择简历", 0);
			}
			
			$smarty->assign('interview_type', 'from_apply_resume');
			$smarty->assign('applyid', implode(',', $_POST['applyid']));
		}
		
		
		
		$company_profile=get_company($uid);
		$jobsNameList = company_jobsname_list($uid);
		$time = time();
		$time_d_html = '';
		for ($i = 1; $i < 31; $i++) {
			$stri = strlen($i) == 1 ? '0'.$i : $i;
			$strselect = (date('d', $time)+1 == $stri) ? ' selected="selected"' : '';
			$time_d_html .= '<option'.$strselect.' value="'.$stri.'">'.$stri.'</option>';
		}
		
		$time_h_html = '';
		for ($i = 0; $i < 24; $i++) {
			$stri = strlen($i) == 1 ? '0'.$i : $i;
			$strselect = ('09' == $stri) ? ' selected="selected"' : '';
			$time_h_html .= '<option'.$strselect.' value="'.$stri.'">'.$stri.'</option>';
		}
		
		$time_i_html = '';
		for ($i = 0; $i < 60; $i++) {
			$stri = strlen($i) == 1 ? '0'.$i : $i;
			$strselect = ('00' == $stri) ? ' selected="selected"' : '';
			$time_i_html .= '<option'.$strselect.' value="'.$stri.'">'.$stri.'</option>';
		}		
		
		$smarty->assign('company_profile', $company_profile);
		$smarty->assign('jobsNameList', $jobsNameList);
		$smarty->assign('time_d_html', $time_d_html);
		$smarty->assign('time_h_html', $time_h_html);
		$smarty->assign('time_i_html', $time_i_html);
		$smarty->assign('time_y', date('Y', $time));
		$smarty->assign('time_y2', date('Y', $time)+1);
		$smarty->assign('time_m', date('m', $time));
		$smarty->assign('time_d', date('d', $time));
		$smarty->assign('time_H', date('H', $time));
		$smarty->assign('time_i', date('i', $time));
		$smarty->assign('apply_resume', $apply_resume);
		$smarty->assign('title','发面试通知- 企业会员中心 - '.$_CFG['site_name']);
		$smarty->display('member_company/company_send_interview.htm');
	}
}

//发面试通知
elseif ($act == 'save_interview') {
	if (!empty($_POST)) {
		$time = time();
		$fail = $succ = array();
		
		//公司
		$uid = $_SESSION['uid'];
		$company_profile=get_company($uid);
		
		//职位
		$jobs_id = $_POST['jobs_id'];
		$jobs = get_jobs_one($jobs_id);		
		
		//简历数组
		$resumeArr = array();
		#来自于简历页面
		if ($_POST['interview_type'] == 'from_resume') {
			$applyuid = trim($_POST['applyuid']);
			$resumeArr[] = $db->getone("SELECT * FROM hr_resume WHERE uid='".$applyuid."'");
			$return_url = $_CFG['site_domain'].'/user/resume.php?uid='.$applyuid;
		}
		#来自于申请简历页面
		elseif ($_POST['interview_type'] == 'from_apply_resume') {	
			//简历		
			$applyid = explode(',', $_POST['applyid']);
			foreach ($applyid as $value) {
				$apply = $db->getone("SELECT * FROM hr_personal_jobs_apply WHERE did='$value'");
				$resumeArr[] = $db->getone("SELECT * FROM hr_resume WHERE uid='".$apply['personal_uid']."'");
			}
			$return_url = '?act=interview_list';
		}
		#来自于下载简历
		elseif ($_POST['interview_type'] == 'from_down_resume') {
			//简历
			$downid = explode(',', $_POST['downid']);
			foreach ($downid as $value) {
				$down = $db->getone("SELECT * FROM hr_company_down_resume WHERE did='$value'");
				$resumeArr[] = $db->getone("SELECT * FROM hr_resume WHERE uid='".$down['resume_uid']."'");
			}
			$return_url = '?act=down_resume_list';
		}
		
		//发面试通知
		foreach ($resumeArr as $resume) {
			if (!empty($resume)) {
				//已发面试通知的一天内无法再次发送
				$interview = $db->getone("SELECT interview_addtime 
					FROM hr_company_interview 
					WHERE applyid='".$apply['did']."' AND resume_uid='".$resume['uid']."' AND company_uid='".$company_profile['uid']."' 
					ORDER BY interview_addtime DESC");
				if (!empty($interview)) {
					if ($interview['interview_addtime']>=$time || (date('Ymd',$interview['interview_addtime'])==date('Ymd',$time))) {
						$fail[] = $resume['fullname'];
						continue;
					}
				}				
				
				$setsqlarr['resume_id'] = $resume['id'];
				$setsqlarr['resume_name'] = $resume['title'];
				$setsqlarr['resume_addtime'] = $resume['addtime'];
				$setsqlarr['resume_uid'] = $resume['uid'];
				$setsqlarr['jobs_id'] = $jobs['id'];
				$setsqlarr['jobs_name'] = $jobs['jobs_name'];
				$setsqlarr['jobs_addtime'] = $jobs['addtime'];
				$setsqlarr['company_id'] = $company_profile['id'];
				$setsqlarr['company_name'] = $company_profile['companyname'];
				$setsqlarr['company_addtime'] = $company_profile['addtime'];
				$setsqlarr['company_uid'] = $company_profile['uid'];
				$setsqlarr['interview_addtime'] = $time;
				$setsqlarr['notes'] = trim($_POST['notes']);
				$setsqlarr['interview_time'] = strtotime($_POST['time_y'].'-'.$_POST['time_m'].'-'.$_POST['time_d'].' '.$_POST['time_h'].':'.$_POST['time_i'].':00');
				$setsqlarr['contact'] = trim($_POST['contact']);
				$setsqlarr['telephone'] = trim($_POST['telephone']);
				$setsqlarr['address'] = trim($_POST['address']);
				$setsqlarr['email'] = trim($_POST['email']);
				$setsqlarr['del'] = 0;				
				$setsqlarr['applyid'] = $apply['did'];
				z_interview($setsqlarr);
				$succ[] = $resume['fullname'];	

				//+send email 面试通知
				$mailArr = array(
					'to'=>$resume['email'],
					'from'=>$jobs['email'],
					'fromName'=>$jobs['contact']
				);
				$data = array(
					'fullname'=>$resume['fullname'],
					'jobs_name'=>$setsqlarr['jobs_name'],
					'interview_time'=>date('Y-m-d H:i', $setsqlarr['interview_time']),
					'address'=>$setsqlarr['address'],
					'contact'=>$setsqlarr['contact'],
					'telephone'=>$setsqlarr['telephone'],
					'company_name'=>$setsqlarr['company_name']
				);
				z_mail('interview', $mailArr, $data);
				//end
			};
		}
		$msg = '';
		$type = '';
		
		if (!empty($succ)) {
			$type = 2;
			$t = implode(',', $succ);
			$msg .= $t.'的面试通知发送成功<br />';
		}
		
		if (!empty($fail)) {
			$type = 0;
			$t = implode(',', $fail);
			$msg .= $t.'的面试通知发送失败，原因：您今天已经发送过该面试通知';
		}
		
		$link[0]['text'] = "已发面试通知";
		$link[0]['href'] = '?act=interview_list';
		showmsg($msg,$type,$link);		
	}
}

//已发面试通知删除
elseif ($act == 'interview_del') {	
	//删除
	if (!empty($_POST['interviewid'])) {
		foreach ($_POST['interviewid'] as $value) {
			$interview = $db->getone("SELECT did,del FROM hr_company_interview WHERE did='".$value."' AND company_uid=$uid");
			if (!empty($interview)) {
				if ($interview['del'] == 2) {
					$db->query("DELETE FROM hr_company_interview WHERE did='".$interview['did']."'");
				} else {
					$db->query("UPDATE hr_company_interview SET del=1 WHERE did='".$interview['did']."' LIMIT 1");
				}
			}
		}
		
		$link[0]['text'] = "已发面试通知";
		$link[0]['href'] = '?act=interview_list';
		showmsg("删除成功",2,$link);
	} else {
		showmsg("请选择面试通知", 0);
	}
}

//已发面试通知
elseif ($act=='interview_list') {
	
	$education = isset($_GET['education']) ? intval($_GET['education']) : 0;
	$jobsid = isset($_GET['jobsid']) ? intval($_GET['jobsid']) : 0;
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$num = 10;
	$limit_start = ($page - 1) * $num;
	
	//条件
	$addsql = '';
	if ($education) {
		$addsql .= " AND r.education=$education";
	}
	if ($jobsid) {
		$addsql .= " AND ci.jobs_id=$jobsid";
	}
	
	//总数
	$total = $db->get_total("SELECT COUNT(*) num
		FROM hr_company_interview ci
		LEFT JOIN hr_resume r ON ci.resume_uid=r.uid
		WHERE ci.del!=1 AND ci.company_uid=$uid$addsql");
	
	//列表
	$sql = "SELECT ci.*,r.fullname,r.sex,r.birthdate,r.education,r.education_cn,r.experience,r.experience_cn,r.district_cn,r.recentjobs,r.photo
		FROM hr_company_interview ci
		LEFT JOIN hr_resume r ON ci.resume_uid=r.uid
		WHERE ci.del!=1 AND ci.company_uid=$uid$addsql
		ORDER BY ci.interview_addtime DESC
		LIMIT $limit_start,$num";
	$resumeList = $db->getall($sql);
	foreach ($resumeList as $key=>$value) {
		$resumeList[$key]['interview_time'] = date('Y-m-d', $value['interview_time']);
		$value['home_province'] or $value['home_province'] = 0;
		$value['home_city'] or $value['home_city'] = 0;
		$resumeList[$key]['district_cn'] = get_now_address($value['home_province'],$value['home_city']);
		if ($value['birthdate']) {
			$value['birthdate'] = ceil((time() - $value['birthdate'])/(365*24*60*60));
		}
		$resumeList[$key]['birthdate'] = $value['birthdate'];
	}
	
	//分页
	$pageHTML = dPage_2($total, $page, 'company_recruitment.php?act=interview_list', $num);
	$smarty->assign('pageHTML', $pageHTML);
	
	$smarty->assign('education', $education);
	$smarty->assign('jobsid', $jobsid);
	$smarty->assign('total', $total);
	$smarty->assign('resumeList', $resumeList);
	$smarty->assign('url_education', rebuild_url('education'));
	$smarty->assign('url_jobsid', rebuild_url('jobsid'));
	$smarty->assign('title','我发起的面试邀请 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_interview.htm');
}

//搜索我的简历库
elseif ($act == 'search') {
	
	//权限
	$setmeal = z_setmeal($_SESSION['uid']);
	if (empty($setmeal['resume_search'])) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = 'company_index.php';
		showmsg("您未开通高级会员！请与我们客服联系：0755-83279360",0,$link);
	}
	
	//权限判断
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = 'company_index.php';
		showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0,$link);
	}

	$jobCate = get_parent_cagegory('category_jobs');#1级职位分类
	
	
	/*$name = $res['fullname'][0].$res['fullname'][1];
	if ($type == 2 ){
		$res['fullname'] = $name.(($res['sex']==1)?'先生':'女士');
	}*/
	
	
	//可指定行业数据
	$res_industry = get_category_zt('QS_trade');
	$smarty->assign('category_industry', $res_industry);
	
	//一级职业分类数据
	$smarty->assign('parent_category', get_parent_cagegory('category_jobs'));
	//地区分类数据
	$smarty->assign('parent_district', get_parent_cagegory('category_district'));
	
	$select_edu = array();
	$res = get_category_zt('QS_education');
	foreach ($res as $v){
		$select_edu[$v['c_id']] = $v['c_name'];
	}
	$smarty->assign('options_edu',array_reverse($select_edu,1));
	
	
	
	
	$smarty->assign('jobCate', $jobCate);
	$smarty->assign('title','搜索我的简历库 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_search.htm');
}

//已下载简历
elseif ($act=='down_resume_list') {
	
	$education = isset($_GET['education']) ? intval($_GET['education']) : 0;
	$jobsid = isset($_GET['jobsid']) ? intval($_GET['jobsid']) : 0;
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$num = 10;
	$limit_start = ($page - 1) * $num;
	
	//条件
	$addsql = '';
	if ($education) {
		$addsql .= " AND r.education=$education";
	}
	
	//总数
	$total = $db->get_total("SELECT COUNT(*) num
		FROM hr_company_down_resume dr
		LEFT JOIN hr_resume r ON dr.resume_uid=r.uid
		WHERE dr.company_uid=$uid$addsql");
	
	//列表
	$sql = "SELECT dr.*,ri.category AS intention_jobs,r.fullname,r.sex,r.birthdate,r.education,r.education_cn,r.experience,r.experience_cn,r.district_cn,r.recentjobs,r.photo
		FROM hr_company_down_resume dr
		LEFT JOIN hr_resume r ON dr.resume_uid=r.uid
		LEFT JOIN hr_resume_intention ri ON ri.uid=r.uid
		WHERE dr.company_uid=$uid$addsql
		ORDER BY dr.down_addtime DESC
		LIMIT $limit_start,$num";
	$resumeList = $db->getall($sql);
	foreach ($resumeList as $key=>$value) {
		$resumeList[$key]['down_addtime'] = date('Y-m-d', $value['down_addtime']);
		$value['home_province'] or $value['home_province'] = 0;
		$value['home_city'] or $value['home_city'] = 0;
		$resumeList[$key]['district_cn'] = get_now_address($value['home_province'],$value['home_city']);
		if ($value['birthdate']) {
			$value['birthdate'] = ceil((time() - $value['birthdate'])/(365*24*60*60));
		}
		$resumeList[$key]['birthdate'] = $value['birthdate'];
		$resumeList[$key]['intention_jobs'] = z_category_cn($value['intention_jobs']);
	}
	
	//分页
	$pageHTML = dPage_2($total, $page, 'company_recruitment.php?act=down_resume_list', $num);
	$smarty->assign('pageHTML', $pageHTML);

	$smarty->assign('education', $education);
	$smarty->assign('total', $total);
	$smarty->assign('resumeList', $resumeList);
	$smarty->assign('url_education', rebuild_url('education'));
	$smarty->assign('title','已下载的简历 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_down_resume.htm');
}

//简历收藏
elseif ($act=='favorites_list')
{
	$education = isset($_GET['education']) ? intval($_GET['education']) : 0;
	$jobsid = isset($_GET['jobsid']) ? intval($_GET['jobsid']) : 0;
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$num = 10;
	$limit_start = ($page - 1) * $num;
	
	//条件
	$addsql = '';
	if ($education) {
		$addsql .= " AND r.education=$education";
	}
	
	//总数
	$total = $db->get_total("SELECT COUNT(*) num
		FROM hr_company_favorites cf
		LEFT JOIN hr_resume r ON cf.resume_id=r.id
		WHERE cf.company_uid=$uid$addsql");
	
	//列表
	$sql = "SELECT cf.*,r.uid,ri.category AS intention_jobs,r.fullname,r.sex,r.birthdate,r.education,r.education_cn,r.experience,r.experience_cn,r.district_cn,r.recentjobs,r.photo
		FROM hr_company_favorites cf
		LEFT JOIN hr_resume r ON cf.resume_id=r.id
		LEFT JOIN hr_resume_intention ri ON ri.uid=r.uid
		WHERE cf.company_uid=$uid$addsql
		ORDER BY cf.favoritesa_ddtime DESC
		LIMIT $limit_start,$num";
	$resumeList = $db->getall($sql);
	foreach ($resumeList as $key=>$value) {
		$resumeList[$key]['favoritesa_ddtime'] = date('Y-m-d', $value['favoritesa_ddtime']);
		$value['home_province'] or $value['home_province'] = 0;
		$value['home_city'] or $value['home_city'] = 0;
		$resumeList[$key]['district_cn'] = get_now_address($value['home_province'],$value['home_city']);
		if ($value['birthdate']) {
			$value['birthdate'] = ceil((time() - $value['birthdate'])/(365*24*60*60));
		}
		$resumeList[$key]['birthdate'] = $value['birthdate'];
		$resumeList[$key]['intention_jobs'] = z_category_cn($value['intention_jobs']);
	}
	
	//分页
	$pageHTML = dPage_2($total, $page, 'company_recruitment.php?act=favorites_list', $num);
	$smarty->assign('pageHTML', $pageHTML);

	$smarty->assign('education', $education);
	$smarty->assign('total', $total);
	$smarty->assign('resumeList', $resumeList);
	$smarty->assign('url_education', rebuild_url('education'));
	$smarty->assign('title','企业人才库 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_favorites.htm');
}

unset($smarty);
?>
