<?php
/* 个人简历处理脚本
 * ZT 2012年10月10日 17:03:47
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');

$smarty->assign('timestamp',$timestamp);

/*基本信息*/
if ($act == 'index' || $act == 'step1'){	
	//修改
	if(!empty($_POST)){
		//简历
		$resume = z_resume($user['uid']);
		
		//行业
		$select_industry = array();	
		$res_industry = get_category_zt('QS_trade');
		foreach ($res_industry as $v){
			$select_industry[$v['c_id']] = $v['c_name'];
		}
		
		//学历
		$select_edu = array();
		$res_edu = get_category_zt('QS_education');
		foreach ($res_edu as $v){
			$select_edu[$v['c_id']] = $v['c_name'];
		}
		
		$post = array();
		$post['fullname'] = $_POST['name'];#姓名
		$post['sex'] = $_POST['sex'];#性别
		$post['sex_cn'] = ($_POST['sex']==1)?'男':'女';
		$post['nation'] = $_POST['nation'];#民族
		$post['height'] = $_POST['height'];#身高
		$post['birthdate'] = strtotime($_POST['birthdayYear'].'-'.$_POST['birthdayMonth'].'-'.$_POST['birthdayDay']);#出生日期
		$post['marriage'] = $_POST['marry'];#婚否
		$post['address'] = $_POST['address'];#通信地址
		$post['workyear'] = $_POST['workyear'];#工作年限			
		//根据工作年限, 判断所属经验
		if ($post['workyear'] == 0){
			$post['experience'] = 1;$post['experience_cn'] = '无经验';
		}
		elseif ($post['workyear'] > 0 && $post['workyear'] < 1){
			$post['experience'] = 2;$post['experience_cn'] = '1年以下';
		}
		elseif ($post['workyear'] >=1 && $post['workyear'] < 3){
			$post['experience'] = 3;$post['experience_cn'] = '1-3年';
		}
		elseif ($post['workyear'] >=3 && $post['workyear'] < 5){
			$post['experience'] = 4;$post['experience_cn'] = '3-5年';
		}
		elseif ($post['workyear'] >=5 && $post['workyear'] < 10){
			$post['experience'] = 5;$post['experience_cn'] = '5-10年';
		}
		else {
			$post['experience'] = 6;$post['experience_cn'] = '10年以上';
		}
		$post['cencus_province'] = $_POST['cencus_province'];#户籍
		$post['cencus_city'] = $_POST['cencus_city'];
		$post['home_province'] = $_POST['home_province'];#现居地
		$post['home_city'] = $_POST['home_city'];
		$post['trade'] = $_POST['work_industry'];#目前行业
		$post['trade_cn'] = $select_industry[$post['trade']];
		$post['recentjobs'] = $_POST['current_jobs'];#目前工作岗位
		$post['education'] = $_POST['highest_degree'];#最高学历
		$post['education_cn'] = $select_edu[$post['education']];		
		//专业
		$specialityCate = z_get_category_speciality();
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
			$post['speciality'] = ",".implode(",", $speciality_id_arr).",";
			$post['speciality_parent'] = z_speciality_category_parentid($speciality_id_arr);
			$post['speciality_cn'] = implode(",", $speciality_name_arr);
		}
		$post['english_ability'] = $_POST['english_ability'];#英语能力
		$post['english_degree'] = $_POST['english_degree'];
		$post['otherlang'] = $_POST['otherlang'].':'.$_POST['otherlang_degree'];#其它外语
		$post['computer_degree'] = $_POST['computer_degree'];#计算机能力
		$post['telephone'] = $_POST['telephone'];#手机号码
		$post['email'] = $_POST['mail'];#邮箱地址		
		$post['photo_img'] = empty($_FILES['photo']['name']) ? $_POST['oldphoto'] : file_upload('photo', $user['uid'], 'gif/jpg/bmp/jpeg',1);#照片
		$post['photo'] = $post['photo_img'] != '' ? 1 : 0;
		$post['edittime'] = time();#修改时间			
		if ($resume['audit']==3) {#状态
			$post['audit'] = 2;
		}
		
		//更新数据
		updatetable('hr_resume', $post, "uid=".$user['uid']);
		
		$returnlink[] = array('text'=>'返回','href'=>$_CFG['site_dir'].'user/personal/personal_resume.php?act=step1');
		showmsg('修改成功', 2,$returnlink);
	} else {
		//简历
		$resume = z_resume($user['uid']);
		
		//简历部分数据处理
		if ($resume['otherlang']){
			list($resume['otherlang'],$resume['otherlang_degree']) = explode(':', $resume['otherlang']);
		}
		$resume['birthdate'] = date('Y-m-d',$resume['birthdate']);
		$smarty->assign('user_info', $resume);
		
		//行业
		$select_industry = array();	
		$res_industry = get_category_zt('QS_trade');
		foreach ($res_industry as $v){
			$select_industry[$v['c_id']] = $v['c_name'];
		}
		$smarty->assign('select_industry_val',$resume['trade']);
		$smarty->assign('select_industry',$select_industry);
		
		//学历
		$select_edu = array();
		$res_edu = get_category_zt('QS_education');
		foreach ($res_edu as $v){
			$select_edu[$v['c_id']] = $v['c_name'];
		}
		$smarty->assign('select_edu_val',$resume['education']);
		$smarty->assign('select_edu',$select_edu);
		
		//专业
		$specialityCate = get_parent_cagegory('category_speciality');#1级专业分类
		$speciality_Right = array();
		$speciality = trim($resume['speciality'], ',');
		$speciality_cn = trim($resume['speciality_cn']);
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
		$select_dis = array();
		$res_dis = get_parent_cagegory('category_district');
		foreach ($res_dis as $v){
			$select_dis[$v['id']] = $v['categoryname'];
		}
		$smarty->assign('select_district_val',$resume['cencus_province']);
		$smarty->assign('select_district',$select_dis);	
		if ($resume['cencus_province'] != ''){#市
			$select_sdis = array();
			$res_sdis = get_subcategory('category_district',$resume['cencus_province']);
			foreach ($res_sdis as $v){
				$select_sdis[$v['id']] = $v['categoryname'];
			}
			$smarty->assign('select_sdistrict_val',$resume['cencus_city']);
			$smarty->assign('select_sdistrict',$select_sdis);
		}
	
		//现居地处理
		$select_dis2 = array();
		$res_dis2 = get_parent_cagegory('category_district');
		foreach ($res_dis2 as $v){
			$select_dis2[$v['id']] = $v['categoryname'];
		}
		$smarty->assign('select_district_val2',$resume['home_province']);
		$smarty->assign('select_district2',$select_dis2);	
		if ($resume['home_province'] != ''){#市
			$select_sdis2 = array();
			$res_sdis2 = get_subcategory('category_district',$resume['home_province']);
			foreach ($res_sdis2 as $v){
				$select_sdis2[$v['id']] = $v['categoryname'];
			}
			$smarty->assign('select_sdistrict_val2',$resume['home_city']);
			$smarty->assign('select_sdistrict2',$select_sdis2);
		}
	}
}


