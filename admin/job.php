<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
$act = trim($_GET['act']);
$time = time();

if (in_array($act, array('list', 'edit'))) {
	//����˹�˾
	$audit2_com_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_company_profile WHERE audit=2 OR audit=4");
	$smarty->assign('audit2_com_num', $audit2_com_num);
	
	//�����ְλ
	$audit2_job_num = $db->get_total("SELECT COUNT(*) AS num FROM hr_jobs WHERE audit=2 OR audit=4");
	$smarty->assign('audit2_job_num', $audit2_job_num);
}

//ְλ�б�
if ($act == 'list') {
	
	//Ȩ��
	check_permissions($_SESSION['admin_purview'], "jobs_show");
	
	//��ҵ����
	$tradeCate = get_category_zt('QS_trade');
	$smarty->assign('tradeCate', $tradeCate);
	
	//����
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
	
	//�б�
	//�û�����ע��ʱ�䣬��˾״̬����˾������ϵ�ˣ���ϵ�绰������ʱ��
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
		#��˾״̬
		if ($value['audit']==1) {
			$jobList[$key]['audit_cn'] = '<span class="f_gre">���ͨ��</span>';
		} elseif ($value['audit']==2 || $value['audit']==4) {
			$jobList[$key]['audit_cn'] = '<span class="f_gray">�ȴ����</span>';
		} elseif ($value['audit']==3) {
			$jobList[$key]['audit_cn'] = '<span class="f_red">���δͨ��</span>';
		}
		#ע��ʱ��
		$jobList[$key]['addtime'] = date('Y-m-d', $value['addtime']);
		#����ʱ��
		$jobList[$key]['refreshtime'] = date('Y-m-d', $value['refreshtime']);
		$jobList[$key]['deadline'] = $value['deadline'] ? date('Y-m-d', $value['deadline']): '����';
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
	$smarty->assign('jobList', $jobList);
	$smarty->assign('pageheader',"ְλ�б�");
	$smarty->display('z/job_list.htm');
}

//����
if ($act == 'do') {
	$optype = $_POST['optype'];
	
	//���
	if ($optype == 'audit_1' || $optype == 'audit_3') {
		$audit = ($optype == 'audit_1') ? 1 : 3;
		$returnurl = base64_decode($_POST['returnurl']);
		
		if (empty($_POST['jobid'])) {
			adminmsg("��ѡ��ְλ", 0);
		}
		
		z_admin_job_audit($_POST['jobid'], $audit);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $returnurl;
		adminmsg('�����ɹ���',2);
	}
	
	//ˢ��
	if ($optype == 'refresh') {
		$returnurl = base64_decode($_POST['returnurl']);
		
		if (empty($_POST['jobid'])) {
			adminmsg("��ѡ��ְλ", 0);
		}
		
		z_admin_job_refresh($_POST['jobid']);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $returnurl;
		adminmsg('�����ɹ���',2);
	}
	
	//ɾ��
	elseif ($optype == 'del') {
		if (empty($_POST['jobid'])) {
			adminmsg("��ѡ��ְλ", 0);
		}
		
		z_admin_job_del($_POST['jobid']);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $returnurl;
		adminmsg('ɾ���ɹ���',2);
	}
}


