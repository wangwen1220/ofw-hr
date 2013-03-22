<?php
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/common.inc.php');
$smarty->cache = false;
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
#e($_SESSION);


if ($_SESSION['uid']=='' || $_SESSION['username']==''){//δ��¼, �˳�
	down_forbidden();
}

//�������ص��ļ�ID��, �ж��Ƿ����ڴ��û�,�����ṩ����,�����˳�
$id = intval($_GET['id'])?intval($_GET['id']):0;
$sql = "SELECT * FROM ".table('resume_file')." WHERE id=$id";
$res = $db->getone($sql);
if (empty($res)){
	down_forbidden();
}

if ($_SESSION['admin_id']) {//����Ա
	downfile(QISHI_ROOT_PATH.'data/file/'.$res['path'], $res['name'], $res['ext'], $res['size']);
}

elseif ($_SESSION['utype'] == 2){//�����û�
	if ($res['uid'] == $_SESSION['uid']){//�ļ��������Ǹ��û�
		downfile(QISHI_ROOT_PATH.'data/file/'.$res['path'], $res['name'], $res['ext'], $res['size']);
	}
	else{
		down_forbidden();
	}
}

elseif($_SESSION['utype'] == 1){//��ҵ
	$p_uid = $res['uid'];
	$c_uid = $_SESSION['uid'];
	
	//�Ƿ�ӦƸ����
	$applyInfo = $db->get_total("SELECT COUNT(*) num FROM hr_personal_jobs_apply WHERE personal_uid=$p_uid AND company_uid=$c_uid AND del!=1");
	
	//�Ƿ����ؼ���
	$downInfo = $db->get_total("SELECT COUNT(*) num FROM hr_company_down_resume WHERE resume_uid='$p_uid' AND company_uid='$c_uid'");
	
	if ($applyInfo || $downInfo) {
		downfile(QISHI_ROOT_PATH.'data/file/'.$res['path'], $res['name'], $res['ext'], $res['size']);
	} else {
		showmsg("�������ظü�����",0);
	}	
} 



function downfile($file,$name,$ext,$size){
	if (file_exists($file)){
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.$name.'.'.$ext);
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: ' . $size);
	    ob_clean();
	    flush();
	    readfile($file);
	}
	else {
		down_forbidden();
	}
    exit;
}
function down_forbidden(){
	header('http/1.1 403 Forbidden');
	exit;
}