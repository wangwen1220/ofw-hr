<?php
 /*
 * 74cms 资讯详细页面
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_newsshow";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

//参数
$id = intval($_GET['id']);

//资讯
$article = z_article($id);
if (empty($article)) {
	$link[0]['text'] = "职场资讯";
	$link[0]['href'] = $_CFG['site_domain'].'/news/';
	showmsg('页面不存在',0,$link);
}
$smarty->assign('article', $article);

//分类信息
$typeInfo = z_article_type($article['type_id']);
$smarty->assign('typeInfo', $typeInfo);

//推荐
$recommendList = z_article_recommend($article['type_id']);
$smarty->assign('recommendList', $recommendList);

//最新
$newList = z_article_new(10);
$smarty->assign('newList', $newList);

$smarty->assign('title', $article['title']);
$smarty->display('news-show.htm');
unset($smarty);
?>