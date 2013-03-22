<?php
/* 个人中心首页
 * ZT 2012年10月10日 17:03:36
 */
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');


//AJAX
if ($act == 'firstlg'){
	$db->query("UPDATE ".table('resume')." SET complete=5 WHERE uid=".$user['uid']);
	exit;
}
if ($act == 'refresh'){
	$db->query("UPDATE ".table('resume')." SET refreshtime=$timestamp WHERE uid=".$user['uid']);
	echo date('Y-m-d',$timestamp);
	exit;
}
if ($act == 'recommend' && $_GET['page']){
	//获取系统推荐的职位数据
	$pagenum = 10;
	$precount = ($_GET['page']-1)*$pagenum;

//此处处理限制条件	
	$option = " WHERE 1 AND (audit=1 OR audit=4) AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
	$res = $db->getone("SELECT category,district FROM ".table('resume_intention')." WHERE uid=".$user['uid']);
	if ($res['category']){
		$tmp = '';
		$tmpf = '';
		$categoryarr = explode(',', $res['category']);
		foreach ($categoryarr as $v){
			if (ifparent_category($v)){//一级类
				$tmp .= $tmpf." `subclass` REGEXP ',?$v,?' ";
			}
			else{//二级类
				$tmp .= $tmpf." `category` REGEXP ',?$v,?' ";
			}
			$tmpf = ' OR ';
		}
		
		$option .= " AND ($tmp) ";
	}
	if ($res['district']){
		$option .= " AND (district IN(".$res['district'].") OR  sdistrict IN(".$res['district'].")) ";
	}
//END	
	
	$sql = "SELECT id,jobs_name,companyname,uid,education_cn,experience_cn,wage_cn,refreshtime,district_cn FROM ".table('jobs')." $option";
	$qry = $db->query($sql);
	$total = $db->num_rows($qry);
	
	$sql .= " ORDER BY refreshtime DESC LIMIT $precount, $pagenum  ";
#	echo $sql;exit;
	$res = $db->getall($sql);
	$tmp = array();
	foreach ($res as $k=>$v){
		foreach ($v as $key=>$val){
			if ($key == 'refreshtime'){
				$tmp[$k][$key] = date('Y-m-d',$val);
			}
			else{
				$tmp[$k][$key] = mb_convert_encoding($val, "utf-8", "gb2312");
			}
		}
	}
	//'pagecount':15,'totalcount':150
	$ret['pagecount'] = ceil($total/$pagenum);
	$ret['totalcount'] = $total;
	$ret['list'] = $tmp;
	echo json_encode($ret);
	exit;
	//END
	
}
//END

$resume_info = $db->getone("SELECT complete,refreshtime,hideall,hidesome FROM ".table('resume')." WHERE uid=".$_SESSION['uid']);
$smarty->assign('resume_info',$resume_info);

//个人统计数据
$countnum = array();
$countnum['applyjob_num'] = $user['p_apply_num'];
$countnum['interview_num'] = $user['p_interview_num'];
$countnum['favorite_num'] = count_jobs_library($_SESSION['uid']);
$smarty->assign('countnum',$countnum);

//查询头像数据
$sql = "SELECT * FROM ".table('resume_file')." WHERE type=1 AND uid=".$_SESSION['uid']." ORDER BY upload_time DESC LIMIT 1";
$res = $db->getone($sql);
if (!empty($res)){
	$smarty->assign('personal_img', $res['path']);
}
//END

$login_type = 0;
//首次登录
if ($resume_info['complete'] == 4){
	$login_type ^= 1;
}

//一个月未登录
if (($timestamp-$user['last_login_time'])/(60*60*24) >= 30){
	$login_type ^= 4;
}

$login_type = 0;
$smarty->assign('login_type',$login_type);

//未读消息
$msg_unread_num = z_message_unread_num($_SESSION['uid']);
$smarty->assign('msg_unread_num', $msg_unread_num);
	

$html_body = 'body_index';
$smarty->assign('html_body',$html_body);
$smarty->assign('page_title','个人中心-首页');

$smarty->display('member_personal/personal_index.htm');

unset($smarty);
?>