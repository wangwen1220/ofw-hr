<?php
 /*
 * 74cms 职位首页
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_jobs";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
        $smarty->cache =true;
		$smarty->cache_lifetime=$mypage['caching'];
	}else{
		$smarty->cache = false;
	}
$cached_id=$_CFG['subsite_id']."|".$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
if(!$smarty->is_cached($mypage['tpl'],$cached_id))
{
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

/*搜索条件处理
 * 
 */
//顶部导航通用参数
$keyword = mysql_real_escape_string($_GET['key']);
$keyword = addcslashes($keyword, '%_'); 
//
#echo $category_id;

	
//搜索页参数
$search_keytype = $_GET['keytype'];
$search_trade = $_GET['industry'];//行业array
$search_category = $_GET['category'];//岗位分类array
$search_district = $_GET['district'];//地区array
$navi_cat_str = array();
$navi_dis_str = array();
$navi_cat_arr = array();
$navi_dis_arr = array();
$search_sex = $_GET['sex'];
$search_nature = $_GET['nature'];//招聘全职,兼职
$search_education = $_GET['education'];//教育程度

$search_age1 = $_GET['age1'];//处理年龄范围
$search_age2 = $_GET['age2'];//

$search_experience = $_GET['experience'];//经验
$search_update = $_GET['update'];//最近更新
//END


//列表页参数
	//分类与行业链接参数
$link_category = intval($_GET['jobcategory'])?intval($_GET['jobcategory']):0;
$link_trade = intval($_GET['trade'])?intval($_GET['trade']):0;
	//END
	
	//列表导航
$list_area = intval($_GET['list_areaid'])?intval($_GET['list_areaid']):0;
$list_education = intval($_GET['list_educationid'])?intval($_GET['list_educationid']):0;
$list_sex = intval($_GET['list_sex'])?intval($_GET['list_sex']):0;
#$list_experience = intval($_GET['list_experience'])?intval($_GET['list_experience']):0;
$list_wage = intval($_GET['list_wage'])?intval($_GET['list_wage']):0;
	//END

//END


//分页参数
$page = intval($_GET['page'])? max(1,intval($_GET['page'])):1;

//END



//条件SQL集合
$option = " WHERE 1 AND (audit=1 OR audit=4)  AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";

if ($_GET['school']){
	$option .= ' AND (nature&4 OR nature&8  OR experience=0) ';
}

if ($keyword != ''){
	if ($search_keytype){//指定关键词类型
		if ($search_keytype == 1){//公司名
			$option .= " AND `companyname` LIKE '%$keyword%'";
		}
		else{//职位名
			$option .= " AND `jobs_name` LIKE '%$keyword%'";
		}
	}
	else{
		$option .= " AND (`jobs_name` LIKE '%$keyword%' OR `companyname` LIKE '%$keyword%')";
	}
}


if ($link_category){//指定岗位分类
	if (!ifparent_category($link_category)){//二级类
		$option .= " AND (`category` REGEXP ',$link_category,') ";
	}
	else{//一级类
		$option .= " AND (`subclass` REGEXP ',$link_category,') ";
	}
	
}
elseif (!empty($search_category)){//
	$tmp = '';
	$tmpf = '';
	foreach ($search_category as $k=>$v){
		$navi_cat_arr[$k]['id'] = $v;
		$navi_cat_arr[$k]['name'] = get_categoryname_by_id($v,'jobs');
		$navi_cat_str['origin'] .= $navi_cat_arr[$k]['name'].'-';
		if (ifparent_category($v)){//一级类
			$tmp .= $tmpf." `subclass` REGEXP ',$v,' ";
		}
		else{//二级类
			$tmp .= $tmpf." `category` REGEXP ',$v,' ";
		}
		$tmpf = ' OR ';
	}
	$option .= " AND ($tmp)";
	$navi_cat_str['cut'] = cut_str(rtrim($navi_cat_str['origin'],'-'), 13,0,'...');
	$smarty->assign('navi_cat_str',$navi_cat_str);
	$smarty->assign('navi_cat_arr',$navi_cat_arr);
}

if ($link_trade){//指定行业
	$option .= " AND `trade` REGEXP ',$link_trade,' ";
}
elseif (!empty($search_trade)){
	$tmp = '';
	$tmpf = '';
	foreach ($search_trade as $v){
		$tmp .= $tmpf." `trade` REGEXP ',$v,' ";
		$tmpf = ' OR ';
	}
	$option .= " AND ($tmp)";
}

