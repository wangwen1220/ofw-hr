<?php
 /*
 * 74cms ��Ѷ��ҳ
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_news";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

//ְ����̬
$article_1 = z_article_list(1);
$smarty->assign('article_1', $article_1);
//����ָ��
$article_2 = z_article_list(2);
$smarty->assign('article_2', $article_2);
//����ָ��
$article_3 = z_article_list(3);
$smarty->assign('article_3', $article_3);
//ְҵ�滮
$article_4 = z_article_list(4);
$smarty->assign('article_4', $article_4);


$smarty->assign('title', 'ְ����Ѷ');
$smarty->display('news.htm');
$db->close();
unset($smarty);
?>