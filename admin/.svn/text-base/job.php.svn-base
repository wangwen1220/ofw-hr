<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
$act = trim($_GET['act']);
$time = time();

if (in_array($act, array('list', 'edit'))) {
	//待审核公司
	$audit2_com_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_company_profile WHERE audit=2 OR audit=4");
	$smarty->assign('audit2_com_num', $audit2_com_num);
	
	//待审核职位
	$audit2_job_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE audit=2 OR audit=4");
	$smarty->assign('audit2_job_num', $audit2_job_num);
}

//职位列表
if ($act == 'list') {
	
	//权限
	check_permissions($_SESSION['admin_purview'], "jobs_show");
	
	//行业分类
	$tradeCate = get_category_zt('QS_trade');
	$smarty->assign('tradeCate', $tradeCate);
	
	//搜索
	$jobList = array();
	$search = array();
	$from = isset($_GET['from']) ? trim($_GET['from']) : '';
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
	$limit_start = ($page - 1) * $num;
		
	$search['id'] = isset($_GET['id']) ? trim($_GET['id']) : '';
	$search['jobname'] = isset($_GET['jobname']) ? trim($_GET['jobname']) : '';
	$search['companyname'] = isset($_GET['companyname']) ? trim($_GET['companyname']) : '';
	$search['addtime_from'] = isset($_GET['addtime_from']) ? trim($_GET['addtime_from']) : '';
	$search['addtime_to'] = isset($_GET['addtime_to']) ? trim($_GET['addtime_to']) : '';
	$search['deadline_from'] = isset($_GET['deadline_from']) ? trim($_GET['deadline_from']) : '';
	$search['deadline_to'] = isset($_GET['deadline_to']) ? trim($_GET['deadline_to']) : '';
	$search['trade'] = isset($_GET['trade']) ? trim($_GET['trade']) : 0;
	if ($from == 'audit') {
		$search['audit'] = 2;
	} else {
		$search['audit'] = isset($_GET['audit']) ? intval($_GET['audit']) : 0;
	}
	$search['expire'] = isset($_GET['expire']) ? intval($_GET['expire']) : 0;
	$addsql = '';
	
	if ($search['id']) {
		$t = $search['id'];
		$addsql .= " AND j.id='$t'";
	}
	if ($search['jobname']) {
		$t = $search['jobname'];
		$addsql .= " AND j.jobs_name LIKE '%$t%'";
	}
	if ($search['companyname']) {
		$t = $search['companyname'];
		$addsql .= " AND j.companyname LIKE '%$t%'";
	}
	if ($search['addtime_from']) {
		$t = strtotime($search['addtime_from']);
		$addsql .= " AND j.addtime>='$t'";
	}
	if ($search['addtime_to']) {
		$t = strtotime($search['addtime_to'].' 23:59:59');
		$addsql .= " AND j.addtime<='$t'";
	}
	if ($search['deadline_from']) {
		$t = strtotime($search['deadline_from']);
		$addsql .= " AND j.deadline>='$t'";
	}
	if ($search['deadline_to']) {
		$t = strtotime($search['deadline_to'].' 23:59:59');
		$addsql .= " AND j.deadline<='$t'";
	}
	if ($search['audit']) {
		$t = $search['audit'];
		$addsql .= $t == 2 ? " AND (j.audit='$t' || j.audit=4)" : " AND j.audit='$t'";
	}
	if ($search['expire']) {
		$t = $search['expire'];
		$addsql .= $t == 2 ? " AND ((j.deadline=0 OR j.deadline>=$time) AND j.setmeal_deadline>=$time)" : " AND (j.setmeal_deadline<$time OR (j.deadline>0 AND j.deadline<$time))";
	}
	if ($search['trade']) {
		$t = $search['trade'];
		$addsql .= " AND j.trade LIKE ',$t,'";
	}
	
	//列表
	//用户名，注册时间，公司状态，公司名，联系人，联系电话，到期时间
	$total = $db->get_total("SELECT COUNT(*) AS num
		FROM hr_jobs j
		WHERE (company_audit=1 OR company_audit=4)$addsql");
	$sql = "SELECT j.*,jc.contact,jc.telephone,jc.address,jc.email
		FROM hr_jobs j
		LEFT JOIN hr_jobs_contact jc ON jc.pid=j.id
		WHERE (company_audit=1 OR company_audit=4)$addsql
		ORDER BY j.id DESC
		LIMIT $limit_start,".$num;
	$jobList = $db->getall($sql);
	foreach ($jobList as $key=>$value) {
		#公司状态
		if ($value['audit']==1) {
			$jobList[$key]['audit_cn'] = '<span class="f_gre">审核通过</span>';
		} elseif ($value['audit']==2 || $value['audit']==4) {
			$jobList[$key]['audit_cn'] = '<span class="f_gray">等待审核</span>';
		} elseif ($value['audit']==3) {
			$jobList[$key]['audit_cn'] = '<span class="f_red">审核未通过</span>';
		}
		#注册时间
		$jobList[$key]['addtime'] = date('Y-m-d', $value['addtime']);
		#到期时间
		$jobList[$key]['refreshtime'] = date('Y-m-d', $value['refreshtime']);
		$jobList[$key]['deadline'] = $value['deadline'] ? date('Y-m-d', $value['deadline']): '永久';
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
	$smarty->assign('jobList', $jobList);
	$smarty->assign('pageheader',"职位列表");
	$smarty->display('z/job_list.htm');
}

//操作
if ($act == 'do') {
	$optype = $_POST['optype'];
	
	//审核
	if ($optype == 'audit_1' || $optype == 'audit_3') {
		$audit = ($optype == 'audit_1') ? 1 : 3;
		$returnurl = base64_decode($_POST['returnurl']);
		
		if (empty($_POST['jobid'])) {
			adminmsg("请选择职位", 0);
		}
		
		z_admin_job_audit($_POST['jobid'], $audit);
		$link[0]['text'] = "返回列表";
		$link[0]['href'] = $returnurl;
		adminmsg('操作成功！',2);
	}
	
	//刷新
	if ($optype == 'refresh') {
		$returnurl = base64_decode($_POST['returnurl']);
		
		if (empty($_POST['jobid'])) {
			adminmsg("请选择职位", 0);
		}
		
		z_admin_job_refresh($_POST['jobid']);
		$link[0]['text'] = "返回列表";
		$link[0]['href'] = $returnurl;
		adminmsg('操作成功！',2);
	}
	
	//删除
	elseif ($optype == 'del') {
		if (empty($_POST['jobid'])) {
			adminmsg("请选择职位", 0);
		}
		
		z_admin_job_del($_POST['jobid']);
		$link[0]['text'] = "返回列表";
		$link[0]['href'] = $returnurl;
		adminmsg('删除成功！',2);
	}
}


//编辑
elseif($act == 'edit') {	
	$jobid = intval($_GET['jobid']);
	$jobs = z_jobs($jobid);
	
	$tradeCate = get_category_zt('QS_trade');
	$eduCate = get_category_zt('QS_education');
	$jobNatureCate = get_category_zt('QS_jobs_nature');
	$experienceCate = get_category_zt('QS_experience');
	$jobCate = get_parent_cagegory('category_jobs');#1级职位分类
	
	//行业处理
	$tradeCate_Left = $tradeCate_Right = array();
	foreach ($tradeCate as $key=>$value) {
		$t = trim($jobs['trade'], ',');
		$t = explode(",", $t);
		if (in_array($value['c_id'], $t)) {
			$tradeCate_Right[] = $value;
		} else {
			$tradeCate_Left[] = $value;
		}
		
	}
	
	//处理岗位
	$category_Right = array();
	$category = trim($jobs['category'], ',');
	$category_cn = trim($jobs['category_cn'], ',');
	$t1 = explode(',', $category);
	$t2 = explode(',', $category_cn);
	foreach ($t1 as $key=>$value) {
		if ($value) {
			$category_Right[] = array('id'=>$t1[$key], 'categoryname'=>$t2[$key]);
		}
	}
	
	//专业
	$specialityCate = get_parent_cagegory('category_speciality');#1级专业分类
	$speciality_Right = array();
	$speciality = trim($jobs['speciality'], ',');
	$speciality_cn = trim($jobs['request']);
	$t1 = explode(',', $speciality);
	$t2 = explode(',', $speciality_cn);
	foreach ($t1 as $key=>$value) {
		if ($value) {
			$speciality_Right[] = array('id'=>$t1[$key], 'categoryname'=>$t2[$key]);
		}
	}
	$smarty->assign('specialityCate',$specialityCate);
	$smarty->assign('speciality_Right',$speciality_Right);
	
	//户籍处理
		//地区省
		$select_dis = array();
		$res_dis = get_parent_cagegory('category_district');
		foreach ($res_dis as $v){
			$select_dis[$v['id']] = $v['categoryname'];
		}
		$smarty->assign('select_district_val',$jobs['cencus_province']);
		$smarty->assign('select_district',$select_dis);
		
		if ($jobs['cencus_province'] != ''){
			//地区市
			$select_sdis = array();
			$res_sdis = get_subcategory('category_district',$jobs['cencus_province']);
			foreach ($res_sdis as $v){
				$select_sdis[$v['id']] = $v['categoryname'];
			}
			
			$smarty->assign('select_sdistrict_val',$jobs['cencus_city']);
			$smarty->assign('select_sdistrict',$select_sdis);
		}
	//END	
	
	$jobs['nature'] = z_queue_resolve($jobs['nature'], 'show');
	
	$jobs['contents_tmp'] && $jobs['contents'] = $jobs['contents_tmp'];
	
	$smarty->assign('tradeCate',$tradeCate);
	$smarty->assign('tradeCate_Right',$tradeCate_Right);
	$smarty->assign('tradeCate_Left',$tradeCate_Left);
	$smarty->assign('eduCate',$eduCate);
	$smarty->assign('jobNatureCate',$jobNatureCate);
	$smarty->assign('experienceCate',$experienceCate);
	$smarty->assign('jobCate',$jobCate);
	$smarty->assign('category_Right',$category_Right);
	$smarty->assign('company_profile',$company_profile);
	
	
	$smarty->assign('jobs', $jobs);
	$smarty->assign('pageheader',"职位修改");
	$smarty->display('z/job_edit.htm');
	
	
	/*$u = get_user($job['uid']);
	
	if (!empty($u))	{
		
		$_SESSION['auth_uid']=$u['uid'];
		$_SESSION['auth_username']=$u['username'];
		$_SESSION['auth_utype']=$u['utype'];
		
		z_goto($_CFG['site_domain']."/user/company/company_jobs.php?act=editjobs&id=$jobid");
	}*/	
}

elseif ($act == 'edit_save') {
	$id=intval($_POST['id']);
	
	$jobs = z_jobs($id);
	
	$tradeCate = get_category_zt('QS_trade');
	$jobCate = z_get_category_jobs();
	$experienceCate = get_category_zt('QS_experience');
	$eduCate = get_category_zt('QS_education');
	
	
	//写数据
	/////////////////////////表jobs//////////////////////
	#职位名称
	$setsqlarr['jobs_name'] = !empty($_POST['jobs_name'])?trim($_POST['jobs_name']):adminmsg('您没有填写职位名称！',1);
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
		adminmsg('您没有选择行业！',1);
	}
	#工作岗位
	if (!empty($_POST['value_category'])) {
		$category_id_arr = array();
		$category_name_arr = array();
		foreach ($_POST['value_category'] as $key=>$value) {
			foreach ($jobCate as $k=>$v) {
				if ($v['id'] == $value) {
					$category_id_arr[] = $v['id'];
					$category_name_arr[] = $v['categoryname'];
				}
			}
		}
		$setsqlarr['category'] = ",".implode(",", $category_id_arr).",";
		$setsqlarr['subclass'] = z_job_category_parentid($category_id_arr);
		$setsqlarr['category_cn'] = ",".implode(",", $category_name_arr).",";
	} else {
		adminmsg('您没有选择工作岗位！',1);
	}
	
	//专业
	if (!empty($_POST['value_speciality'])) {
		$specialityCate = z_get_category_speciality();
		$speciality_id_arr = array();
		$speciality_name_arr = array();
		foreach ($_POST['value_speciality'] as $key=>$value) {
			foreach ($specialityCate as $k=>$v) {
				if ($v['id'] == $value) {
					$speciality_id_arr[] = $v['id'];
					$speciality_name_arr[] = $v['categoryname'];
				}
			}
		}
		$setsqlarr['speciality'] = ",".implode(",", $speciality_id_arr).",";
		$setsqlarr['speciality_parent'] = z_speciality_category_parentid($speciality_id_arr);
		$setsqlarr['request'] = implode(",", $speciality_name_arr);
	} else {
		$setsqlarr['speciality'] = $setsqlarr['speciality_parent'] = $setsqlarr['request'] = '';
	}
	#工作地区
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('请选择工作地区！',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	#招聘对象
	$setsqlarr['nature']=  z_queue_combination($_POST['nature']);
	$setsqlarr['nature_cn']=  z_queue_combination_cn($_POST['nature']);
	#招聘人数
	$setsqlarr['amount']=intval($_POST['amount']);
	#薪酬
	$setsqlarr['wage_min']=intval($_POST['wage_min']);
	$setsqlarr['wage_max']=intval($_POST['wage_max']);
	#能提供住宿
	$setsqlarr['room'] = empty($_POST['room']) ? 0 : 1;
	#年龄要求
	$setsqlarr['age_min'] = intval($_POST['age_min']); 
	$setsqlarr['age_max'] = intval($_POST['age_max']);
	#性别
	if ($_POST['sex'] == 1) {
		$setsqlarr['sex'] = 1;
		$setsqlarr['sex_cn'] = '男';
	} elseif ($_POST['sex'] == 2) {
		$setsqlarr['sex'] = 2;
		$setsqlarr['sex_cn'] = '女';
	} else {
		$setsqlarr['sex'] = 0;
		$setsqlarr['sex_cn'] = '不限';
	}
	#经验
	$t = intval($_POST['experience']);
	$setsqlarr['experience'] = $t;
	switch ($t) {
		case 0:$setsqlarr['experience_cn'] = '不限';break;
		case 1:$setsqlarr['experience_cn'] = '无经验';break;
		case 2:$setsqlarr['experience_cn'] = '1年以下';break;
		case 3:$setsqlarr['experience_cn'] = '1-3年';break;
		case 4:$setsqlarr['experience_cn'] = '3-5年';break;
		case 5:$setsqlarr['experience_cn'] = '5-10年';break;
		case 6:$setsqlarr['experience_cn'] = '10年以上';break;
	}
	#户口所在地
	$setsqlarr['cencus_province'] = intval($_POST['cencus_province']);
	$setsqlarr['cencus_city'] = intval($_POST['cencus_city']);
	#英语水平
	$setsqlarr['english'] = intval($_POST['english']);
	#计算机能力
	$setsqlarr['computer'] = intval($_POST['computer']);
	#学历要求
	$t = intval($_POST['education']);
	$setsqlarr['education'] = $t;
	if ($t) {
		foreach ($eduCate as $key=>$value) {
			if($value['c_id'] == $t) {
				$setsqlarr['education_cn'] = $value['c_name'];
			}
		}
	} else {
		$setsqlarr['education_cn'] = '不限';
	}
	#有效期
	$setsqlarr['effect'] = intval($_POST['effect']);
	#职位描述/要求
	if ($jobs['audit'] == 4) {
		$setsqlarr['contents_tmp']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('您没有填写职位描述！',1);
	} else {
		$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('您没有填写职位描述！',1);
	}
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	//其他
	switch ($setsqlarr['effect']) {
		case 0:$setsqlarr['deadline'] = 0;break;#长期有效
		case 1:$setsqlarr['deadline'] = $timestamp+1*30*24*60*60;break;#1个月
		case 2:$setsqlarr['deadline'] = $timestamp+3*30*24*60*60;break;#3个月
		case 3:$setsqlarr['deadline'] = $timestamp+6*30*24*60*60;break;#6个月
		case 4:$setsqlarr['deadline'] = $timestamp+12*30*24*60*60;break;#1年
	}
	
	
	/////////////////////////表jobs_contact//////////////////////
	#联系人	
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('您没有填写联系人！',1);
	#联系电话
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('您没有填写联系电话！',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	#邮箱地址
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('您没有填写联系邮箱！',1);
		
	
	////////////////////////////////////////////////////////////
	if (!updatetable(table('jobs'), $setsqlarr," id='{$id}'")) showmsg("保存失败！",0);
	if (!updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")) showmsg("保存失败！",0);
	
	$link[0]['text'] = "返回";
	$link[0]['href'] = 'job.php?act=edit&jobid='.$id;
	adminmsg("修改成功！",2,$link);
}

//审核
elseif ($act == 'audit') {
	$jobid=intval($_POST['id']);
	
	$jobs = z_jobs($jobid);
	
	if (!empty($jobs)) {
		$audit = intval($_POST['audit']);
		$audit_reason = trim($_POST['audit_reason']);
		$audit_reason_other = trim($_POST['audit_reason_other']);
		$audit_email = intval($_POST['audit_email']);
		$reason = $audit_reason_other ? $audit_reason_other : $audit_reason;
		
		$data = array(
			'audit'=>$audit
		);
		
		if ($audit == 1) {
			$db->query("UPDATE hr_jobs SET `contents`=`contents_tmp`,`contents_tmp`='' WHERE id=$jobid AND contents_tmp!=''");
		}
		
		updatetable('hr_jobs', $data, array('id'=>$jobid));
		
		//发送邮件
		if ($audit_email) {
			if ($audit == 1) {
				//+send email 职位审核通过
				$mailArr = array(
					'to'=>$jobs['email']
				);
				$data = array(
					'companyname'=>$jobs['companyname'],
					'jobs_name'=>$jobs['jobs_name']
				);
				z_mail('job_audit_allow', $mailArr, $data);
				//end
			} elseif ($audit == 3) {
				//+send email 职位审核不通过
				$mailArr = array(
					'to'=>$jobs['email']
				);
				$data = array(
					'companyname'=>$jobs['companyname'],
					'jobs_name'=>$jobs['jobs_name'],
					'reason'=>$reason
				);
				z_mail('job_audit_notallow', $mailArr, $data);
				//end
			}
		}
		
		if ($audit == 1) {
			//+sene message 职位审核通过
			z_message('job_audit_allow', $jobs['uid'], array('jobs_name'=>$jobs['jobs_name']));
			//end
		} elseif ($audit == 3) {
			//+sene message 职位审核不通过
			z_message('job_audit_notallow', $jobs['uid'], array('jobs_name'=>$jobs['jobs_name'], 'reason'=>$reason));
			//end
		}
		
		$link[0]['text'] = "返回";
		$link[0]['href'] = "job.php?act=edit&jobid=$jobid";
		adminmsg('操作成功！',2);
	}
}