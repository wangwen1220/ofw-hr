<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$act = isset($_GET['act']) ? $_GET['act'] : '';
if ($act == 'feedback_save') 
{
	$setsqlarr['title']=trim($_POST['title']);          //�ٱ���˾����
	$setsqlarr['feedback']=trim($_POST['feedback']);    //��������
	$setsqlarr['uid']=$_SESSION['uid'];                 //�û����
	$setsqlarr['usertype']=$_SESSION['utype'];         //�û�����
	$setsqlarr['username']=$_SESSION['username'];      //�û�����
        $setsqlarr['infotype']=5;                          //������Ϣ��ʶ
	$setsqlarr['addtime']=$timestamp;                  //���ʱ��
	//������
	$setsqlarr['file'] = move_upload('file', $_SESSION['uid'], 'gif/jpg/bmp/png',4, 1*1024);
	!inserttable('hr_feedback',$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա�ظ���",2);
}

?>