if ($list_area){//列表页指定地区
	$option .= " AND (`district`=$list_area OR `sdistrict`=$list_area)";
}
elseif (!empty($search_district)){
	$tmp = '';
	$tmpf = '';
	foreach ($search_district as $k=>$v){
		$navi_dis_arr[$k]['id'] = $v;
		$navi_dis_arr[$k]['name'] = get_categoryname_by_id($v,'district');
		$navi_dis_str['origin'] .= $navi_dis_arr[$k]['name'].'-';
		$tmp .= $tmpf." `district`=$v OR `sdistrict`=$v ";
		$tmpf = ' OR ';
	}
	$option .= " AND ($tmp)";
	$navi_dis_str['cut'] = cut_str(rtrim($navi_dis_str['origin'],'-'), 4,0,'...');
	$smarty->assign('navi_dis_str', $navi_dis_str);
	$smarty->assign('navi_dis_arr', $navi_dis_arr);
}


if ($list_sex){
	$option .= " AND `sex`!=".($list_sex==1?2:1);
}
elseif ($search_sex ){//性别
	$option .= " AND `sex`!=".($search_sex==1?2:1);
}

if (!empty($search_nature)){//职业全职类别
	function arrayor($v,$w){
		$v |= $w;
		return $v;
	}
	$option .= " AND `nature`&".array_reduce($search_nature, "arrayor").">0 ";
}

if ($search_education || $list_education){//学历
	$option .= " AND `education`=".($search_education|$list_education);
}

