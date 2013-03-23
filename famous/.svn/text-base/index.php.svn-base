<?php
define('IN_QISHI', true);
$alias="QS_other";
require_once(dirname(__FILE__).'/../include/common.inc.php');

require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

$t = time();

/*名企动态*/
#pic
$article_pic = z_article_pic(5);
$smarty->assign('article_pic', $article_pic);

#list
$idarr = array();
if (!empty($article_pic)) {
	$idarr[] = $article_pic['id'];
}
$article_list = z_article_list(5, 6, $idarr);
$smarty->assign('article_list', $article_list);


$list = array();
/*名企列表*/
$sql = "SELECT cp.uid,cp.companyname,cp.logo 
	FROM hr_company_profile cp
	INNER JOIN hr_members_setmeal ms ON cp.uid=ms.uid
	WHERE cp.company_type=2 AND (cp.audit=1 OR cp.audit=4) AND (ms.endtime=0 OR ms.endtime>$t)
	ORDER BY cp.company_type_addtime DESC
	LIMIT 100";
$list_temp = $db->getall($sql);

/*名企职位*/
if (!empty($list_temp)) {
	foreach ($list_temp as $k=>$c) {
		$uid = $c['uid'];
		$sql = "SELECT id,jobs_name 
			FROM hr_jobs
			WHERE uid='$uid' AND (audit=1 OR audit=4) AND display=1 AND (deadline=0 OR deadline>$t)
			ORDER BY refreshtime DESC
			LIMIT 2";
		
		$temp = $db->getall($sql);
		if (count($temp) >= 2) {
			$aa = $c;
			$aa['jobs'] = $temp;
			$list[] = $aa;
			if (count($list) >= 18) {
				break;
			}
		}
	}
}

$smarty->assign('list', $list);
$smarty->display('famous/index.htm');
unset($smarty);