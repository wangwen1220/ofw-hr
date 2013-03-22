<?php
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/common.inc.php');
$smarty->cache = false;
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
#e($_SESSION);


if ($_SESSION['uid']=='' || $_SESSION['username']==''){//未登录, 退出
	down_forbidden();
}

//根据下载的文件ID号, 判断是否属于此用户,是则提供下载,否则退出
$id = intval($_GET['id'])?intval($_GET['id']):0;
$sql = "SELECT * FROM ".table('resume_file')." WHERE id=$id";
$res = $db->getone($sql);
if (empty($res)){
	down_forbidden();
}

if ($_SESSION['admin_id']) {//管理员
	downfile(QISHI_ROOT_PATH.'data/file/'.$res['path'], $res['name'], $res['ext'], $res['size']);
}

elseif ($_SESSION['utype'] == 2){//个人用户
	if ($res['uid'] == $_SESSION['uid']){//文件所有者是该用户
		downfile(QISHI_ROOT_PATH.'data/file/'.$res['path'], $res['name'], $res['ext'], $res['size']);
	}
	else{
		down_forbidden();
	}
}

elseif($_SESSION['utype'] == 1){//企业
	$p_uid = $res['uid'];
	$c_uid = $_SESSION['uid'];
	
	//是否应聘简历
	$applyInfo = $db->get_total("SELECT COUNT(*) num FROM hr_personal_jobs_apply WHERE personal_uid=$p_uid AND company_uid=$c_uid AND del!=1");
	
	//是否下载简历
	$downInfo = $db->get_total("SELECT COUNT(*) num FROM hr_company_down_resume WHERE resume_uid='$p_uid' AND company_uid='$c_uid'");
	
	if ($applyInfo || $downInfo) {
		downfile(QISHI_ROOT_PATH.'data/file/'.$res['path'], $res['name'], $res['ext'], $res['size']);
	} else {
		showmsg("请先下载该简历！",0);
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