//�༭
elseif($act == 'edit') {	
	$jobid = intval($_GET['jobid']);
	$jobs = z_jobs($jobid);
	
	$tradeCate = get_category_zt('QS_trade');
	$eduCate = get_category_zt('QS_education');
	$jobNatureCate = get_category_zt('QS_jobs_nature');
	$experienceCate = get_category_zt('QS_experience');
	$jobCate = get_parent_cagegory('category_jobs');#1��ְλ����
	
	//��ҵ����
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
	
	//�����λ
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
	
	//רҵ
	$specialityCate = get_parent_cagegory('category_speciality');#1��רҵ����
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
	
	//��������
		//����ʡ
		$select_dis = array();
		$res_dis = get_parent_cagegory('category_district');
		foreach ($res_dis as $v){
			$select_dis[$v['id']] = $v['categoryname'];
		}
		$smarty->assign('select_district_val',$jobs['cencus_province']);
		$smarty->assign('select_district',$select_dis);
		
		if ($jobs['cencus_province'] != ''){
			//������
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
	$smarty->assign('pageheader',"ְλ�޸�");
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
	
	
	//д����
	/////////////////////////��jobs//////////////////////
	#ְλ����
	$setsqlarr['jobs_name'] = !empty($_POST['jobs_name'])?trim($_POST['jobs_name']):adminmsg('��û����дְλ���ƣ�',1);
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
		adminmsg('��û��ѡ����ҵ��',1);
	}
	#������λ
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
		adminmsg('��û��ѡ������λ��',1);
	}
	
	//רҵ
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
	#��������
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	#��Ƹ����
	$setsqlarr['nature']=  z_queue_combination($_POST['nature']);
	$setsqlarr['nature_cn']=  z_queue_combination_cn($_POST['nature']);
	#��Ƹ����
	$setsqlarr['amount']=intval($_POST['amount']);
	#н��
	$setsqlarr['wage_min']=intval($_POST['wage_min']);
	$setsqlarr['wage_max']=intval($_POST['wage_max']);
	#���ṩס��
	$setsqlarr['room'] = empty($_POST['room']) ? 0 : 1;
	#����Ҫ��
	$setsqlarr['age_min'] = intval($_POST['age_min']); 
	$setsqlarr['age_max'] = intval($_POST['age_max']);
	#�Ա�
	if ($_POST['sex'] == 1) {
		$setsqlarr['sex'] = 1;
		$setsqlarr['sex_cn'] = '��';
	} elseif ($_POST['sex'] == 2) {
		$setsqlarr['sex'] = 2;
		$setsqlarr['sex_cn'] = 'Ů';
	} else {
		$setsqlarr['sex'] = 0;
		$setsqlarr['sex_cn'] = '����';
	}
	#����
	$t = intval($_POST['experience']);
	$setsqlarr['experience'] = $t;
	switch ($t) {
		case 0:$setsqlarr['experience_cn'] = '����';break;
		case 1:$setsqlarr['experience_cn'] = '�޾���';break;
		case 2:$setsqlarr['experience_cn'] = '1������';break;
		case 3:$setsqlarr['experience_cn'] = '1-3��';break;
		case 4:$setsqlarr['experience_cn'] = '3-5��';break;
		case 5:$setsqlarr['experience_cn'] = '5-10��';break;
		case 6:$setsqlarr['experience_cn'] = '10������';break;
	}
	#�������ڵ�
	$setsqlarr['cencus_province'] = intval($_POST['cencus_province']);
	$setsqlarr['cencus_city'] = intval($_POST['cencus_city']);
	#Ӣ��ˮƽ
	$setsqlarr['english'] = intval($_POST['english']);
	#���������
	$setsqlarr['computer'] = intval($_POST['computer']);
	#ѧ��Ҫ��
	$t = intval($_POST['education']);
	$setsqlarr['education'] = $t;
	if ($t) {
		foreach ($eduCate as $key=>$value) {
			if($value['c_id'] == $t) {
				$setsqlarr['education_cn'] = $value['c_name'];
			}
		}
	} else {
		$setsqlarr['education_cn'] = '����';
	}
	#��Ч��
	$setsqlarr['effect'] = intval($_POST['effect']);
	#ְλ����/Ҫ��
	if ($jobs['audit'] == 4) {
		$setsqlarr['contents_tmp']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	} else {
		$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	}
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	//����
	switch ($setsqlarr['effect']) {
		case 0:$setsqlarr['deadline'] = 0;break;#������Ч
		case 1:$setsqlarr['deadline'] = $timestamp+1*30*24*60*60;break;#1����
		case 2:$setsqlarr['deadline'] = $timestamp+3*30*24*60*60;break;#3����
		case 3:$setsqlarr['deadline'] = $timestamp+6*30*24*60*60;break;#6����
		case 4:$setsqlarr['deadline'] = $timestamp+12*30*24*60*60;break;#1��
	}
	
	
	/////////////////////////��jobs_contact//////////////////////
	#��ϵ��	
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	#��ϵ�绰
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	#�����ַ
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
		
	
	////////////////////////////////////////////////////////////
	if (!updatetable(table('jobs'), $setsqlarr," id='{$id}'")) showmsg("����ʧ�ܣ�",0);
	if (!updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")) showmsg("����ʧ�ܣ�",0);
	
	$link[0]['text'] = "����";
	$link[0]['href'] = 'job.php?act=edit&jobid='.$id;
	adminmsg("�޸ĳɹ���",2,$link);
}

//���
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
		
		//�����ʼ�
		if ($audit_email) {
			if ($audit == 1) {
				//+send email ְλ���ͨ��
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
				//+send email ְλ��˲�ͨ��
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
			//+sene message ְλ���ͨ��
			z_message('job_audit_allow', $jobs['uid'], array('jobs_name'=>$jobs['jobs_name']));
			//end
		} elseif ($audit == 3) {
			//+sene message ְλ��˲�ͨ��
			z_message('job_audit_notallow', $jobs['uid'], array('jobs_name'=>$jobs['jobs_name'], 'reason'=>$reason));
			//end
		}
		
		$link[0]['text'] = "����";
		$link[0]['href'] = "job.php?act=edit&jobid=$jobid";
		adminmsg('�����ɹ���',2);
	}
}