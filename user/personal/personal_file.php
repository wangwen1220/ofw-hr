<?php 
/*
 * ���˸��� 2012��10��11�� 10:23:55
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');



if (($_GET['action'] == 'del') && intval($_GET['id'])){
	//ɾ���ļ�
	$sql = "SELECT * FROM ".table('resume_file')." WHERE id=".intval($_GET['id']);
	$res = $db->getone($sql);
	
	if ($user['uid'] == $res['uid']){//�Ǹ��û��ϴ��ĸ���
		
		if ($res['type'] == 1){//ɾ��������Ƭ,��Ҫ���¼������ͷ���ֶ�����
			$sql = "SELECT * FROM ".table('resume_file')." WHERE `uid`=".$res['uid']." AND `type`=1 ORDER BY `upload_time` DESC LIMIT 1,1";
			$img = $db->getone($sql);
			if (!empty($img)){//������Ƭ����,���¼������ֶ�
				$db->query("UPDATE ".table('resume')." SET `photo_img`='$img[path]',`photo`=1 WHERE uid=".$res['uid']);
			}
			else{//û����Ƭ����,��ռ������ֶ�
				$db->query("UPDATE ".table('resume')." SET `photo_img`='',`photo`=0 WHERE uid=".$res['uid']);
			}
		}
		
		unlink('../../data/file/'.$res['path']);
		
		$sql = "DELETE FROM ".table('resume_file')." WHERE id=".intval($_GET['id']);
		$db->query($sql);
	//	echo $sql;exit;
		header("Location:${_CFG['site_dir']}user/personal/personal_file.php");
		exit;
	}
}



//��ѯ����
$sql = "SELECT * FROM ".table('resume_file')." WHERE uid=".$user['uid']." ORDER BY `type` ASC,`upload_time` DESC ";
$res = $db->getall($sql);
foreach ($res as &$v){
	$size1 = round($v['size']/1024);
	$v['size'] = $size1.'KB';
	if ($size1 > 1000){
		$size2 = round($size1/1024,2);
		$v['size'] = $size2.'MB';
	}
	if ($size2 > 1000){
		$size3 = round($size2/1024,2);
		$v['size'] = $size3.'GB';
	}
	
}

#e($res);
$smarty->assign('file_info',$res);



if ($_POST['type'] != '' && $_POST['name'] != ''){
	//word��ppt��pdf��jpg��gif��gpeg��bmp��tiff
	file_upload('file', $_SESSION['uid'], 'gif/jpg/jpeg/bmp/png/doc/docx/ppt/pdf/tiff');
	header("Location:${_CFG['site_dir']}user/personal/personal_file.php");
}


$html_body = 'personal_file';
$smarty->assign('html_body',$html_body);
$smarty->assign('page_title','��������-��������');
$smarty->display('member_personal/personal_index.htm');
?>