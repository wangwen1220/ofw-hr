<?php
/*
 * 简历保密设置 2012年10月11日 11:13:24
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');

if ($_POST['submit']){
	$wheresql=" uid=".intval($_SESSION['uid'])."";

	//将用户ID储存至公司数据中
	$uid = $_SESSION['uid'];
	//更新旧字段2 2,3 1,2,3
	$sql = " UPDATE ".table('company_profile')." SET forbidden=IF( forbidden REGEXP '$uid' ,REPLACE( IF( RIGHT(forbidden, 1)!=',',CONCAT(forbidden, ','), forbidden), '$uid,', ''), forbidden)"; 
	$db->query($sql);	
	
	if ($_POST['hide'] == 1){//完全隐藏
		$setsqlarr['hideall']=1;
		$setsqlarr['hidesome']=0;
		$sql = "UPDATE ".table('company_profile')." SET forbidden=IF(forbidden='', '$uid',IF(RIGHT(forbidden, 1)!=',',CONCAT(CONCAT(forbidden, ','), '$uid'), CONCAT(forbidden,'$uid')  )) ";
		$db->query($sql);		
	}
	else if ($_POST['hide'] == 0){//部分隐藏
		$setsqlarr['hideall']=0;
		$setsqlarr['hidesome']=1;
		

		//重新插入新字段
		if (!empty($_POST['company'])){
			foreach ($_POST['company'] as $v){
				if ($v == '') continue;
				$sql = " SELECT id FROM ".table('company_profile')." WHERE companyname LIKE '%$v%'";
				$res = $db->getall($sql);
				if (!empty($res)){
					foreach ($res as $val){
						if ($val['id']){
							$sql = "UPDATE ".table('company_profile')." SET forbidden=IF(forbidden='', '$uid',IF(RIGHT(forbidden, 1)!=',',CONCAT(CONCAT(forbidden, ','), '$uid'), CONCAT(forbidden,'$uid')  )) WHERE id=${val['id']} ";
							$db->query($sql);
						}
					}
				}
			}
		}
	}
	$setsqlarr['hidestr']=implode(',', $_POST['company']);
	updatetable(table('resume'),$setsqlarr,$wheresql);
	showmsg("设置成功！",2);
}

$sql = "SELECT hideall,hidesome,hidestr FROM ".table('resume')." WHERE uid=".$_SESSION['uid'];
$res = $db->getone($sql);
$res['company'] = explode(',', $res['hidestr']);
$smarty->assign('private',$res);

$html_body = 'personal_private';
$smarty->assign('html_body',$html_body);
$smarty->assign('page_title','个人中心-保密设定');
$smarty->display('member_personal/personal_index.htm');
unset($smarty);
?>