/*教育工作背景*/
elseif ($act == 'step2'){
	
	/*教育经历*/
	$edu = $db->getone("SELECT * FROM hr_resume_education WHERE uid=".$user['uid']." AND status=0");
	if (empty($edu)){	
		$edu = $db->getone("SELECT * FROM hr_resume_education WHERE uid=".$user['uid']." AND status=1");
	}
	//部分数据处理
	if (!empty($edu)){
		preg_match('/(\d+).*?(\d+).*/', $edu['start'],$m);
		$edu['sdate1'] = $m[1];
		$edu['sdate2'] = $m[2];
		preg_match('/(\d+).*?(\d+).*/', $edu['endtime'],$m);
		$edu['sdate3'] = $m[1];
		$edu['sdate4'] = $m[2];
	}	
	$edu['edustart'] = strftime($edu['sdate1'].'-'.$edu['sdate2']);
	$edu['eduend'] = strftime($edu['sdate3'].'-'.$edu['sdate4']);
	$edu_content = $edu['content'];
	
	/*工作经历*/
	$work = $db->getall("SELECT * FROM hr_resume_work WHERE uid=".$user['uid']." AND status=0");
	if (empty($work)){
		$work = $db->getall("SELECT * FROM hr_resume_work WHERE uid=".$user['uid']." AND status=1");
	}
	//部分数据处理
	if (!empty($work)){
		$multia = $multib = array();
		foreach ($work as $k=>$v){
			preg_match('/(\d+)年(\d+)月/', $work[$k]['start'],$m);
			$work[$k]['edate1'] = $m[1];
			$work[$k]['edate2'] = $m[2];
			$multia[$k] = $m[1];
			$multib[$k] = $m[2];
			$multic[$k] = $work[$k]['work_status'];
			preg_match('/(\d+)年(\d+)月/', $work[$k]['endtime'],$m);
			$work[$k]['edate3'] = $m[1];
			$work[$k]['edate4'] = $m[2];
			$work[$k]['work_start'] = strftime($work[$k]['edate1'].'-'.$work[$k]['edate2']);
			$work[$k]['work_end'] = strftime($work[$k]['edate3'].'-'.$work[$k]['edate4']);
			$work[$k]['all_extra'] = $work[$k]['work_status']?'disabled="true"':'';
			$work_content[$k] = $work[$k]['achievements'];
		}
		array_multisort($multic, SORT_NUMERIC,SORT_DESC,$multia, SORT_NUMERIC, SORT_DESC,$multib, SORT_NUMERIC, SORT_ASC,$work);
		$smarty->assign('user_work',$work);
	}
	
	//学历
	$select_edu = array();
	$res_edu = get_category_zt('QS_education');
	foreach ($res_edu as $v){
		$select_edu[$v['c_id']] = $v['c_name'];
	}
	$smarty->assign('select_edu_val',$edu['education']);
	$smarty->assign('select_edu',$select_edu);
	$smarty->assign('user_edu', $edu);

	
	//修改数据
	if (!empty($_POST)){
		
		/*教育背景*/
		$post = array();		
		$eduid = $post['id'] = $_POST['edu_id'];
		$post['uid'] = $user['uid'];
		$post['school'] = $_POST['graduated_school'];
		$post['speciality'] = $_POST['study_course'];
		$post['education'] = $_POST['degree'];
		$post['education_cn'] = $select_edu[$_POST['degree']];
		$post['start'] = $_POST['edustart_Year'].'年'.$_POST['edustart_Month'].'月';
		$post['endtime'] = $_POST['eduend_Year'].'年'.$_POST['eduend_Month'].'月';
		$post['content'] = $_POST['edu_content'];
		
		if ($post['content'] == $edu_content){//没做修改
			unset($post['content'], $post['id']);
			updatetable(table('resume_education'), $post, "uid=".$user['uid']." AND id=$eduid");
		} else {//修改
			unset($post['id']);
			$post['status'] = 0;
			
			$res = $db->getone("SELECT id FROM hr_resume_education WHERE uid=".$user['uid']." AND status=0");
			if (!empty($res)){
				$post['id'] = $res['id'];
				inserttable(table('resume_education'), $post, 0, 1);
			} else {
				inserttable(table('resume_education'), $post);
			}	
			
			updatetable(table('resume'), array('audit'=>'4'), " uid=".$user['uid']);//简历状态为修改待审
		}
				
		/*工作经历*/
		$post = array();
		foreach ($_POST['work_id'] as $k=>$v){
			$post['id'][$k] = $v;
			$post['uid'][$k] = $user['uid'];
		}		
		foreach ($_POST['workstart_Year'] as $k=>$v){
			$post['start'][$k] = $v.'年';
		}
		foreach ($_POST['workstart_Month'] as $k=>$v){
			$post['start'][$k] .= $v.'月';
		}
		foreach ($_POST['workend_Year'] as $k=>$v){
			$post['endtime'][$k] = $v.'年';
		}
		foreach ($_POST['workend_Month'] as $k=>$v){
			$post['endtime'][$k] .= $v.'月';
		}
		foreach ($_POST['work_status'] as $k=>$v){#工作状态
			$post['work_status'][$k] = $v;
		}
		foreach ($_POST['salary'] as $k=>$v){#月薪
			$post['salary'][$k] = $v;
		}
		foreach ($_POST['salary_private'] as $k=>$v){#是否保密月薪
			$post['salary_private'][$k] = $v;
		}
		foreach ($_POST['job_title'] as $k=>$v) {#职位 名称
			$post['jobs'][$k] = $v;
			$post['pid'][$k] = $resume_id;
			$post['uid'][$k] = $user['uid'];
			if ($_POST['work_id'][$k]){
				$post['id'][$k] = $_POST['work_id'][$k];
			} else {
				$post['id'][$k] = '';
			}
		}
		foreach ($_POST['company_name'] as $k=>$v) {#公司名称
			$post['companyname'][$k] = $v;
		}
		foreach ($_POST['work_content'] as $k=>$v){#工作经历
			$post['achievements'][$k] = $v;
		}
	
		$t = array();
		foreach ($post as $k=>$v){
			foreach ($v as $key=>$val){
				$t[$key][$k] = $val;
			}
		}
	
		function compare_array($a, $b){//a为原始数组,b为修改数组
			for ($i = 0; $i < count($a); $i++){
				$cur_a = $a[$i];
				foreach ($b as $k=>$v){
					$cur_b = $v;
					if ($cur_a == $cur_b){//值相等
						unset($b[$k]);
					}
				}
			}
			if (empty($b)){
				return true;
			}
			return false;
		}
		
		
		if (!empty($t)){//有传入工作经历,则比较旧值与新值的差异
			if (compare_array($work_content,$post['achievements'])){//相同
				//有备份,更新备份记录
				$res = $db->getall("SELECT id FROM hr_resume_work WHERE uid=".$user['uid']." AND status=0");
				if (!empty($res)){
					$db->query("DELETE FROM hr_resume_work WHERE uid=".$user['uid']." AND status=0");
				}
				foreach ($t as $insertsqlarr) {
					inserttable('hr_resume_work', $insertsqlarr, 0, 1);
				}
			} else {//不同,备份,改状态	
				$res = $db->getone("SELECT id FROM hr_resume_work WHERE uid=".$user['uid']." AND status=0");
				if (!empty($res)){
					$db->query("DELETE FROM hr_resume_work WHERE uid=".$user['uid']." AND status=0");
				}
				foreach ($t as $insertsqlarr) {
					unset($insertsqlarr['id']);
					$insertsqlarr['status'] = 0;
					inserttable('hr_resume_work', $insertsqlarr, 0);
				}
						
				updatetable(table('resume'), array('audit'=>'4'), " uid=".$user['uid']);
			}
		}	

		//如果简历未通过，这改成待审状态
		$resume = z_resume($user['uid']);
		if ($resume['audit']==3){
			updatetable(table('resume'), array('audit'=>2), " uid=".$user['uid']);
		}
		//更新时间
		updatetable('hr_resume', array('edittime'=>time()), "uid=".$user['uid']);
		
		$returnlink[] = array('text'=>'返回','href'=>$_CFG['site_dir'].'user/personal/personal_resume.php?act=step2');
		showmsg('修改成功', 2,$returnlink);
	}	
}

