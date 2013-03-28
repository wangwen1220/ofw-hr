<?php
 /*
 * 74cms 职位详细页
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/

define('IN_QISHI', true);
$alias="QS_jobsshow";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
        $smarty->cache =true;
		$smarty->cache_lifetime=$mypage['caching'];
	}else{
		$smarty->cache = false;
	}
$cached_id=$_CFG['subsite_id']."|".$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);


if ($_GET['id'] == ''){
		header("HTTP/1.1 404 Not Found"); 
		$smarty->display("404.htm");
		exit();
}


if ($_GET['type'] == 'com'){
	$companyid = intval($_GET['id']);
}
else{
	$sql = "SELECT uid FROM ".table('jobs')." WHERE id=".intval($_GET['id']);
	$res = $db->getone($sql);
	$companyid = $res['uid'];
}

if ($companyid == ''){
		header("HTTP/1.1 404 Not Found"); 
		$smarty->display("404.htm");
		exit();
}


$option = " WHERE 1 AND (audit=1 OR audit=4)  AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
$option .= "AND uid={$companyid} ";

//验证码random
$smarty->assign('random',time());


//tab1页数据
#e($_SESSION);
//END

//此处是公司的职位列表页
if ($_GET['page']){
	if ($_GET['page'] == 0){
		exit;
	}
	if ($_GET['areaid']>0){
		$option .= " AND (`district`={$_GET['areaid']} OR `sdistrict`={$_GET['areaid']})";
	}
	if ($_GET['eduid']>0){
		$option .= " AND `education`={$_GET['eduid']}";
	}
	
	$pagenum = 10;
	$precount = ($_GET['page']-1)*$pagenum;
	
	$sql = "SELECT id,jobs_name,education_cn,experience_cn,wage_min,wage_max,refreshtime,district_cn FROM ".table('jobs')." $option ORDER by refreshtime DESC,sort ASC,addtime DESC";
	$qry = $db->query($sql);
	$total = $db->num_rows($qry);
	
	$sql .= " LIMIT $precount, $pagenum ";
#	echo $sql;exit;
	$res = $db->getall($sql);
	$tmp = array();
	foreach ($res as $k=>$v){
		foreach ($v as $key=>$val){
/*
			if ($key == 'jobs_name' || $key == 'education_cn' || $key == 'experience_cn' ||
				$key == 'wage_cn' || $key == 'district_cn'){
					$tmp[$k][$key] = mb_convert_encoding($val, "utf-8", "gb2312");
				}
*/				
			if ($key == 'refreshtime'){
				$tmp[$k][$key] = date('Y-m-d',$val);
			}
			else{
				$tmp[$k][$key] = mb_convert_encoding($val, "utf-8", "gb2312");
			}
		}
		
		
		$tmp[$k]['wage_cn'] = mb_convert_encoding(z_wage_cn($v['wage_min'], $v['wage_max']), "utf-8", "gb2312");
	}
	//'pagecount':15,'totalcount':150
	$ret['pagecount'] = ceil($total/$pagenum);
	$ret['totalcount'] = $total;
	$ret['list'] = $tmp;
	echo json_encode($ret);
	exit;
}
//END







$mypage['tpl']=get_tpl("jobs",intval($_GET['id']));
$smarty->display($mypage['tpl'],$cached_id);
$db->close();
unset($smarty);
?>
