<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_personal_fun.php');
$act = trim($_GET['act']);

//简历列表
if ($act == 'list') {
	
	//权限
	check_permissions($_SESSION['admin_purview'], "resume_show");
	
	//行业分类
	$tradeCate = get_category_zt('QS_trade');
	$smarty->assign('tradeCate', $tradeCate);
	
	//搜索
	$resumeList = array();
	$search = array();
	$from = isset($_GET['from']) ? trim($_GET['from']) : '';
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
	$limit_start = ($page - 1) * $num;
	$eduCate = get_category_zt('QS_education');
	$experienceCate = get_category_zt('QS_experience');
	
	$search['uid'] = isset($_GET['uid']) ? trim($_GET['uid']) : '';
	$search['username'] = isset($_GET['username']) ? trim($_GET['username']) : '';
	$search['fullname'] = isset($_GET['fullname']) ? trim($_GET['fullname']) : '';
	$search['sex'] = isset($_GET['sex']) ? intval($_GET['sex']) : 0;
	$search['regtime_from'] = isset($_GET['regtime_from']) ? trim($_GET['regtime_from']) : '';
	$search['regtime_to'] = isset($_GET['regtime_to']) ? trim($_GET['regtime_to']) : '';
	$search['refreshtime_from'] = isset($_GET['refreshtime_from']) ? trim($_GET['refreshtime_from']) : '';
	$search['refreshtime_to'] = isset($_GET['refreshtime_to']) ? trim($_GET['refreshtime_to']) : '';
	$search['complete'] = isset($_GET['complete']) ? intval($_GET['complete']) : 0;
	$search['trade'] = isset($_GET['trade']) ? trim($_GET['trade']) : 0;
	if ($from == 'audit') {
		$search['audit'] = 2;
	} else {
		$search['audit'] = isset($_GET['audit']) ? intval($_GET['audit']) : 0;
	}
	$search['education'] = isset($_GET['education']) ? intval($_GET['education']) : 0;
	$search['experience'] = isset($_GET['experience']) ? intval($_GET['experience']) : 0;
	$search['age_from'] = isset($_GET['age_from']) ? trim($_GET['age_from']) : '';
	$search['age_to'] = isset($_GET['age_to']) ? trim($_GET['age_to']) : '';
	$search['sort'] = isset($_GET['sort']) ? trim($_GET['sort']) : '';
	$search['operatetype'] = isset($_GET['operatetype']) ? intval($_GET['operatetype']) : 0;
	$addsql = '';
	
	if ($search['uid']) {
		$t = $search['uid'];
		$addsql .= " AND m.uid='$t'";
	}
	if ($search['username']) {
		$t = $search['username'];
		$addsql .= " AND m.username='$t'";
	}
	if ($search['operatetype']) {
		$t = $search['operatetype'];
		$addsql .= " AND m.operatetype='$t'";
	}
	if ($search['fullname']) {
		$t = $search['fullname'];
		$addsql .= " AND r.fullname='$t'";
	}
	if ($search['sex']) {
		$t = $search['sex'];
		$addsql .= " AND r.sex='$t'";
	}
	if ($search['regtime_from']) {
		$t = strtotime($search['regtime_from']);
		$addsql .= " AND m.reg_time>='$t'";
	}
	if ($search['regtime_to']) {
		$t = strtotime($search['regtime_to'].' 23:59:59');
		$addsql .= " AND m.reg_time<='$t'";
	}
	if ($search['refreshtime_from']) {
		$t = strtotime($search['refreshtime_from']);
		$addsql .= " AND r.refreshtime>='$t'";
	}
	if ($search['refreshtime_to']) {
		$t = strtotime($search['refreshtime_to'].' 23:59:59');
		$addsql .= " AND r.refreshtime<='$t'";
	}
	if ($search['complete']) {
		$t = $search['complete'];
		if ($t == 1) {#完善
			$addsql .= " AND r.complete>=4";
		} elseif ($t == 2) {
			$addsql .= " AND r.complete<4";
		}
	}
	if ($search['audit']) {
		$t = $search['audit'];
		$addsql .= $t == 2 ? " AND (r.audit='$t' || r.audit=4)" : " AND r.audit='$t'";
	}
	if ($search['education']) {
		$t = $search['education'];
		$addsql .= " AND r.education='$t'";
	}
	if ($search['experience']) {
		$t = $search['experience'];
		$addsql .= " AND r.experience='$t'";
	}
	if ($search['age_from']) {
		$t = $search['age_from'];
		$t = time() - ($t * (365*24*60*60));
		$t = max($t, 0);	
		$addsql .= " AND r.birthdate<='$t'";
	}
	if ($search['age_to']) {
		$t = $search['age_to'];
		$t = time() - ($t * (365*24*60*60));
		$t = max($t, 0);	
		$addsql .= " AND r.birthdate>='$t'";
	}	
	if ($search['trade']) {
		$t = $search['trade'];
		$addsql .= " AND r.trade='$t'";
	}
	
	if ($search['sort']) {
		$t = $search['sort'];
		$sortsql = "ORDER BY m.$t DESC";
	} else {
		$sortsql = "ORDER BY m.reg_time DESC";
	}
	
	//列表
	//用户名，姓名，简历完善度（complete小于4），简历状态，学历，经验，年龄，注册时间，更新时间
	$total = $db->get_total("SELECT COUNT(m.uid) AS num
		FROM hr_members m
		LEFT JOIN hr_resume r ON r.uid=m.uid
		WHERE m.utype=2$addsql");
	$sql = "SELECT m.uid,m.username,m.reg_time,r.audit,r.fullname,r.complete,r.refreshtime,r.education,r.education_cn,r.experience,r.experience_cn,r.birthdate 
		FROM hr_members m
		LEFT JOIN hr_resume r ON r.uid=m.uid
		WHERE m.utype=2$addsql
		$sortsql
		LIMIT $limit_start,".$num;
	$resumeList = $db->getall($sql);
	foreach ($resumeList as $key=>$value) {
		#是否完善
		$resumeList[$key]['complete_cn'] = $value['complete']<4 ? '不完善' : '完善';
		#简历状态
		if ($value['audit']==1) {
			$resumeList[$key]['audit_cn'] = '<span class="f_gre">审核通过</span>';
		} elseif ($value['audit']==2 || $value['audit']==4) {
			$resumeList[$key]['audit_cn'] = '<span class="f_gray">等待审核</span>';
		} elseif ($value['audit']==3) {
			$resumeList[$key]['audit_cn'] = '<span class="f_red">审核未通过</span>';
		}
		#年龄
		if ($value['birthdate']) {
			$value['birthdate'] = ceil((time() - $value['birthdate'])/(365*24*60*60));
		}
		$resumeList[$key]['birthdate'] = $value['birthdate'];
		#注册时间
		$resumeList[$key]['reg_time'] = $value['reg_time'] ? date('Y-m-d', $value['reg_time']): '';
		#刷新时间
		$resumeList[$key]['refreshtime'] = $value['refreshtime'] ? date('Y-m-d', $value['refreshtime']): '';
	}
	
	//分页
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
	$smarty->assign('eduCate', $eduCate);
	$smarty->assign('experienceCate', $experienceCate);
	$smarty->assign('resumeList', $resumeList);
	$smarty->assign('pageheader',"简历列表");
	$smarty->display('z/personal_list.htm');
}

//操作
if ($act == 'do') {
	$optype = $_POST['optype'];
	
	//审核
	if ($optype == 'audit_1' || $optype == 'audit_3') {
		$audit = ($optype == 'audit_1') ? 1 : 3;
		$returnurl = base64_decode($_POST['returnurl']);
		
		if (empty($_POST['uid'])) {
			adminmsg("请选择用户", 0);
		}
		
		z_admin_personal_audit($_POST['uid'], $audit);
		$link[0]['text'] = "返回列表";
		$link[0]['href'] = $returnurl;
		adminmsg('操作成功！',2);
	}
	
	//删除
	elseif ($optype == 'del') {
		if (empty($_POST['uid'])) {
			adminmsg("请选择用户", 0);
		}
		
		z_admin_personal_del($_POST['uid']);
		$link[0]['text'] = "返回列表";
		$link[0]['href'] = $returnurl;
		adminmsg('删除成功！',2);
	}
}

//编辑
elseif($act == 'edit')
{	
	$uid=intval($_GET['uid']);
	$u=get_user($uid);
	
	if (!empty($u))	{
		
		$_SESSION['auth_uid']=$u['uid'];
		$_SESSION['auth_username']=$u['username'];
		$_SESSION['auth_utype']=$u['utype'];
		
		z_goto($_CFG['site_domain'].'/user/personal/personal_resume.php');
	}	
} 

//授权
elseif ($act == 'management') {	
	$uid=intval($_GET['uid']);
	$u=get_user($uid);
	
	if (!empty($u))	{
		
		$_SESSION['auth_uid']=$u['uid'];
		$_SESSION['auth_username']=$u['username'];
		$_SESSION['auth_utype']=$u['utype'];
		
		z_goto($_CFG['site_domain'].'/user/personal/');
	}	
} 

//审核
elseif ($act == 'audit') {
	$uid = intval($_POST['uid']);
	$resume = z_resume($uid);;
	
	if (!empty($resume)) {
		$audit = intval($_POST['audit']);
		$audit_reason = trim($_POST['audit_reason']);
		$audit_reason_other = trim($_POST['audit_reason_other']);
		$audit_email = intval($_POST['audit_email']);
		$reason = $audit_reason_other ? $audit_reason_other : $audit_reason;
				
		//通过
		if ($audit == 1) {
			
			//不完整
			if($resume['complete'] < 4) {
				$link[0]['text'] = "返回";
				$link[0]['href'] = $_CFG['site_domain']."/user/resume.php?uid=$uid";
				adminmsg('简历不完整不能通过！',0,$link);
			}
			
			zt_personal_audit($uid);
		}
		
		$data = array(
			'audit'=>$audit,
			'audit_reason'=>$reason
		);
		updatetable('hr_resume', $data, array('uid'=>$uid));
		
		//发送职位申请邮件
		if ($audit == 1) {
			//+send email 申请职位
			$resume = z_resume($uid);
			$apply_list = z_jobs_apply_audit2($uid);
			
			if (!empty($apply_list)) {
				foreach ($apply_list as $value) {
					
					updatetable('hr_personal_jobs_apply', array('audit'=>1), array('did'=>$value['did']));
					
					$jobsInfo = z_jobs($value['jobs_id']);
					$mailArr = array(
						'to'=>$jobsInfo['email'],
						'from'=>$resume['email'],
						'fromName'=>$resume['fullname']
					);
					$data = array(
						'fullname'=>$resume['fullname'],
						'jobs_name'=>$jobsInfo['jobs_name']
					);
					z_mail('resume', $mailArr, $data, $uid);
				};
			}
			//end
		}
		
		//发送邮件
		if ($audit_email) {
			if ($audit == 1) {
				
				//最近岗位
				$t = time();
				$jobs_list_html = '';
				$sql = "SELECT id,jobs_name,uid,companyname,district_cn,education_cn,experience_cn,wage_min,wage_max,refreshtime
					FROM hr_jobs
					WHERE (audit=1 OR audit=4) AND display=1 AND (deadline=0 OR deadline>$t) AND setmeal_deadline>$t
					ORDER BY refreshtime DESC
					LIMIT 12";
				$jobs_list = $db->getall($sql);
				$jobs_list_html = '';
				foreach ($jobs_list as $key=>$job) {
					$style = ($key % 2 == 0) ? 'background:#fff' : 'background:#E6EFF9';
					$job['wage_cn'] = z_wage_cn($job['wage_min'], $job['wage_max']);
					$job['refreshtime'] = date('Y-m-d', $job['refreshtime']);
					$jobs_list_html .= '<tr style="'.$style.'">
								<td style="text-indent:2em; border-left:1px solid #eae4e4;"><a href="'.$_CFG['site_domain'].'/jobs/jobs-show.php?id='.$job['id'].'" target="_blank">'.$job['jobs_name'].'</a></td>
								<td style="text-indent:4em;"><a href="'.$_CFG['site_domain'].'/company/company-show.php?id='.$job['uid'].'" target="_blank">'.$job['companyname'].'</a></td>
								<td align="center">'.$job['district_cn'].' </td>
								<td align="center">'.$job['education_cn'].' </td>
								<td align="center">'.$job['experience_cn'].' </td>
								<td align="center">'.$job['wage_cn'].' </td>
								<td style="border-right:1px solid #eae4e4;">'.$job['refreshtime'].' </td>
						</tr>';
				}
								
				//+send email 简历审核通过
				$mailArr = array(
					'to'=>$resume['email']
				);
				$data = array(
					'fullname'=>$resume['fullname'],
					'jobs_list_html'=>$jobs_list_html
				);
				z_mail('resume_audit_allow', $mailArr, $data);
				//end
			} elseif ($audit == 3) {
				//+send email 简历审核不通过
				$mailArr = array(
					'to'=>$resume['email']
				);
				$data = array(
					'fullname'=>$resume['fullname'],
					'reason'=>$reason
				);
				z_mail('resume_audit_notallow', $mailArr, $data);
				//end
			}
		}
		
		if ($audit == 1) {
			//+sene message 简历审核通过
			z_message('resume_audit_allow', $uid);
			//end
		} elseif ($audit == 3) {
			//+sene message 简历审核不通过
			z_message('resume_audit_notallow', $uid, array('reason'=>$reason));
			//end
		}
		
		$link[0]['text'] = "返回";
		$link[0]['href'] = $_CFG['site_domain']."/user/resume.php?uid=$uid";
		adminmsg('操作成功！',2);
	}
}
else if($act == 'resume'){
	
	$id = intval($_POST['id']);
	$val = mb_convert_encoding($_POST['value'],'gbk', 'utf-8');
	$res = 0;
	
	if ($val == ''){
		exit(0);
	}
	
	switch ($_POST['type']){
		case 'work':{//工作经历
			$res = updatetable(table('resume_work'), array('achievements'=>$val), array('id'=>$id));
			break;
		}	
		case 'edu':{//教育经历
			$res = updatetable(table('resume_education'), array('content'=>$val), array('id'=>$id));
			break;
		}
		case 'spe':{//优势特长,tmp
			$res = updatetable(table('resume_intention'), array('specialty_tmp'=>$val), array('id'=>$id));
			break;
		}
		case 'self':{//自我简介,tmp
			$res = updatetable(table('resume_intention'), array('self_evaluation_tmp'=>$val), array('id'=>$id));
			break;
		}
		default:
			break;
	}
	
	echo $res;exit;
}




