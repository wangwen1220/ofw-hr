<?php
 /*
 * 74cms 资讯首页
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_news";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

//职场动态
$article_1 = z_article_list(1);
$smarty->assign('article_1', $article_1);
//面试指南
$article_2 = z_article_list(2);
$smarty->assign('article_2', $article_2);
//简历指导
$article_3 = z_article_list(3);
$smarty->assign('article_3', $article_3);
//职业规划
$article_4 = z_article_list(4);
$smarty->assign('article_4', $article_4);


$smarty->assign('title', '职场资讯');
$smarty->display('news.htm');
$db->close();
unset($smarty);
?>