/*求职意向特长*/
elseif ($act == 'step3'){
	if (!empty($_POST)){
		//简历
		$resume = z_resume($user['uid']);
		
		//特长
		$res = $db->getone("SELECT * FROM ".table('resume_intention')." WHERE uid=".$user['uid']);
		
		/*提交数据*/
		$post = array();
		$post['id'] = $_POST['intention_id'];
		$post['pid'] = $resume_id;
		$post['uid'] = $user['uid'];
		$post['industry'] = implode(',', $_POST['value_industry']);
		$post['category'] = implode(',', $_POST['value_category']);
		$post['district'] = implode(',', $_POST['value_district']);
		$post['work_status'] = $_POST['work_status'];
		$post['time_2work'] = $_POST['time_2work'];
		$post['expected_salary'] = $_POST['expected_salary'];		
		if ($_POST['self_evaluation'] != $res['self_evaluation'] && ($resume['audit'] == 1 || $resume['audit'] == 4)){#自我介绍
			$post['self_evaluation'] = $res['self_evaluation'];
			$post['self_evaluation_tmp'] = $_POST['self_evaluation'];
			updatetable(table('resume'), array('audit'=>'4'), " uid=".$user['uid']);
		} else {
			$post['self_evaluation'] = $_POST['self_evaluation'];
			$post['self_evaluation_tmp'] = $_POST['self_evaluation'];
		}
		if ($_POST['specialty'] != $res['specialty'] && ($resume['audit'] == 1 || $resume['audit'] == 4)){#优势特长
			$post['specialty'] = $res['specialty'];
			$post['specialty_tmp'] = $_POST['specialty'];
			updatetable(table('resume'), array('audit'=>'4'), " uid=".$user['uid']);
		} else {
			$post['specialty'] = $_POST['specialty'];
			$post['specialty_tmp'] = $_POST['specialty'];
		}
		
		inserttable('hr_resume_intention', $post, 0, 1);
		
		//如果审核未通过则变为待审
		if ($resume['audit']==3){
			updatetable(table('resume'), array('audit'=>2), " uid=".$user['uid']);
		}
		//更新时间
		updatetable('hr_resume', array('edittime'=>time()), "uid=".$user['uid']);
		
		$returnlink[] = array('text'=>'返回','href'=>$_CFG['site_dir'].'user/personal/personal_resume.php?act=step3');
		showmsg('修改成功', 2,$returnlink);	
	} else {	
		//可指定行业数据
		$res_industry = get_category_zt('QS_trade');
		$smarty->assign('category_industry', $res_industry);
		
		//一级职业分类数据
		$parent_category = get_parent_cagegory('category_jobs');
		$smarty->assign('parent_category', $parent_category);
	
		//地区分类数据
		$parent_district = get_parent_cagegory('category_district');
		$smarty->assign('parent_district', $parent_district);
		
		$res = $db->getone("SELECT * FROM ".table('resume_intention')." WHERE uid=".$user['uid']);	
	
		if (!empty($res)){
			$res['industry'] = get_options_html('category', $res['industry']);
			$res['category'] = get_options_html('category_jobs', $res['category']);
			$res['district'] = get_options_html('category_district', $res['district']);
		}
		$smarty->assign('intention_info', $res);
	}
}


elseif ($act == 'resume_show'){
#	echo '简历预览';
#	e($smarty);
	//简历信息
	$s = assing_resume($user['uid'],1);
	if ($s>0){
		$smarty->display('mail_template/outward.htm');
	}
	else{
		echo $s;
		echo '<script>alert("简历资料不完善")</script>';
	}
	exit;
}


$html_body = 'personal_resume';
$smarty->assign('html_body',$html_body);
$smarty->assign('page_title','个人中心-简历修改');
$smarty->display('member_personal/personal_index.htm');
?>