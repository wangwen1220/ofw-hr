<?php
define('IN_QISHI', true);

//使用跟搜索页相同 的配置
$alias="QS_jobs";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
        $smarty->cache =true;
		$smarty->cache_lifetime=$mypage['caching'];
	}else{
		$smarty->cache = false;
	}
$cached_id=$_CFG['subsite_id']."|".$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');

//更改$_PAGE的模板文件
$mypage['tpl'] = 'job_search.htm';
if(!$smarty->is_cached($mypage['tpl'],$cached_id))
{
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

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



$smarty->display($mypage['tpl'],$cached_id);
$db->close();
}
else
{
$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);