<?php
/**
 * ����OFweek�޸���������
 * 
 * $_POST['username'],$_POST['oldpwd'],$_POST['newpwd']
 */

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

if (!empty($_POST)) {
	
	$logfile = dirname(__FILE__).'/pwd.log';
	$logtxt = date('Y-m-d H:i:s', time());
	$logtxt .= '[pwd]';
	foreach ($_POST as $key=>$value) {
		$logtxt .= "$key:$value,";
	}
	$logtxt .= "\r\n";
	error_log($logtxt, 3, $logfile);
	
	$username = trim($_POST['username']);
	$password = trim($_POST['oldpwd']);
	$newpassword = trim($_POST['newpwd']);
	
	if ($username && $password && $newpassword) {
		$members = $db->getone("SELECT * FROM ".table('members')." WHERE username='$username'");
		
		if (!empty($members)) {
			
			$md5password = md5($password);
			if ($md5password == $members['password']) {
				$md5password = md5($newpassword);
				//error_log(date('Y/m/d H:i:s', time()).' '.serialize($_POST)." UPDATE ".table('members')." SET password = '$md5password'  WHERE username='".$username."'\r\n",3,'log.txt');
				$db->query("UPDATE ".table('members')." SET password = '$md5password'  WHERE username='".$username."'");
				write_memberslog($members['uid'],$members['utype'],1004,$members['username'],"�޸�������");
				
				echo 1;//�ɹ�
			} else {
				echo 0;//ʧ�ܣ����벻��
			}
		} else {
			echo 1;//�û�������
		}
	} else {
		echo 0;//��������
	}
} else {
	echo 0;
}
