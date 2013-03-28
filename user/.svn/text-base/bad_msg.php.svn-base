<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$act = isset($_GET['act']) ? $_GET['act'] : '';
if ($act == 'feedback_save') 
{
	$setsqlarr['title']=trim($_POST['title']);          //举报公司名称
	$setsqlarr['feedback']=trim($_POST['feedback']);    //具体描述
	$setsqlarr['uid']=$_SESSION['uid'];                 //用户编号
	$setsqlarr['usertype']=$_SESSION['utype'];         //用户类型
	$setsqlarr['username']=$_SESSION['username'];      //用户名称
        $setsqlarr['infotype']=5;                          //不良信息标识
	$setsqlarr['addtime']=$timestamp;                  //添加时间
	//处理附件
	$setsqlarr['file'] = move_upload('file', $_SESSION['uid'], 'gif/jpg/bmp/png',4, 1*1024);
	!inserttable('hr_feedback',$setsqlarr)?showmsg("添加失败！",0):showmsg("添加成功，请等待管理员回复！",2);
}

?>
