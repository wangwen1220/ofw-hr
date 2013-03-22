<?php
 /*
 * 74cms 资讯列表页
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_newslist";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

//分类id
$typeid = $_GET['id'];
$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
$num = 15;
$limit_start = ($page - 1) * $num;

//分类信息
$typeInfo = z_article_type($typeid);
if (empty($typeInfo)) {
	$link[0]['text'] = "职场资讯";
	$link[0]['href'] = $_CFG['site_domain'].'/news/';
	showmsg('页面不存在',0,$link);
}
$smarty->assign('typeInfo', $typeInfo);

//总数
$sql = "SELECT COUNT(*) num FROM hr_article WHERE is_display=1 AND type_id=$typeid";
$total = $db->get_total($sql);
//列表数据
$sql = "SELECT * 
	FROM hr_article
	WHERE is_display=1 AND type_id=$typeid
	ORDER BY addtime DESC
	LIMIT $limit_start,$num";
$artcileList = $db->getall($sql);
foreach ($artcileList as $key=>$value) {
	$artcileList[$key]['addtime_cn'] = date('Y-m-d', $value['addtime']);
	$value['content'] = str_replace('&nbsp;', ' ', $value['content']);
	$artcileList[$key]['briefly'] = cut_str(strip_tags($value['content']),160,0,'...');
}
$smarty->assign('artcileList', $artcileList);
//分页
$pageHTML = dPage_2($total, $page, "news-list.php?id=$typeid", $num);	
$smarty->assign('pageHTML', $pageHTML);


//推荐
$recommendList = z_article_recommend($typeid);
$smarty->assign('recommendList', $recommendList);

//最新
$newList = z_article_new(10);
$smarty->assign('newList', $newList);


$smarty->assign('title', $typeInfo['categoryname']);
$smarty->display('news-list.htm');
$db->close();

unset($smarty);
?>