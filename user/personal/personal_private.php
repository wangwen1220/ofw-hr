<?php
/*
 * ������������ 2012��10��11�� 11:13:24
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');

if ($_POST['submit']){
	$wheresql=" uid=".intval($_SESSION['uid'])."";

	//���û�ID��������˾������
	$uid = $_SESSION['uid'];
	//���¾��ֶ�2 2,3 1,2,3
	$sql = " UPDATE ".table('company_profile')." SET forbidden=IF( forbidden REGEXP '$uid' ,REPLACE( IF( RIGHT(forbidden, 1)!=',',CONCAT(forbidden, ','), forbidden), '$uid,', ''), forbidden)"; 
	$db->query($sql);	
	
	if ($_POST['hide'] == 1){//��ȫ����
		$setsqlarr['hideall']=1;
		$setsqlarr['hidesome']=0;
		$sql = "UPDATE ".table('company_profile')." SET forbidden=IF(forbidden='', '$uid',IF(RIGHT(forbidden, 1)!=',',CONCAT(CONCAT(forbidden, ','), '$uid'), CONCAT(forbidden,'$uid')  )) ";
		$db->query($sql);		
	}
	else if ($_POST['hide'] == 0){//��������
		$setsqlarr['hideall']=0;
		$setsqlarr['hidesome']=1;
		

		//���²������ֶ�
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
	showmsg("���óɹ���",2);
}

$sql = "SELECT hideall,hidesome,hidestr FROM ".table('resume')." WHERE uid=".$_SESSION['uid'];
$res = $db->getone($sql);
$res['company'] = explode(',', $res['hidestr']);
$smarty->assign('private',$res);

$html_body = 'personal_private';
$smarty->assign('html_body',$html_body);
$smarty->assign('page_title','��������-�����趨');
$smarty->display('member_personal/personal_index.htm');
unset($smarty);
?>