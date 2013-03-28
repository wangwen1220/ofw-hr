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
$smarty->assign('leftmenu',"jobs");

//职位列表
if ($act=='jobs') {
	$uid = $_SESSION['uid'];
	$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
	$jobtype = isset($_GET['jobtype']) ? intval($_GET['jobtype']) : 0;
	$provice = isset($_GET['provice']) ? intval($_GET['provice']) : 0;
	$num = isset($_GET['num']) ? max(intval($_GET['num']), 10) : 10;
	$stop = isset($_GET['stop']) ? intval($_GET['stop']) : 0;
	$key = isset($_GET['key']) ? trim($_GET['key']) : '';
	$addsql = '';
	$t = time();
	
	//省
	$proviceList = z_province();
	$smarty->assign('proviceList', $proviceList);
	
	//关键字
	if ($key) {
		$addsql .= " AND jobs_name LIKE '%$key%'";
	}
	
	//职位分类
	switch ($jobtype) {
		//发布中
		case 1:$addsql .= " AND (audit=1 AND display=1 AND ((deadline=0 OR deadline>$t) AND setmeal_deadline>$t))";break;
		//草稿
		case 2:$addsql .= " AND (audit=1 AND display=4 AND ((deadline=0 OR deadline>$t) AND setmeal_deadline>$t))";break;
		//待发布
		case 3:$addsql .= " AND (audit=1 AND display=3 AND ((deadline=0 OR deadline>$t) AND setmeal_deadline>$t))";break;
		//待审核
		case 4:$addsql .= " AND (audit=2 OR audit=4)";break;
		//已过期
		case 5:$addsql .= " AND (audit=1 AND ((deadline>0 AND deadline<$t) OR setmeal_deadline<$t))";break;
		//已停止
		case 6:$addsql .= " AND (audit=1 AND display=2 AND ((deadline=0 OR deadline>$t) AND setmeal_deadline>$t))";break;
		//审核未通过
		case 7:$addsql .= " AND audit=3";break;
	}
	
	//省
	if ($provice) {
		$addsql .= " AND district=$provice";
	}
	
	//显示过期和已停止的职位	
	if ($stop) {
		$addsql .= " AND (audit=1 AND ((deadline<$t OR setmeal_deadline<$t) OR display=2))";
	}
	
	//总数
	$sql = "SELECT COUNT(*) num FROM hr_jobs WHERE uid='$uid'$addsql";
	$total = $db->get_total($sql);
	
	//列表数据
	$limit_start = ($page - 1) * $num;
	$sql = "SELECT id,jobs_name,audit,display,addtime,refreshtime,district_cn,deadline,setmeal_deadline FROM hr_jobs 
		WHERE uid='$uid'$addsql
		ORDER BY sort ASC
		LIMIT $limit_start,$num";
	$jobs = $db->getall($sql);
	
	foreach ($jobs as $key=>$value) {
		$jobs[$key]['addtime'] = date('Y-m-d', $value['addtime']);
		$jobs[$key]['refreshtime'] = date('Y-m-d', $value['refreshtime']);
		
		//显示状态
		/*规则：
			A．	待审核，修改待审，则待审核
			B．	审核通过且已过期，则已过期
			C．	审核通过且未过期，则发布中/草稿/待发布/已停止
			D．	审核未通过，则审核未通过
		*/
		$show_status_style = 'f_gray';
		if ($value['audit']==2 || $value['audit']==4) {
			$show_status = '待审核';
			$show_status_style = 'f_blue';
		} elseif ($value['audit']==1) {
			if (($value['deadline']>0 && $value['deadline'] < $t) || $value['setmeal_deadline'] < $t) {
				$show_status = '已过期';
			} else {
				switch ($value['display']) {
					case 1: $show_status='发布中';$show_status_style = 'f_black';break;
					case 2: $show_status='已停止';$show_status_style = 'f_red';break;
					case 3: $show_status='待发布';break;
					case 4: $show_status='草稿';break;
				}
			}
		} else {
			$show_status = '审核未通过';
		}
		
		$jobs[$key]['show_status'] = $show_status;
		$jobs[$key]['show_status_style'] = $show_status_style;
		
		//申请数
		$jobs[$key]['apply_num'] = $db->get_total("SELECT COUNT(*) num FROM hr_personal_jobs_apply WHERE jobs_id='".$value['id']."'");
	}
	
	//分页
	$pageHTML = dPage_2($total, $page, 'company_jobs.php?act=jobs', $num);	
	
	$smarty->assign('title','职位管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('url_jobtype', rebuild_url('jobtype'));
	$smarty->assign('url_provice', rebuild_url('provice'));
	$smarty->assign('url_num', rebuild_url('num'));
	$smarty->assign('url_stop', rebuild_url('stop'));
	$smarty->assign('jobs', $jobs);
	$smarty->assign('total', $total);
	$smarty->assign('jobtype', $jobtype);
	$smarty->assign('provice', $provice);
	$smarty->assign('num', $num);
	$smarty->assign('stop', $stop);
	$smarty->assign('pageHTML', $pageHTML);
	$smarty->assign('refer', $_SERVER['REQUEST_URI']);
	$smarty->display('member_company/company_jobs.htm');
}

//排序
elseif ($act == 'sort') {
	$uid = $_SESSION['uid'];
	$sql = "SELECT id,jobs_name,addtime,refreshtime,sort 
		FROM hr_jobs 
		WHERE uid='$uid'
		ORDER BY sort ASC";
	$jobs = $db->getall($sql);
	foreach ($jobs as $key=>$value) {
		$jobs[$key]['addtime'] = date('Y-m-d', $value['addtime']);
		$jobs[$key]['refreshtime'] = date('Y-m-d', $value['refreshtime']);
	}
	
	$smarty->assign('jobs', $jobs);
	$smarty->assign('title','职位排序 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_jobs_sort.htm');
}

//排序保存
elseif ($act == 'sort_save') {
	$uid = $_SESSION['uid'];
	foreach ($_POST as $key=>$value) {
		$t = explode('_', $key);
		if ($t[0] == 'jobsid') {
			$jobsid = intval($t[1]);
			$sort = intval($value);
			updatetable('hr_jobs', array('sort'=>$sort), array('id'=>$jobsid, 'uid'=>$uid));
		}
	}	
	
	$link[0]['text'] = "返回";
	$link[0]['href'] = 'company_jobs.php?act=sort';
	showmsg("操作成功！",1,$link);
}

//查找招聘信息
elseif ($act=='search') {
	$smarty->assign('title','职位管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->display('member_company/company_jobs_search.htm');
}

//职位列表页操作
elseif ($act=='jobs_save') {
	$opname = $_POST['opname'];
	$refer = $_POST['refer'];
	$idarr = $_POST['jobslist'];
	
	
	if (!empty($idarr)) {
		$ids = implode("','", $idarr);
		$uid = $_SESSION['uid'];
		$t = time();
		$dayNow = strtotime(date('Y-m-d 00:00:00', $t));
	
		//刷新
		if ($opname=='refresh') {
			
			//权限判断
			$expire = z_company_expire($_SESSION['uid']);
			if ($expire) {
				$link[0]['text'] = "返回";
				$link[0]['href'] = 'company_index.php';
				showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0,$link);
			}
			
			//取出要刷新的职位数据
			$id_ok = array();
			$id_day = array();
			$list = $db->getall("SELECT id,audit,display,addtime,refreshtime,deadline,setmeal_deadline FROM hr_jobs
				WHERE uid='$uid' AND id IN('$ids')");
			foreach ($list as $value) {
				if ($value['audit']==1 && $value['display']==1 && (($value['deadline'] == 0 || $value['deadline'] > $t) && $value['setmeal_deadline'] > $t)) {
					if ($value['refreshtime'] < $dayNow+60) {
						$id_ok[] = $value['id'];
					} else {
						$id_day[] = $value['id'];
					}
				}
			}
			
			if (!empty($id_ok)) {
				refresh_jobs($id_ok, $uid);
			}
			
			$cnt_total = count($idarr);
			$cnt_ok = count($id_ok);
			$cnt_day = count($id_day);
			$cnt_other = $cnt_total - ($cnt_ok + $cnt_day);
			
			$msg = '成功刷新了【'.$cnt_ok.'】条招聘信息';
			if ($cnt_other || $cnt_day) {
				$cnt_other && $msg .= '，【'.$cnt_other.'】条非发布状态的';
				$cnt_day && ($msg .= $cnt_other ? '及【'.$cnt_day.'】条刷新未满1天的信息' : '，【'.$cnt_day.'】条刷新未满1天的信息');
				$msg .= '不能刷新';
			}
			
			showmsg($msg, 1, array(array('href'=>$refer, 'text'=>'返回')));
		}
		
		//发布/重新发布
		elseif ($opname=='repost') {
			$id_ok = array();
			
			//审核通过
			$list = $db->getall("SELECT id,audit,display,addtime,refreshtime,deadline,setmeal_deadline,effect FROM hr_jobs
				WHERE uid='$uid' AND id IN('$ids')");
			foreach ($list as $value) {
				//if ($value['audit']==1 && $value['display']==1 && (($value['deadline']==0 || $value['deadline'] > $t) && $value['setmeal_deadline'] > $t)) {
				if ($value['audit']==1) {
					$id_ok[] = $value['id'];
				}
			}
			
			if (!empty($id_ok)) {
				$ids_ok = implode("','", $id_ok);
				switch ($value['effect']) {
					case 0:	$deadline = $value['deadline'];break;
					case 1:	$deadline = $t+1*30*24*60*60;break;
					case 2:	$deadline = $t+3*30*24*60*60;break;
					case 3:	$deadline = $t+6*30*24*60*60;break;
					case 4:	$deadline = $t+12*30*24*60*60;break;
				}
				$db->query("UPDATE hr_jobs SET display=1,addtime='$t',refreshtime='$t',deadline='$deadline' WHERE uid='$uid' AND id IN('$ids_ok')");
			}
			
			$cnt_total = count($idarr);
			$cnt_ok = count($id_ok);
			$cnt_other = $cnt_total - $cnt_ok;
			
			$msg = '发布成功【'.$cnt_ok.'】条';
			if ($cnt_other) {
				$msg .= '，其中【'.$cnt_other.'】发布失败';
				//$msg .= '，其中【'.$cnt_other.'】条职位审核未通过，请先修改职位描述';
			}
			
			showmsg($msg, 1, array(array('href'=>$refer, 'text'=>'返回')));
		}
		
		//启用
		elseif ($opname=='start') {
			$id_ok = array();
			
			//已停止,过期
			$list = $db->getall("SELECT id,audit,display,addtime,refreshtime,deadline,setmeal_deadline,effect FROM hr_jobs
				WHERE uid='$uid' AND id IN('$ids')");
			foreach ($list as $value) {
				if ($value['audit']==1 && $value['display']==2 && (($value['deadline']==0 || $value['deadline'] > $t) && $value['setmeal_deadline'] > $t)) {
					$id_ok[] = $value['id'];
				}
			}
			
			if (!empty($id_ok)) {
				activate_jobs($id_ok, 1, $uid);
			}
			
			showmsg('已启用', 1, array(array('href'=>$refer, 'text'=>'返回')));
		}
		
		//停止
		elseif ($opname=='stop') {
			$id_ok = array();
			
			//已停止,过期
			$list = $db->getall("SELECT id,audit,display,addtime,refreshtime,deadline,setmeal_deadline,effect FROM hr_jobs
				WHERE uid='$uid' AND id IN('$ids')");
			foreach ($list as $value) {
				if ($value['audit']==1 && $value['display']==1 && (($value['deadline']==0 || $value['deadline'] > $t) && $value['setmeal_deadline'] > $t)) {
					$id_ok[] = $value['id'];
				}
			}
			
			if (!empty($id_ok)) {
				activate_jobs($id_ok, 2, $uid);
			}
			
			showmsg('已停止', 1, array(array('href'=>$refer, 'text'=>'返回')));
		}
		
		//删除
		elseif ($opname=='del') {
			del_jobs($idarr, $uid);
			showmsg('已删除', 1, array(array('href'=>$refer, 'text'=>'返回')));
		}
	} else {
		showmsg('请选择职位', 1, array(array('href'=>$refer, 'text'=>'返回')));
	}
}

//增加职位
elseif ($act=='addjobs') {
	$uid = $_SESSION['uid'];
	
	//权限判断
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = 'company_index.php';
		showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0,$link);
	}
	
	$setmeal = get_user_setmeal($_SESSION['uid']);
	//已发布职位总数
	$count_jobs = count_jobs($_SESSION['uid']);
	//剩余职位数
	$count_jobs_leave = max(0, $setmeal['jobs_ordinary'] - $count_jobs);
	if ($count_jobs_leave <= 0) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = 'company_index.php';
		showmsg("您的发布职位数不足，请与我们的客服联系！",1,$link);
	}
	
	//行业分类
	$tradeCate = get_category_zt('QS_trade');
	$eduCate = get_category_zt('QS_education');
	$jobNatureCate = get_category_zt('QS_jobs_nature');
	$experienceCate = get_category_zt('QS_experience');
	$jobCate = get_parent_cagegory('category_jobs');#1级职位分类
	$specialityCate = get_parent_cagegory('category_speciality');#1级专业分类
	
	$jobId = isset($_GET['jobid']) ? intval($_GET['jobid']) : 0;
	$jobInfo = array();
	$inport = false;
	if ($jobId) {
		$jobInfo=get_jobs_one($jobId,$uid);
		if (!empty($jobInfo)) {
			$inport = true;
			
			$jobInfo['nature'] = z_queue_resolve($jobInfo['nature'], 'show');
			
			////				
			//行业处理
			$tradeCate_Left = $tradeCate_Right = array();
			foreach ($tradeCate as $key=>$value) {
				$t = trim($jobInfo['trade'], ',');
				$t = explode(",", $t);
				if (in_array($value['c_id'], $t)) {
					$tradeCate_Right[] = $value;
				} else {
					$tradeCate_Left[] = $value;
				}
				
			}
			$smarty->assign('tradeCate_Left', $tradeCate_Left);
			$smarty->assign('tradeCate_Right', $tradeCate_Right);
			
			//处理岗位
			$category_Right = array();
			$category = trim($jobInfo['category'], ',');
			$category_cn = trim($jobInfo['category_cn'], ',');
			$t1 = explode(',', $category);
			$t2 = explode(',', $category_cn);
			foreach ($t1 as $key=>$value) {
				if ($value) {
					$category_Right[] = array('id'=>$t1[$key], 'categoryname'=>$t2[$key]);
				}
			}
			$smarty->assign('category_Right', $category_Right);
			
			//专业要求
			$speciality_Right = array();
			$speciality = trim($jobInfo['speciality'], ',');
			$request = trim($jobInfo['request']);
			$t1 = explode(',', $speciality);
			$t2 = explode(',', $request);
			foreach ($t1 as $key=>$value) {
				if ($value) {
					$speciality_Right[] = array('id'=>$t1[$key], 'categoryname'=>$t2[$key]);
				}
			}
			$smarty->assign('speciality_Right', $speciality_Right);
			
			//户籍处理
				//地区省
				$select_dis = array();
				$res_dis = get_parent_cagegory('category_district');
				foreach ($res_dis as $v){
					$select_dis[$v['id']] = $v['categoryname'];
				}
				$smarty->assign('select_district_val',$jobInfo['cencus_province']);
				$smarty->assign('select_district',$select_dis);
				
				if ($jobInfo['cencus_province'] != ''){
					//地区市
					$select_sdis = array();
					$res_sdis = get_subcategory('category_district',$jobInfo['cencus_province']);
					foreach ($res_sdis as $v){
						$select_sdis[$v['id']] = $v['categoryname'];
					}
					
					$smarty->assign('select_sdistrict_val',$jobInfo['cencus_city']);
					$smarty->assign('select_sdistrict',$select_sdis);
				}
			//END				
		} else {
			//户籍处理
				//地区省
				$select_dis = array();
				$res_dis = get_parent_cagegory('category_district');
				foreach ($res_dis as $v){
					$select_dis[$v['id']] = $v['categoryname'];
				}
				$smarty->assign('select_district_val',$res['cencus_province']);
				$smarty->assign('select_district',$select_dis);
				
				if ($res['cencus_province'] != ''){
					//地区市
					$select_sdis = array();
					$res_sdis = get_subcategory('category_district',$res['cencus_province']);
					foreach ($res_sdis as $v){
						$select_sdis[$v['id']] = $v['categoryname'];
					}
					$smarty->assign('select_sdistrict_val',$res['cencus_city']);
					$smarty->assign('select_sdistrict',$select_sdis);
				}
			//END	
		}
	} else {
			//户籍处理
				//地区省
				$select_dis = array();
				$res_dis = get_parent_cagegory('category_district');
				foreach ($res_dis as $v){
					$select_dis[$v['id']] = $v['categoryname'];
				}
				$smarty->assign('select_district_val',$res['cencus_province']);
				$smarty->assign('select_district',$select_dis);
				
				if ($res['cencus_province'] != ''){
					//地区市
					$select_sdis = array();
					$res_sdis = get_subcategory('category_district',$res['cencus_province']);
					foreach ($res_sdis as $v){
						$select_sdis[$v['id']] = $v['categoryname'];
					}
					$smarty->assign('select_sdistrict_val',$res['cencus_city']);
					$smarty->assign('select_sdistrict',$select_sdis);
				}
			//END			
	}
	
	
	$company_profile=get_company($_SESSION['uid']);
	//资料是否完善
	if (empty($company_profile['contents']) && empty($company_profile['contents_tmp'])) {
		showmsg('提示先进行完善公司基本资料', 1, array(array('href'=>'company_info.php?act=company_intro_edit', 'text'=>'修改公司简介')));
	} elseif (empty($company_profile['nature']) 
		|| empty($company_profile['trade']) 
		|| (empty($company_profile['contact']) && empty($company_profile['contact_tmp'])) 
		|| (empty($company_profile['telephone']) && empty($company_profile['telephone_tmp']))
		|| empty($company_profile['email']) 
		|| (empty($company_profile['address']) && empty($company_profile['address_tmp'])) 
		|| (empty($company_profile['website']) && empty($company_profile['website_tmp']))
	) {
		showmsg('提示先进行完善公司基本资料', 1, array(array('href'=>'company_info.php?act=company_profile_edit', 'text'=>'修改公司基本资料')));
	}
	
	
	//导入招聘职位
	$jobsList = $db->getall("SELECT id,jobs_name FROM hr_jobs
				WHERE uid='$uid'");
	$smarty->assign('jobsList',$jobsList);
	
	$smarty->assign('tradeCate',$tradeCate);
	$smarty->assign('eduCate',$eduCate);
	$smarty->assign('jobNatureCate',$jobNatureCate);
	$smarty->assign('experienceCate',$experienceCate);
	$smarty->assign('jobCate',$jobCate);
	$smarty->assign('specialityCate',$specialityCate);
	$smarty->assign('user',$user);
	$smarty->assign('inport',$inport);
	$smarty->assign('jobInfo',$jobInfo);
	
	
	if ($company_profile['companyname']) {
		$smarty->assign('title','发布职位 - 企业会员中心 - '.$_CFG['site_name']);
		$smarty->assign('company_profile',$company_profile);
		if ($_CFG['operation_mode']=="2"){
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if (($setmeal['endtime']>time() || $setmeal['endtime']=="0") &&  $setmeal['jobs_ordinary']>0)
			{
			$smarty->assign('setmeal',$setmeal);
			$smarty->assign('add_mode',2);
			}
			elseif($_CFG['setmeal_to_points']=="1")
			{
			$smarty->assign('points_total',get_user_points($_SESSION['uid']));
			$smarty->assign('points',get_cache('points_rule'));
			$smarty->assign('add_mode',1);
			}
			else
			{
			$smarty->assign('setmeal',$setmeal);
			$smarty->assign('add_mode',2);
			}
			
		}
		elseif ($_CFG['operation_mode']=="1"){
			$smarty->assign('points_total',get_user_points($_SESSION['uid']));
			$smarty->assign('points',get_cache('points_rule'));
			$smarty->assign('add_mode',1);
		}
		$captcha=get_cache('captcha');
		$smarty->assign('verify_addjob',$captcha['verify_addjob']);
		$smarty->display('member_company/company_addjobs.htm');
	} else {
		$link[0]['text'] = "完善企业资料";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("为了达到更好的招聘效果，请先完善您的企业资料！",1,$link);
	}
}

//发布职位
elseif ($act=='addjobs_save'){
	$company_profile=get_company($_SESSION['uid']);
	$setmeal = get_user_setmeal($_SESSION['uid']);
	
	$tradeCate = get_category_zt('QS_trade');
	$jobCate = z_get_category_jobs();
	$specialityCate = z_get_category_speciality();
	$experienceCate = get_category_zt('QS_experience');
	$eduCate = get_category_zt('QS_education');
	
	//已发布职位总数
	$count_jobs = count_jobs($_SESSION['uid']);
	//剩余职位数
	$count_jobs_leave = max(0, $setmeal['jobs_ordinary'] - $count_jobs);
	if ($count_jobs_leave <= 0) {
		showmsg("发布失败，您的发布职位数不足，请与我们的客服联系！");
	}

	
	//写数据
	/////////////////////////表jobs//////////////////////
	#职位名称
	$setsqlarr['jobs_name'] = !empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('您没有填写职位名称！',1);
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
		showmsg('您没有选择工作岗位！',1);
	}
	#专业要求
	if (!empty($_POST['value_speciality'])) {
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
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('您没有填写职位描述！',1);
	//z_check_banword($_POST['contents'])?showmsg('职位描述存在敏感词，请修改过再提交',0):'';
	//其他
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['companyname']=$company_profile['companyname'];
	$setsqlarr['company_id']=$company_profile['id'];
	$setsqlarr['company_addtime']=$company_profile['addtime'];
	$setsqlarr['company_audit']=$company_profile['audit'];	
	$setsqlarr['scale']=$company_profile['scale'];
	$setsqlarr['scale_cn']=$company_profile['scale_cn'];
	$setsqlarr['street']=$company_profile['street'];
	$setsqlarr['street_cn']=$company_profile['street_cn'];
	$setsqlarr['officebuilding']=$company_profile['officebuilding'];
	$setsqlarr['officebuilding_cn']=$company_profile['officebuilding_cn'];
	$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
	$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
	$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['edittime']=$timestamp;
	switch ($setsqlarr['effect']) {
		case 0:$setsqlarr['deadline'] = 0;break;#长期有效
		case 1:$setsqlarr['deadline'] = $timestamp+1*30*24*60*60;break;#1个月
		case 2:$setsqlarr['deadline'] = $timestamp+3*30*24*60*60;break;#3个月
		case 3:$setsqlarr['deadline'] = $timestamp+6*30*24*60*60;break;#6个月
		case 4:$setsqlarr['deadline'] = $timestamp+12*30*24*60*60;break;#1年
	}	
	$setsqlarr['refreshtime']=$timestamp;	
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['subsite_id']=intval($_CFG['subsite_id']);
	$setsqlarr['tpl']=$company_profile['tpl'];
	$setsqlarr['map_x']=$company_profile['map_x'];
	$setsqlarr['map_y']=$company_profile['map_y'];
	$setsqlarr['audit']=2;
	$setsqlarr['display']=$_POST['optype']=='save_draft'?4:1;//1发布中，4草稿
	
	
	/////////////////////////表jobs_contact//////////////////////
	#联系人	
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('您没有填写联系人！',1);
	#联系电话
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('您没有填写联系电话！',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	#邮箱地址
	$setsqlarr_contact['email']=trim($_POST['email']);
	//其他
	$setsqlarr_contact['address']='';
	$setsqlarr_contact['notify']=0;
	
	
	////////////////////////////////////////////////////////////
	//添加职位信息
	$pid=inserttable(table('jobs'),$setsqlarr,true);
	empty($pid)?showmsg("添加失败！",0):'';
	//添加联系方式
	$setsqlarr_contact['pid']=$pid;
	!inserttable(table('jobs_contact'),$setsqlarr_contact)?showmsg("添加失败！",0):'';

	//发布职位
	write_memberslog($_SESSION['uid'],1,2001,$_SESSION['username'],"发布了职位：{$setsqlarr['jobs_name']}");
	$link[0]['text'] = "已发布职位";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "继续发布职位";
	$link[1]['href'] = '?act=addjobs';
	$link[3]['text'] = "会员中心首页";
	$link[3]['href'] = "company_index.php";
	$msg = $_POST['optype']=='save_draft' ? '保存成功' : '发布成功！';
	showmsg($msg,2,$link);
}

//编辑职位
elseif ($act=='editjobs')
{
	$uid = $_SESSION['uid'];
	
	//权限判断
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = 'company_index.php';
		showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0,$link);
	}
	
	$jobs=get_jobs_one(intval($_GET['id']),$_SESSION['uid']);
	$company_profile=get_company($_SESSION['uid']);
	if (empty($jobs)) showmsg("参数错误！",1);
	
	$tradeCate = get_category_zt('QS_trade');
	$eduCate = get_category_zt('QS_education');
	$jobNatureCate = get_category_zt('QS_jobs_nature');
	$experienceCate = get_category_zt('QS_experience');
	$jobCate = get_parent_cagegory('category_jobs');#1级职位分类
	$specialityCate = get_parent_cagegory('category_speciality');#1级专业分类
	
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
	
	//处理专业
	$speciality_Right = array();
	$speciality = trim($jobs['speciality'], ',');
	$request = trim($jobs['request']);
	$t1 = explode(',', $speciality);
	$t2 = explode(',', $request);
	foreach ($t1 as $key=>$value) {
		if ($value) {
			$speciality_Right[] = array('id'=>$t1[$key], 'categoryname'=>$t2[$key]);
		}
	}
	
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
	$smarty->assign('specialityCate',$specialityCate);
	$smarty->assign('speciality_Right',$speciality_Right);
	$smarty->assign('company_profile',$company_profile);
	
	$smarty->assign('user',$user);
	$smarty->assign('title','修改职位 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('points_total',get_user_points($_SESSION['uid']));
	$smarty->assign('jobs',$jobs);
	$smarty->display('member_company/company_editjobs.htm');
}

//保存编辑
elseif ($act=='editjobs_save') {
	$id=intval($_POST['id']);
	
	$jobs=get_jobs_one($id,$_SESSION['uid']);
	
	$tradeCate = get_category_zt('QS_trade');
	$jobCate = z_get_category_jobs();
	$specialityCate = z_get_category_speciality();
	$experienceCate = get_category_zt('QS_experience');
	$eduCate = get_category_zt('QS_education');
	
	
	//写数据
	/////////////////////////表jobs//////////////////////
	#职位名称
	$setsqlarr['jobs_name'] = !empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('您没有填写职位名称！',1);
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
		showmsg('您没有选择工作岗位！',1);
	}
	#专业
	if (!empty($_POST['value_speciality'])) {
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
	
	//其他
	switch ($setsqlarr['effect']) {
		case 0:$setsqlarr['deadline'] = 0;break;#长期有效
		case 1:$setsqlarr['deadline'] = $timestamp+1*30*24*60*60;break;#1个月
		case 2:$setsqlarr['deadline'] = $timestamp+3*30*24*60*60;break;#3个月
		case 3:$setsqlarr['deadline'] = $timestamp+6*30*24*60*60;break;#6个月
		case 4:$setsqlarr['deadline'] = $timestamp+12*30*24*60*60;break;#1年
	}
	
	#职位描述/要求
	//z_check_banword($_POST['contents'])?showmsg('职位描述存在敏感词，请修改过再提交',0):'';
	$content = !empty($_POST['contents'])?trim($_POST['contents']):showmsg('您没有填写职位描述！',1);	
	if ( ($jobs['audit'] == 1 && trim($jobs['contents']) != $content) || $jobs['audit'] == 4) {		
		$setsqlarr['audit']=4;
		$setsqlarr['contents_tmp'] = $content;
	} elseif ($jobs['audit'] == 3) {
		$setsqlarr['audit']=2;
		$setsqlarr['contents'] = $content;
		$setsqlarr['contents_tmp']='';
	} else {
		$setsqlarr['contents'] = $content;
		$setsqlarr['contents_tmp']='';
	}
	
	$setsqlarr['display']=1;//1发布中，4草稿
	$setsqlarr['edittime'] = time();
	
	
	/////////////////////////表jobs_contact//////////////////////
	#联系人	
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('您没有填写联系人！',1);
	#联系电话
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('您没有填写联系电话！',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	#邮箱地址
	$setsqlarr_contact['email']=trim($_POST['email']);
	
	////////////////////////////////////////////////////////////
	if (!updatetable(table('jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("保存失败！",0);
	if (!updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")) showmsg("保存失败！",0);
		
	
	//保存职位
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],2002,$_SESSION['username'],"修改了职位：{$setsqlarr['jobs_name']}，职位ID：{$id}");
	$link[0]['text'] = "职位列表";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "查看修改结果";
	$link[1]['href'] = "?act=editjobs&id={$id}";
	$link[2]['text'] = "会员中心首页";
	$link[2]['href'] = "company_index.php";
	showmsg("修改成功！",2,$link);	
}
unset($smarty);
?>