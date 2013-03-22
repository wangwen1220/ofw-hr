<?php
 /*
 * 74cms 网站首页
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_index";

require_once(dirname(__FILE__).'/include/common.inc.php');

//开启静页面
$mypage['caching'] = 0; //一小时3600


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

//测试数据
//首页最新招聘数据
$smarty->assign('recent_joblist',get_recent_joblist());

//获取首页岗位分类数据 get_category_list()
#var_dump(array_chunk(get_category_list(),8));
$category_list = array_chunk(get_category_list(),8);
for ($i = 0; $i < count($category_list); $i++){
	$category_pages .= '<a href="javascript:void(0);" onclick="category_page('.$i.');">'. ($i+1) . '</a>';
}
$smarty->assign('category_pages',$category_pages);
$smarty->assign('category_list',$category_list);


//获取首页按行业分类数据get_industry_list()
#e(get_industry_list());
$smarty->assign('industry_list',get_industry_list());
#exit;
//end
$smarty->display($mypage['tpl'],$cached_id);
}
else
{
$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>