//年龄
if ($search_age1 > $search_age2){
	$temp = $search_age1;
	$search_age1 = $search_age2;
	$search_age2 = $temp;
}
if ($search_age1 && !$search_age2){
	$option .= " AND `age_min`<=$search_age1";
}
elseif ($search_age2 && !$search_age1){
	$option .= " AND `age_max`>=$search_age2";
}
elseif ($search_age1 && $search_age2){
	$option .= " AND ( (`age_min`<=$search_age1 AND `age_max`>=$search_age1) OR (`age_max`>=$search_age2 AND `age_min`<=$search_age2) OR (`age_max`<=$search_age2 AND `age_min`>=$search_age1) ) ";
}
//工作经验
if ($search_experience ){
	if ($search_experience == 2){
		$option .= " AND `experience`<=$search_experience ";
	}
	else{
		$option .= " AND `experience`=$search_experience ";
	}
	
}
//更新日期  
if ($search_update){
	switch ($search_update){
		case 1:{
			$day = 1;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 2:{
			$day = 3;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 3:{
			$day = 7;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 4:{
			$day = 30;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 5:{
			$day = 90;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 6:{
			$day = 180;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 7:{
			$day = 360;
			$option .= " AND `refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}	
		default:
			break;
	}
}


if ($list_wage){
	switch ($list_wage){
		case 1:{//面议
			$option .= " AND (`wage_min`=0 AND `wage_max`=0) ";
			break;
		}
		case 2:{//2000以下
			$option .= " AND  (`wage_min`<2000 OR (`wage_max`<2000 AND `wage_max`!=0) ) ";
			break;
		}
		case 3:{//2000~3000   2-5   1-8  3-4 3-8 1-3
			$option .= " AND ((`wage_min`<=2000 AND `wage_max`>=3000) OR (`wage_min`>=2000 AND `wage_max`!=0 AND `wage_max`<=3000) ";
			$option .= " OR (`wage_min`>=2000 AND `wage_min`<=3000) OR (`wage_max`>=2000 AND `wage_max`<=3000) ) ";
			break;
		}
		case 4:{//3000~5000
			$option .= " AND ((`wage_min`<=3000 AND `wage_max`>=5000) OR (`wage_min`>=3000 AND `wage_max`!=0 AND `wage_max`<=5000) ";
			$option .= " OR (`wage_min`>=3000 AND `wage_min`<=5000) OR (`wage_max`>=3000 AND `wage_max`<=5000) ) ";
			break;
		}
		case 5:{//5000~8000
			$option .= " AND ((`wage_min`<=5000 AND `wage_max`>=8000) OR (`wage_min`>=5000 AND `wage_max`!=0 AND `wage_max`<=8000) ";
			$option .= " OR (`wage_min`>=5000 AND `wage_min`<=8000) OR (`wage_max`>=5000 AND `wage_max`<=8000) ) ";
			break;
		}
		case 6:{//8000~10000
			$option .= " AND ((`wage_min`<=8000 AND `wage_max`>=10000) OR (`wage_min`>=8000 AND `wage_max`!=0 AND `wage_max`<=10000) ";
			$option .= " OR (`wage_min`>=8000 AND `wage_min`<=10000) OR (`wage_max`>=8000 AND `wage_max`<=10000) ) ";
			break;
		}
		case 7:{//10000~15000
			$option .= " AND ((`wage_min`<=10000 AND `wage_max`>=15000) OR (`wage_min`>=10000 AND `wage_max`!=0 AND `wage_max`<=15000) ";
			$option .= " OR (`wage_min`>=10000 AND `wage_min`<=15000) OR (`wage_max`>=10000 AND `wage_max`<=15000) ) ";
			break;
		}	
		case 8:{//15000~30000
			$option .= " AND ((`wage_min`<=15000 AND `wage_max`>=30000) OR (`wage_min`>=15000 AND `wage_max`!=0 AND `wage_max`<=30000) ";
			$option .= " OR (`wage_min`>=15000 AND `wage_min`<=30000) OR (`wage_max`>=15000 AND `wage_max`<=30000) ) ";
			break;
		}	
		case 9:{//3万以上
			$option .= " AND (`wage_min`>=30000 OR `wage_max`>=30000) ";
			break;
		}	
		default:
			break;
	}	
}
//END

/*
if ($keyword){
	$option .= " AND `jobs_name` LIKE '%$keyword%'";
}
if ($category_id){
	$option .= " AND (`category`=$category_id OR `subclass`=$category_id)";
}
if ($trade_id){
	$option .= " AND `trade`=$trade_id";
}
if ($area_id){
	$option .= " AND (`district`=$area_id OR `sdistrict`=$area_id)";
}
if ($education_id){
	$option .= " AND `education`=$education_id";
}
if ($sex_id){
	$option .= " AND `sex`=$sex_id";
}
if ($experience_id){
	$option .= " AND `experience`=$experience_id";
}
if ($wage_type){
	$option .= " AND `wage`=$wage_type";
}
*/

//处理列表页相关分类
if ($link_category){//指定分类
	//该分类下的总职位数
	$sql = "SELECT * FROM ".table('category_jobs')." WHERE id=$link_category";
	$qry = $db->query($sql);
	$res = $db->fetch_array($qry);
	$smarty->assign('related_category_info',$res);
//	e(get_category_related_to_category($category_id));
	$res_category = get_category_related_to_category($link_category);
	

	$smarty->assign('backup_level', true);
	$sql = "SELECT id,parentid,categoryname FROM ".table('category_jobs')." WHERE id=".$link_category;
	$res = $db->getone($sql);
	$smarty->assign('backup_level_id', $res['parentid']);
	$smarty->assign('current_info', "{'id':$res[id],'name':'$res[categoryname]'}");
	//查询父类信息
	if($res['parentid'] > 0){
		$sql = "SELECT id,categoryname FROM ".table('category_jobs')." WHERE id=".$res['parentid'];
		$res = $db->getone($sql);
		$smarty->assign('parent_info', "{'id':$res[id],'name':'$res[categoryname]'}");
	}
	
}
else{//没指定分类,则根据关键词搜索相关
	$res_category = get_category_related_to_key($keyword);
}

$smarty->assign('related_category',$res_category);
$smarty->assign('related_category_url',rebuild_url('jobcategory'));


//可指定行业数据
$res_industry = get_category_zt('QS_trade');
$smarty->assign('category_industry', $res_industry);
$smarty->assign('category_industry_url', rebuild_url('trade'));
//END

//END



$page_num = 20;//每页显示条数
$sql = "SELECT * FROM ".table('jobs')." $option";
#echo $sql;
$sql_total = "SELECT COUNT(*) num FROM ".table('jobs')." $option";
$total = $db->get_total($sql_total);


if ($total > 0){
	$page_html = dPage($total, $page, rebuild_url('page'),$page_num);
}

$limit = " LIMIT ".(($page==0)?0:($page-1))*$page_num.", $page_num";
$order = " ORDER BY refreshtime DESC, addtime DESC";
$sql .= $order.$limit;

#echo $sql;
$res = $db->getall($sql);
$smarty->assign('job_list',$res);
$smarty->assign('page_html_1',$page_html);

$page_html_2 = str_replace('pageno', 'pageno_2', $page_html);

$smarty->assign('page_html_2',$page_html_2);

//e($res);
//查询地区下拉单的筛选条件
$select_area = array();
$res = get_category_district_zt();
if (!empty($res)){

	foreach ($res as $v){
		$select_area[$v['id']] = $v['categoryname'];
	}
#	e($select_area);
	$smarty->assign('options_area', $select_area);
}

//
$select_edu = array();
$res = get_category_zt('QS_education');
foreach ($res as $v){
	$select_edu[$v['c_id']] = $v['c_name'];
}
$smarty->assign('options_edu',array_reverse($select_edu,1));

//
/*
$select_exp = array();
$res = get_category_zt('QS_experience');
foreach ($res as $v){
	$select_exp[$v['c_id']] = $v['c_name'];
}
$smarty->assign('options_exp',$select_exp);
*/
//
$select_wage = array();
$res = get_category_zt('QS_wage');
foreach ($res as $v){
	$select_wage[$v['c_id']] = $v['c_name'];
}
$smarty->assign('options_wage',$select_wage);
//END

$smarty->display($mypage['tpl'],$cached_id);
$db->close();
}
else
{
$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>
