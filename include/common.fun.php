<?php
 /*
 * 74cms ���ú���
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI')) die('Access Denied!');
function addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
		if (!get_magic_quotes_gpc())
		{
		$value=is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
		}
//		$value=is_array($value) ? array_map('addslashes_deep', $value) : mystrip_tags($value);//��BUG,�Ѹ�дǰ��ر�magic_gpc
		if(!is_array($value)) $value =  mystrip_tags($value);
		return $value;
    }
}
function mystrip_tags($string)
{
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
	$string = strip_tags($string);
	return $string;
}
function table($table)
{
 	global $pre;
    return $pre .$table ;
}
function showmsg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true,$seconds=3)
{
	global $smarty;
    if (count($links) == 0)
    {
        $links[0]['text'] = '������һҳ';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }
   $smarty->assign('ur_here',     'ϵͳ��ʾ');
   $smarty->assign('msg_detail',  $msg_detail);
   $smarty->assign('msg_type',    $msg_type);
   $smarty->assign('links',       $links);
   $smarty->assign('default_url', $links[0]['href']);
   $smarty->assign('auto_redirect', $auto_redirect);
   $smarty->assign('seconds', $seconds);
   $smarty->display('showmsg.htm');
	exit;
}
function get_smarty_request($str)
{
$str=rawurldecode($str);
$strtrim=rtrim($str,']');
	if (substr($strtrim,0,4)=='GET[')
	{
	$getkey=substr($strtrim,4);
	return $_GET[$getkey];
	}
	elseif (substr($strtrim,0,5)=='POST[')
	{
	$getkey=substr($strtrim,5);
	return $_POST[$getkey];
	}
	else
	{
	return $str;
	}
}
function get_cache($cachename)
{
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_".$cachename.".php";
	@include($cache_file_path);
	return $data;
}
function exectime(){ 
	$time = explode(" ", microtime());
	$usec = (double)$time[0]; 
	$sec = (double)$time[1]; 
	return $sec + $usec; 
}
function check_word($noword,$content)
{
	$word=explode('|',$noword);
	if (!empty($word) && !empty($content))
	{
		foreach($word as $str)
		{
			if(!empty($str) && strstr($content,$str))
			{
			return true;
			}

		}
	}
	return false;
}

//����Ƿ������д�
function z_check_banword($content) {
	$word = z_get_banword();
	
	if (!empty($word) && !empty($content)) {
		foreach($word as $str) {
			if(!empty($str) && strstr($content,$str)) {
				return true;
			}
		}
	}
	return false;
}

//������д�
function z_get_banword() {
	global $db;
	$time = time();
	$cache_file = QISHI_ROOT_PATH."data/cache_banword.php";
	
	if (file_exists($cache_file)) {
		$c = file_get_contents($cache_file);
		$c = unserialize(base64_decode($c));
		
		if ($time - $c['addtime'] < 1*24*60*60) {//һ��������Ч
			return $c['list'];
		}
	}
	
	$c['addtime'] = $time;
	$r = $db->getall("SELECT replacefrom FROM hr_banword");
	foreach ($r as $value) {
		!empty($value['replacefrom']) && $c['list'][] = $value['replacefrom'];
	}
	
	$filecontent = "<?php return '".base64_encode(serialize($c))."';";
	
	//д�ļ�
	$fp = fopen($cache_file, 'w') or die("can't open file");
	fwrite($fp, $filecontent);
	fclose($fp);
	
	return $c['list'];
}

function getip()
{
	if (getenv('HTTP_CLIENT_IP') and strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {
		$onlineip=getenv('HTTP_CLIENT_IP');
	}elseif (getenv('HTTP_X_FORWARDED_FOR') and strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {
		$onlineip=getenv('HTTP_X_FORWARDED_FOR');
	}elseif (getenv('REMOTE_ADDR') and strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {
		$onlineip=getenv('REMOTE_ADDR');
	}elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] and strcasecmp($_SERVER['REMOTE_ADDR'],'unknown')) {
		$onlineip=$_SERVER['REMOTE_ADDR'];
	}
	preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/",$onlineip,$match);
	return $onlineip = $match[0] ? $match[0] : 'unknown';
}
function inserttable($tablename, $insertsqlarr, $returnid=0, $replace = false, $silent=0) {
	global $db;
	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	$method = $replace?'REPLACE':'INSERT';
#	echo $method." INTO $tablename ($insertkeysql) VALUES ($insertvaluesql)";die;
	$state = $db->query($method." INTO $tablename ($insertkeysql) VALUES ($insertvaluesql)", $silent?'SILENT':'');
	if($returnid && !$replace) {
		return $db->insert_id();
	}else {
	    return $state;
	} 
}
function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {
	global $db;
	$setsql = $comma = '';
	foreach ($setsqlarr as $set_key => $set_value) {
		if(is_array($set_value)) {
			$setsql .= $comma.'`'.$set_key.'`'.'='.$set_value[0];
		} else {
			$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
		}
		$comma = ', ';
	}
	$where = $comma = '';
	if(empty($wheresqlarr)) {
		$where = '1';
	} elseif(is_array($wheresqlarr)) {
		foreach ($wheresqlarr as $key => $value) {
			$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
		}
	} else {
		$where = $wheresqlarr;
	}
	$sql = "UPDATE ".($tablename)." SET ".$setsql." WHERE ".$where;
	return $db->query("UPDATE ".($tablename)." SET ".$setsql." WHERE ".$where, $silent?"SILENT":"");
}
function wheresql($wherearr='')
{
	$wheresql="";
	if (is_array($wherearr))
		{
		$where_set=' WHERE ';
			foreach ($wherearr as $key => $value)
			{
			$wheresql .=$where_set. $comma.$key.'="'.$value.'"';
			$comma = ' AND ';
			$where_set=' ';
			}
		}
	return $wheresql;
}
function convert_datefm ($date,$format,$separator="-")
{
	 if ($format=="1")
	 {
	 return date("Y-m-d", $date);
	 }
	 else
	 {
		if (!preg_match("/^[0-9]{4}(\\".$separator.")[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/",$date))  return false;
		$date=explode($separator,$date);
		return mktime(0,0,0,$date[1],$date[2],$date[0]);
	 }
}
function sub_day($endday,$staday,$range='')
{
	$value = $endday - $staday;
	if($value < 0)
	{
		return '';
	}
	elseif($value >= 0 && $value < 59)
	{
		return ($value+1)."��";
	}
	elseif($value >= 60 && $value < 3600)
	{
		$min = intval($value / 60);
		return $min."����";
	}
	elseif($value >=3600 && $value < 86400)
	{
		$h = intval($value / 3600);
		return $h."Сʱ";
	}
	elseif($value >= 86400 && $value < 86400*30)
	{
		$d = intval($value / 86400);
		return intval($d)."��";
	}
	elseif($value >= 86400*30 && $value < 86400*30*12)
	{
		$mon  = intval($value / (86400*30));
		return $mon."��";
	}
	else{	
		$y = intval($value / (86400*30*12));
		return $y."��";
	}
}
function daterange($endday,$staday,$format='Y-m-d',$color='',$range=3)
{
	$value = $endday - $staday;
	if($value < 0)
	{
		return '';
	}
	elseif($value >= 0 && $value < 59)
	{
		$return=($value+1)."��ǰ";
	}
	elseif($value >= 60 && $value < 3600)
	{
		$min = intval($value / 60);
		$return=$min."����ǰ";
	}
	elseif($value >=3600 && $value < 86400)
	{
		$h = intval($value / 3600);
		$return=$h."Сʱǰ";
	}
	elseif($value >= 86400)
	{
		$d = intval($value / 86400);
		if ($d>$range)
		{
		return date($format,$staday);
		}
		else
		{
		$return=$d."��ǰ";
		}
	}
	if ($color)
	{
	$return="<span style=\"color:{$color}\">".$return."</span>";
	}
	return $return;	 
}
function cut_str($string, $length, $start=0,$dot='') 
{
		$length=$length*2;
		if(strlen($string) <= $length) {
			return $string;
		}
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
		$strcut = '';	 
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
		return $strcut.$dot;
}
//��ȡ�ʼ�ģ��
function get_mail_template($tpl){
	global $_CFG;
	if($tpl == ''){
		return false;
	}
	return file_get_contents(QISHI_ROOT_PATH.'templates/'.$_CFG['template_dir'].'mail_template/'.$tpl);
}

//�����ʼ�
function z_mail($act, $mailArr, $data = array(), $z_uid = 0) {
	global $smarty;
	$uid = $z_uid ? $z_uid : $_SESSION['uid'];
	
	$mailArr['to'] = trim($mailArr['to']);
	$mailArr['body'] && $mailArr['body'] = nl2br($mailArr['body']);
	
	//from
	$fromList = array(
		'company_register'       => array('smtp'=>'service','from'=>'service@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'person_register'        => array('smtp'=>'service','from'=>'service@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'person_register_noactive'        => array('smtp'=>'service','from'=>'service@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'interview'              => array('smtp'=>'interview','from'=>'interview@ofweek.com','fromName'=>'OFweek�˲���'),//reply-to:��˾
		'recommend_job'          => array('smtp'=>'recommend','from'=>'recommend@hr.ofweek.com','fromName'=>'OFweek�˲���'),//reply-to:�Ƽ���
		'company_audit_allow'    => array('smtp'=>'audit','from'=>'audit@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'company_audit_notallow' => array('smtp'=>'audit','from'=>'audit@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'job_audit_allow'        => array('smtp'=>'audit','from'=>'audit@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'job_audit_notallow'     => array('smtp'=>'audit','from'=>'audit@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'resume'                 => array('smtp'=>'applyjob','from'=>'applyjob@hr.ofweek.com','fromName'=>'OFweek�˲���'),//reply-to:��ְ��
		'resume_audit_allow'     => array('smtp'=>'audit','from'=>'audit@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'resume_audit_notallow'  => array('smtp'=>'audit','from'=>'audit@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'setmeal'                => array('smtp'=>'notice','from'=>'notice@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'resume_refresh'         => array('smtp'=>'notice','from'=>'notice@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'resume_complete'        => array('smtp'=>'notice','from'=>'notice@hr.ofweek.com','fromName'=>'OFweek�˲���'),
		'free_resume'            => array('smtp'=>'applyjob','from'=>'applyjob@hr.ofweek.com','fromName'=>'OFweek�˲���'),//reply-to:��ְ��
		'free_resume_re'         => array('smtp'=>'recommend','from'=>'recommend@hr.ofweek.com','fromName'=>'OFweek�˲���'),//reply-to:ת����
		'get_password'           => array('smtp'=>'notice','from'=>'notice@hr.ofweek.com','fromName'=>'OFweek�˲���'),
	);
	
	
	//��֤
	if (empty($mailArr['to'])) {
		return false;
	}
	
	//�ⷢ����
	if ($act == 'free_resume') {
		assing_resume($uid,1,1);
		$body = $smarty->fetch('mail_template/outward.htm');
		return smtp_mail($mailArr['to'], $mailArr['subject'], $mailArr['body'].$body, $mailArr['from'], $mailArr['fromName'], $fromList[$act]['smtp']);
	}
	
	//ת������
	elseif ($act == 'free_resume_re') {
		assing_resume($data['resume_uid'], $data['downed'], 1);
		$body = $smarty->fetch('mail_template/outward.htm');
		return smtp_mail($mailArr['to'], $mailArr['subject'], $mailArr['body'].$body, $mailArr['from'], $mailArr['fromName'], $fromList[$act]['smtp']);
	}
		
	//��ѭ�ʼ�ģ�巢��
	else {		
		//subject
		$subjectList = array(
			'company_register'       => '��л��ע��OFweek�˲�����ҵ��Ա',
			'person_register'        => 'OFweek�˲���ע�ἤ���ʼ�',
			'person_register_noactive'        => 'OFweek�˲���ע�ἤ���ʼ�',
			'interview'              => '����֪ͨ-{company_name}',
			'recommend_job'          => '���ĺ���{fullname}�����Ƽ���ְλ-- {jobs_name}',
			'company_audit_allow'    => '���ͨ������ϲ����������ҵ�Ѿ�ͨ�����ǵ����',
			'company_audit_notallow' => '���δͨ����������ҵ����δͨ����ˣ��뼰ʱ�����޸�',
			'job_audit_allow'        => 'ͨ����ˣ���ϲ��������ְλ{jobs_name}��ͨ�����',
			'job_audit_notallow'     => 'δͨ����ˣ�����ְλ{jobs_name}δͨ����ˣ��뼰ʱ�����޸�',
			'resume'                 => '{fullname}-ӦƸ {jobs_name} ����OFweek�˲���������',
			'resume_audit_allow'     => '���ͨ������ϲ�������ļ����Ѿ�ͨ�����',
			'resume_audit_notallow'  => 'δͨ����ˣ����ļ���δͨ����ˣ��뼰ʱ�����޸�',
			'setmeal'                => '���ѿ�ͨOFweek{setmeal_name}',
			'resume_refresh'         => '{fullname}��ã����ļ�����Ҫ������',
			'resume_complete'        => '{fullname}��ã����ļ����ݲ����ƣ��뼰ʱ����',
			'get_password'           => 'OFweek�˲������������ʼ�',
		);
		
		
		if (!empty($mailArr['subject'])) {
			$subject = $mailArr['subject'];
		} else {
			$subject = $subjectList[$act];
			foreach ($data as $key=>$value) {
				$subject = str_replace('{'.$key.'}', $value, $subject);
			}
		}
		
		//body
		if ($act == 'resume') {
			assing_resume($uid,1,1);
			$body = $smarty->fetch('mail_template/outward.htm');
		} else {
			$file = dirname(dirname(__FILE__))."/templates/default/mail_template/$act.htm";
			if (file_exists($file)) {
				$body = file_get_contents($file);
				foreach ($data as $key=>$value) {
					$body = str_replace('{'.$key.'}', $value, $body);
				}
			} else {
				return false;
			}		
		}
		
		//������
		if (empty($mailArr['from'])) {
			$mailArr['from'] = $fromList[$act]['from'];
			$mailArr['fromName'] = $fromList[$act]['fromName'];
		}
		
		return smtp_mail($mailArr['to'], $subject, $body, $mailArr['from'], $mailArr['fromName'], $fromList[$act]['smtp']);		
	}
}

//ϵͳ����վ����
function z_message($act, $uid, $data = array()) {
	$memberInfo = z_member($uid);
	
	//subject
	$msgList = array(
		'company_register'=>array(
			'subject'=>'��л��ע��OFweek�˲�����ҵ��Ա',
			'body'=>'��л��ע���ΪOFweek�˲�����ҵ��Ա��OFweek�˲�����OFweek������վ�����й����ȵĸ߿Ƽ��˲���Դƽ̨��'
		),
		'person_register'=>array(
			'subject'=>'OFweek�˲���ע�ἤ���ʼ�',
			'body'=>'��л��ע���ΪOFweek�˲�����Ա��OFweek�˲�����OFweek������վ�����й����ȵĸ߿Ƽ��˲���Դƽ̨��'
		),
		'company_audit_allow'=>array(
			'subject'=>'���ͨ������ϲ����������ҵ�Ѿ�ͨ�����ǵ����',
			'body'=>'��ϲ����������ҵ��Ա�Ѿ�ͨ�����ǵ���ˡ������Է���ְλ�����ռ����ȷ���'
		), 
		'company_audit_notallow'=>array(
			'subject'=>'���δͨ����������ҵ����δͨ����ˣ��뼰ʱ�����޸�',
			'body'=>'�ǳ��ź���֪ͨ��������OFweek�˲���ע�����ҵ��Աδͨ�����ǵ���ˣ�δͨ����˵�ԭ����{reason}���⽫��Ӱ�쵽����������վ��ʹ�ã����Ĺ�˾�Լ���Ƹ��Ϣ�޷�����ְ�߲鿴����������'
		),
		'job_audit_allow'=>array(
			'subject'=>'ͨ����ˣ���ϲ��������ְλ{jobs_name}��ͨ�����',
			'body'=>'��ϲ������������ְλ��{jobs_name}ͨ�����ǵ���ˡ�����ְλ��ְ�߿��Խ��в��ġ�����ˢ������ְλ������ø�����ְ�߲��ĵĻ��ᡣ'
		),
		'job_audit_notallow'=>array(
			'subject'=>'δͨ����ˣ�����ְλ{jobs_name}δͨ����ˣ��뼰ʱ�����޸�',
			'body'=>'��л��ע��OFweek�˲�����Ա���ǳ��ź���֪ͨ��������OFweek�˲���ע��ķ�����ְλ{jobs_name}δͨ�����ǵ���ˣ�δͨ����˵�ԭ����{reason}����ְλְλ�޷�����ְ�߲鿴����������'
		), 
		'resume_audit_allow'=>array(
			'subject'=>'���ͨ������ϲ�������ļ����Ѿ�ͨ�����',
			'body'=>'��ϲ��������Ofweek�˲����ļ���ͨ�����ǵ���ˡ�������������Ͷ�ݼ���������ְλ�Ȳ�����������¼/ˢ�����ļ���������ø�����ҵ��������'
		), 
		'resume_audit_notallow'=>array(
			'subject'=>'δͨ����ˣ����ļ���δͨ����ˣ��뼰ʱ�����޸�',
			'body'=>'�ǳ��ź���֪ͨ��������OFweek�˲���ע��ļ���δͨ�����ǵ���ˣ�δͨ����˵�ԭ����{reason}���⽫��Ӱ�쵽������ְ��'
		) 
	);
	
	foreach ($data as $key=>$value) {
		$msgList[$act]['subject'] = str_replace('{'.$key.'}', $value, $msgList[$act]['subject']);
		$msgList[$act]['body'] = str_replace('{'.$key.'}', $value, $msgList[$act]['body']);
	}
	
	$data = array(
		'uid'=>$memberInfo['uid'],
		'replyinfo'=>1,
		'usertype'=>$memberInfo['utype'],
		'username'=>$memberInfo['username'],
		'infotype'=>9,
		'title'=>$msgList[$act]['subject'],
		'feedback'=>$msgList[$act]['body'],
		'reply'=>'',
		'addtime'=>time(),
		'feedbacktime'=>0,
		'file'=>''
	);
	
	inserttable('hr_feedback', $data);
}

//�����ʼ�
function smtp_mail($sendto_email,$subject,$body,$From='',$FromName='', $smtp){	
	global $_CFG, $z_mail_config;
	
	if ($From) {
		$From = $From;
		$FromName = $FromName;
	} else {
		$From = $z_mail_config[$smtp]['smtpfrom'];
		$FromName = 'OFweek�˲���';
	}
	
	require_once(QISHI_ROOT_PATH.'phpmailer/class.phpmailer.php');
	$mail = new PHPMailer();
		
	$mail->IsSMTP();
	$mail->Host = $z_mail_config[$smtp]['smtpservers'];
	$mail->SMTPDebug= 0; 
	$mail->SMTPAuth = true;
	$mail->Username = $z_mail_config[$smtp]['smtpusername']; 
	$mail->Password = $z_mail_config[$smtp]['smtppassword']; 
	$mail->Port = $z_mail_config[$smtp]['smtpport'];
	$mail->From = $z_mail_config[$smtp]['smtpfrom'];
	$mail->FromName = 'OFweek�˲���';	
	$mail->CharSet = 'GB2312';
	$mail->Encoding = "base64";
	$mail->AddReplyTo($From,$FromName);
	$mail->AddAddress($sendto_email,"");
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->Body =$body;
	$mail->AltBody ="text/html";
	if($mail->Send()) {
		return true;
	} else {
		//write_syslog(2,'MAIL',$mail->ErrorInfo);
		return false;
	}
}

function dfopen($url,$limit = 0, $post = '', $cookie = '', $bysocket = FALSE	, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCOD')
{
		$return = '';
		$matches = parse_url($url);
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;

		if($post) {
			$out = "POST $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$boundary = $encodetype == 'URLENCODE' ? '' : ';'.substr($post, 0, trim(strpos($post, "\n")));
			$out .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data$boundary\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host:$port\r\n";
			$out .= 'Content-Length: '.strlen($post)."\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cache-Control: no-cache\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
			$out .= $post;
		} else {
			$out = "GET $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host:$port\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}

		if(function_exists('fsockopen')) {
			$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		} elseif (function_exists('pfsockopen')) {
			$fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		} else {
			$fp = false;
		}

		if(!$fp) {
			return '';
		} else {
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			if(!$status['timed_out']) {
				while (!feof($fp)) {
					if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
						break;
					}
				}

				$stop = false;
				while(!feof($fp) && !$stop) {
					$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
					$return .= $data;
					if($limit) {
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
				}
			}
			@fclose($fp);
			return $return;
		}
}
function send_sms($mobile,$content)
{
	global $db;
	$sms=get_cache('sms_config');
	if ($sms['open']!="1" || empty($sms['sms_name']) || empty($sms['sms_key']) || empty($mobile) || empty($content))
	{
	return false;
	}
	else
	{
	return dfopen("http://www.74cms.com/SMSsend.php?sms_name={$sms['sms_name']}&sms_key={$sms['sms_key']}&mobile={$mobile}&content={$content}");
	}	
}
function execution_crons()
{
	global $db;
	$crons=$db->getone("select * from ".table('crons')." WHERE (nextrun<".time()." OR nextrun=0) AND available=1 LIMIT 1  ");
	if (!empty($crons))
	{
		include_once(QISHI_ROOT_PATH."include/crons/".$crons['filename']);
	}
}
function get_tpl($type,$id)
{
	global $db,$_CFG,$smarty;
	$id=intval($id);
	$tarr=array("jobs","company_profile","resume");
	if (!in_array($type,$tarr)) exit();
	$utpl=$db->getone("SELECT tpl FROM ".table($type)." WHERE id='{$id}' limit 1");
	$thistpl=$utpl['tpl'];
	if (!empty($_GET['style']))
	{
	$thistpl=$_GET['style'];
	}
	if (empty($thistpl))
	{
		if ($type=='resume')
		{
		$thistpl="../tpl_resume/{$_CFG['tpl_personal']}/{$type}.htm";
		$smarty->assign('user_tpl',$_CFG['site_dir']."templates/tpl_resume/{$_CFG['tpl_personal']}/");
		return $thistpl;
		}
		else
		{
		$thistpl="../tpl_company/{$_CFG['tpl_company']}/{$type}.htm";
		$smarty->assign('user_tpl',$_CFG['site_dir']."templates/tpl_company/{$_CFG['tpl_company']}/");
		return $thistpl;
		}
	}
	else
	{
		if ($type=='resume')
		{
			if (!file_exists(QISHI_ROOT_PATH."templates/tpl_resume/{$thistpl}/{$type}.htm"))
			{
			$thistpl=$_CFG['tpl_personal'];
			}
			$smarty->assign('user_tpl',$_CFG['site_dir']."templates/tpl_resume/{$thistpl}/");
		return "../tpl_resume/{$thistpl}/{$type}.htm";
		}
		else
		{
			if (!file_exists(QISHI_ROOT_PATH."templates/tpl_company/{$thistpl}/{$type}.htm"))
			{
			$thistpl=$_CFG['tpl_company'];
			}
		$smarty->assign('user_tpl',$_CFG['site_dir']."templates/tpl_company/{$thistpl}/");
		return "../tpl_company/{$thistpl}/{$type}.htm";
		}		
	}	
}
function url_rewrite($alias=NULL,$get=NULL,$rewrite=true)
{
	global $_CFG,$_PAGE;
	$url = $id  = $page ='';
#	if ($_PAGE[$alias]['url']=='0' || $rewrite==false)//ԭʼ����
#	{
	if(1){	
		$url =$_CFG['site_dir'].$_PAGE[$alias]['file'];
		isset($get['id0'])?$url .= '?id='.$get['id0']:'';		
		isset($get['page'])?$url .=(isset($get['id0'])?'&amp;':'?').'page='.$get['page']:'';
		return $url;
	}
	elseif ($_PAGE[$alias]['url']=='1')
	{
		$addtime=isset($get['addtime'])?getdate($get['addtime']):'';
		$url =$_CFG['site_dir'].$_PAGE[$alias]['rewrite'];
		$url=str_replace('($id)',$get['id0'],$url);
		if (!empty($addtime)){
		$url=str_replace('($y)',$addtime['year'],$url);
		$url=str_replace('($m)',$addtime['mon'],$url);
		$url=str_replace('($d)',$addtime['mday'],$url);
		}
		$get['page']=$get['page']?$get['page']:1;
		$url=str_replace('($page)',$get['page'],$url);
		return $url;
	}
}
function get_member_url($type,$dirname=false)
{
	global $_CFG;
	$type=intval($type);
	if ($type===0) 
	{
	return "";
	}
	elseif ($type===1)
	{
	$return=$_CFG['site_dir']."user/company/company_index.php";
	}
	elseif ($type===2) 
	{
	$return=$_CFG['site_dir']."user/personal/index.php";
	}
	if ($dirname)
	{
	return dirname($return).'/';
	}
	else
	{
	return $return;
	}
}
function fulltextpad($str)
{
	if (empty($str))
	{
	return '';
	}
	$leng=strlen($str);
	if ($leng>=8)
		{
		return $str;
	}
	else
	{
		$l=4-($leng/2);
		return str_pad($str,$leng+$l,'0');
	}
}
function asyn_userkey($uid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid = '".intval($uid)."' LIMIT 1";
	$user=$db->getone($sql);
	return md5($user['username'].$user['pwd_hash'].$user['password']);
}
function write_syslog($type,$type_name,$str)
{
 	global $db,$online_ip;
	$l_page = addslashes(request_url());
	$str = addslashes($str);
 	$sql = "INSERT INTO ".table('syslog')." (l_type, l_type_name, l_time,l_ip,l_page,l_str) VALUES ('{$type}', '{$type_name}', '".time()."','{$online_ip}','{$l_page}','{$str}')"; 
	return $db->query($sql);
}
function write_memberslog($uid,$utype,$type,$username,$str)
{
 	global $db,$online_ip;
 	$sql = "INSERT INTO ".table('members_log')." (log_uid,log_username,log_utype,log_type,log_addtime,log_ip,log_value) VALUES ( '{$uid}','{$username}','{$utype}','{$type}', '".time()."','{$online_ip}','{$str}')";
	return $db->query($sql);
}
function request_url()
{     
  	if (isset($_SERVER['REQUEST_URI']))     
    {        
   	 $url = $_SERVER['REQUEST_URI'];    
    }
	else
	{    
		  if (isset($_SERVER['argv']))        
			{           
			$url = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];      
			}         
		  else        
			{          
			$url = $_SERVER['PHP_SELF'] .'?'.$_SERVER['QUERY_STRING'];
			}  
    }    
    return urlencode($url);
}
//��socket �����ʼ��ӿ�
/*
 * $act  ��������
 * $arr  ��������б��������ֵ��ƥ�䣬��ӦΪURL�����б�
 */
function sdopen($act,$uid,$arr){
	global $_CFG;
	$query = '';
	if(isset($act)){
		if(!empty($arr)){
			$depar = '&';
			foreach($arr as $k => $v){
				$query .= $depar.$k.'='.urlencode($v);
			}
			$query .= '&'.'uid='.$uid;
			$query .= '&'.'key='.asyn_userkey($uid);
		}
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?act=".$act.$query);
		//echo $_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?act=".$act.$query;exit;
	}
}

function label_replace($templates)
{
	global $_CFG;
	$templates=str_replace('{sitename}',$_CFG['site_name'],$templates);
	$templates=str_replace('{sitedomain}',$_CFG['site_domain'].$_CFG['site_dir'],$templates);
	$templates=str_replace('{username}',$_GET['sendusername'],$templates);
	$templates=str_replace('{password}',$_GET['sendpassword'],$templates);
	$templates=str_replace('{newpassword}',$_GET['newpassword'],$templates);
	$templates=str_replace('{personalfullname}',$_GET['personal_fullname'],$templates);
	$templates=str_replace('{jobsname}',$_GET['jobs_name'],$templates);
	$templates=str_replace('{companyname}',$_GET['companyname'],$templates);
	$templates=str_replace('{paymenttpye}',$_GET['paymenttpye'],$templates);
	$templates=str_replace('{amount}',$_GET['amount'],$templates);
	$templates=str_replace('{oid}',$_GET['oid'],$templates);
	$templates=str_replace('{mail_template_img_dir}',$_CFG['site_domain'].$_CFG['site_dir'].'templates/'.$_CFG['template_dir'].'images/email/',$templates);
	$templates=str_replace('{mail_template_dir}',$_CFG['site_domain'].$_CFG['site_dir'].'templates/'.$_CFG['template_dir'].'mail_template/',$templates);
	return $templates;
}
function make_dir($path)
{ 
	if(!file_exists($path))
	{
	make_dir(dirname($path));
	@mkdir($path,0777);
	@chmod($path,0777);
	}
}

/*ZT 2012��9��6�� 09:55:07
 * ��ҳ������ʾ
 */
function get_category_list()
{
	global $db;
	$sql = "select * from ".table('category_jobs')." where parentid=0  order BY category_order desc,id asc";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{		
		$sql = "select * from ".table('category_jobs')." where parentid=".$row['id']."  order BY category_order desc,CONVERT(REPLACE(REPLACE(`stat_jobs`,'(',''),')',''), SIGNED) DESC,id asc LIMIT 5";
		$sub=$db->getall($sql);
		$row['sub']=$sub;
		$category[]=$row;
	}
	return $category;
}
//END

/*ZT 2012��9��7�� 15:38:41
 * ��ҳ����ҵ��ʾ����
 */
function get_industry_list(){
//��ҵ����ǰ����Ƹ��Ϣ
	global $db,$timestamp;

	$option = " WHERE 1 AND (audit=1 OR audit=4) AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
#	$option = " WHERE 1 ";
	$industry = get_category_zt('QS_trade');
#	e($industry);
	
	$list_num = 8;
	$return = array();
	$separate = array();
	foreach ($industry as $v){
		$trade = $v['c_id'];
		$trade_cn = $v['c_name'];
		$sql = " SELECT id,jobs_name, companyname, district_cn, wage_min, wage_max,uid FROM  ".table('jobs')." $option ". (empty($separate)?'':' AND uid NOT IN  ('.implode(',', $separate).')') ." AND trade REGEXP ',$trade,' GROUP BY uid ORDER BY refreshtime DESC LIMIT $list_num ";
		$res = $db->getall($sql);
		$return[$trade]['trade_cn'] = $trade_cn;
		$return[$trade]['display'] = $v['display'];
//		$return[$trade]['url'] = $trade;
		$rewrite = '';
		switch ($trade){
			case 1:
				$rewrite = 'lights';
				break;
			case 2:
				$rewrite = 'solar';
				break;
			case 3:
				$rewrite = 'fiber';
				break;
			case 4:
				$rewrite = 'laser';
				break;
			case 5:
				$rewrite = 'ee';
				break;
			case 6:
				$rewrite = 'gongkong';
				break;
			case 7:
				$rewrite = 'display';
				break;
			case 8:
				$rewrite = 'smartgrids';
				break;
			case 9:
				$rewrite = 'instrument';
				break;
			case 10:
				$rewrite = 'emc';
				break;
			default:
				$rewrite = 'jobs/?trade='.$trade;
				break;
		}
		$return[$trade]['url'] = $_CFG['site_domain'].'/jobs/'.$rewrite;
		
		foreach ($res as $val){
			$return[$trade]['jobs'][] = $val;
			$separate[] = $val['uid'];
		}
	}
	return array_chunk($return,2);
#	e($return);	
}
//END

/*������Ƹ��Ϣ
 *ZT 2012��9��10�� 11:02:50 
 */
function get_recent_joblist($limit = 10){
	global $db,$timestamp;

	$option = " WHERE 1 AND (audit=1 OR audit=4) AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
	$fields = "`id`,`uid`,`jobs_name`,`companyname`,`district_cn`,`education_cn`,`wage_min`,`wage_max`,`refreshtime`";
		
	$sql = "
		SELECT * FROM 
			(SELECT $fields
			FROM hr_jobs $option ORDER BY refreshtime DESC
			LIMIT 200) t
		GROUP BY t.uid
		ORDER BY t.refreshtime DESC
		LIMIT 10
	";
		
#	echo $sql;
#	echo '<br/>';
	return $db->getall($sql);

}


/*��������鿴����
 * ZT 2012��9��17�� 10:34:42
 */
function e(){
	$arg_list = func_get_args();
	if (empty($arg_list)) return;
	foreach ($arg_list as $v){
		echo "<pre style=\"border: 1px solid #000; height: 20em; overflow: auto; margin: 0.5em;\">";
		var_dump($v);
		echo "</pre>";
	}
}

/*	�ж�ĳ����IDΪһ�����໹�Ƕ�������
 * ZT 2012��11��30�� 15:44:55
 */
function ifparent_category($id){
	global $db;
	$sql = "SELECT id,parentid FROM ".table('category_jobs')." WHERE id=".$id;
	$res = $db->getone($sql);
	if ($res['parentid']){
		return false;
	}
	else{
		return true;
	}
}

/*�ؼ��ʹ�������, �����б�ҳ������������
 * ZT 2012��9��18�� 15:44:53
 */
function get_category_related_to_key($keyword){
	global $db;
	//����ؼ���Ϊ��
	$sql = "SELECT * FROM ".table('category_jobs')." WHERE  parentid=0 ORDER BY category_order DESC, CONVERT(REPLACE(REPLACE(`stat_jobs`,'(',''),')',''), SIGNED) DESC";
	if($keyword){
		$sql = "SELECT * FROM ".table('category_jobs')." WHERE  categoryname LIKE '%$keyword%' ORDER BY category_order DESC, CONVERT(REPLACE(REPLACE(`stat_jobs`,'(',''),')',''), SIGNED) DESC";
	}
	return $db->getall($sql);
}
/**����ѡ����࣬��ѯ�˷����ӷ���
 * ZT 2012��9��19�� 10:21:20
 */
function get_category_related_to_category($category){
	global $db;
	$sql = '';
#	$sql = "SELECT * FROM ".table('category_jobs')." WHERE id=$category";
#	$qry = $db->query($sql);
#	$res = $db->fetch_array($qry);
	//��ѯ����Ϊ������,���ѯ�ӷ�������
	//��ѯ����Ϊ�ӷ���,���ѯ����������
	$where = "parentid"."=".$category ;
	$sql = "SELECT * FROM ".table('category_jobs')." WHERE ". $where ." ORDER BY category_order DESC, CONVERT(REPLACE(REPLACE(`stat_jobs`,'(',''),')',''), SIGNED) DESC";
#	echo $sql;
	return $db->getall($sql);
}

/*ָ����������
 * ZT 2012��9��18�� 16:38:50
 */
function get_category_zt($alias)
{
	global $db;
	$sql = "select * from ".table('category')." WHERE c_alias='".$alias."'  ORDER BY c_order DESC,c_id ASC";
	return $db->getall($sql);
}

/* ����ID��ȡ������
 * ZT 2013��2��27�� 17:09:35
 * �ɻ�ȡ�������,ְλ,�����ȱ������
 */
function get_categoryname_by_id($id, $table){
	global $db;
	$return = '';
	$sql = "SELECT categoryname FROM ".table('category_'.$table). " WHERE id=$id";
	$res =  $db->getone($sql);
	$return = $res['categoryname'];
	return $return;
}

/*URLƴ�Ӻ���,��һ������ΪҪ����ı�����,URL��������Ĭ�Ϸָ���&,����д���Ϊ��Ӧ�ָ�����:-
 * ZT 2012��9��20�� 09:33:42
 */
function rebuild_url($variable,$split='&'){
	global $_CFG;
	$rurl = $_SERVER['REQUEST_URI'];
	$rurl = str_replace($_CFG['site_domain'], '', $rurl);
	//У԰��Ƹ����
	if ($rurl == '/campus'){
		$rurl = '/jobs/?school=1';
	}
	//��ҵ
	elseif ($rurl == '/jobs/lights'){
		$rurl = '/jobs/?trade=1';
	}
	elseif ($rurl == '/jobs/solar'){
		$rurl = '/jobs/?trade=2';
	}
	elseif ($rurl == '/jobs/fiber'){
		$rurl = '/jobs/?trade=3';
	}
	elseif ($rurl == '/jobs/laser'){
		$rurl = '/jobs/?trade=4';
	}
	elseif ($rurl == '/jobs/ee'){
		$rurl = '/jobs/?trade=5';
	}
	elseif ($rurl == '/jobs/gongkong'){
		$rurl = '/jobs/?trade=6';
	}
	elseif ($rurl == '/jobs/display' || $rurl == '/jobs/optics'){
		$rurl = '/jobs/?trade=7';
	}
	elseif ($rurl == '/jobs/smartgrids'){
		$rurl = '/jobs/?trade=8';
	}
	elseif ($rurl == '/jobs/instrument'){
		$rurl = '/jobs/?trade=9';
	}
	elseif ($rurl == '/jobs/emc'){
		$rurl = '/jobs/?trade=10';
	}
	//����
	elseif ($rurl == '/beijing'){
		$rurl = '/jobs/?list_areaid=1';
	}
	elseif ($rurl == '/shanghai'){
		$rurl = '/jobs/?list_areaid=2';
	}
	elseif ($rurl == '/guangdong'){
		$rurl = '/jobs/?list_areaid=20';
	}
	elseif ($rurl == '/zhejiang'){
		$rurl = '/jobs/?list_areaid=12';
	}
	elseif ($rurl == '/jiangsu'){
		$rurl = '/jobs/?list_areaid=11';
	}
	elseif ($rurl == '/shandong'){
		$rurl = '/jobs/?list_areaid=16';
	}
	elseif ($rurl == '/fujian'){
		$rurl = '/jobs/?list_areaid=14';
	}
	elseif ($rurl == '/hubei'){
		$rurl = '/jobs/?list_areaid=18';
	}
	elseif ($rurl == '/hunan'){
		$rurl = '/jobs/?list_areaid=19';
	}
	elseif ($rurl == '/shanxi'){
		$rurl = '/jobs/?list_areaid=27';
	}
	
	$return = '';
	$pattern = '';
	if($split == '&'){
		//?a=1&b=2&c=3
		if (($pattern = "/&$variable=\d*/")  && preg_match($pattern, $rurl)){
			$return = preg_replace($pattern, "", $rurl).'&'.$variable.'=';
		}
		elseif (($pattern = "/\?$variable=\d*&/")  && preg_match($pattern, $rurl)){
			$return = preg_replace("/$variable=\d*&/", "", $rurl).'&'.$variable.'=';
		}
		elseif (($pattern = "/\?$variable=\d*$/")  && preg_match($pattern, $rurl)){
			$return = preg_replace("/\d*/", "", $rurl);
		}
		elseif(strpos($rurl, '?') === FALSE){
			$return = '?'.$variable.'=';
		}
		else{
			$return = $rurl.'&'.$variable.'=';
		}
	}
	else{//-a-1-b-2-c-3
		$pattern = "/${split}${variable}${spilt}\d*/";
		if (preg_match($pattern, $rurl)){
			$return = preg_replace($pattern, "", $rurl).$split.$variable.$split;
		}
		else{
			$return = $split.$variable.$split;
		}
	}
	$return = preg_replace('/page=\d*/', 'page=', $return);
	return $return;
}

/**
 * ��ҳ
 * @param int $total ������
 * @param int $pageCNT ÿҳ����
 * @param int $page ��ǰҳ��
 * @param string $url url
 * @return string
 */
function dPage($total, $page, $url, $pageCNT = 50) {
	global $CFG;
		
	//page������Ч��
	$num_max = $total ? ceil($total / $pageCNT) : 1;
	if ($page < 1 || $page > $num_max) {
		stripos($_CFG['cur_url'], '/admin/') ? adminmsg("ҳ�治���ڣ�",0) : showmsg("ҳ�治���ڣ�",0);		
	}
	
	if ($total <= $pageCNT) {#�պ�һҳ
		return '';
	} else {
		$html = '';
		
#		$pageStr = stripos($url,'?') ? '&page=' : '?page=';
		
		//��ҳ��
		$pNum = ceil($total / $pageCNT);
		$prePage = $page - 1;
		$nxtPage = $page + 1;
		$prePageUrl =   "$url$pageStr$prePage";
		$nxtPageUrl = "$url$pageStr$nxtPage";
		
		$html = "<div class='page'>��ǰ��&nbsp;$page&nbsp;ҳ, ��&nbsp;".$pNum."&nbsp;ҳ&nbsp;&nbsp;&nbsp;&nbsp;".$total."����¼</cite></div><div class='page_num'>";
		
		//��ҳ
		if ($page == 1) {
			$html .= " <strong>&nbsp;1&nbsp;</strong> ";
		} else {
			$html .= " <a href=\"$prePageUrl\" title=\"��һҳ\">��һҳ</a> ";
			if ($page > 4 ) {
				$html .= " ... ";
			}
			
			$i = max(1, $page-3);
			for (; $i < $page; $i++) {
				$html .= " <a href=\"$url$pageStr$i\">$i</a> ";
			}
			$html .= " <strong>$i</strong> ";
		}
			
		
		if ($page == $pNum) {
			$html .= '';
		} else {		
			$j = min($pNum, $page+3);
			for ($i = $page+1; $i <= $j; $i++) {
				$html .= " <a href=\"$url$pageStr$i\">$i</a> ";
			}
			if ($j == $pNum) {
				$html .= " <a href=\"$nxtPageUrl\" title=\"��һҳ\">��һҳ</a> ";
			} else {
				$html .= " ... <a href=\"$nxtPageUrl\" title=\"��һҳ\">��һҳ</a> ";
			}
		}
		
		if ($html) {
		#	$html .= " <cite>��".$total."��/".$pNum."ҳ</cite> ";
			$html .= "<input size=4 type=\"text\" onkeydown=\"if(event.keyCode==13 &amp;&amp; this.value) {window.location.href='".$url.$pageStr."{hr_page}'.replace(/\{hr_page\}/, this.value);return false;}\" value=\"\" id=\"hr_pageno\" >";
			$html .= "<input type=\"button\" onclick=\"if($('#hr_pageno').val()&gt;0)window.location.href='".$url.$pageStr."{hr_page}'.replace(/\{hr_page\}/, $('#hr_pageno').val());\" value=\"GO\" >";
		}
		
		return $html.'</div>';
	}
}

/**
 * ��ҳ
 * @param int $total ������
 * @param int $pageCNT ÿҳ����
 * @param int $page ��ǰҳ��
 * @param string $url url
 * @return string
 */
function dPage_2($total, $page, $url, $pageCNT = 50) {
	global $_CFG;
	
	//page������Ч��
	$num_max = $total ? ceil($total / $pageCNT) : 1;
	if ($page < 1 || $page > $num_max) {
		stripos($_CFG['cur_url'], '/admin/') ? adminmsg("ҳ�治���ڣ�",0) : showmsg("ҳ�治���ڣ�",0);		
	}
		
	if ($total <= $pageCNT) {#�պ�һҳ
		return '';
	} else {
		$html = '';
		
		$pageStr = stripos($url,'?') ? '&page=' : '?page=';
		
		//��ҳ��
		$pNum = ceil($total / $pageCNT);
		$prePage = $page - 1;
		$nxtPage = $page + 1;
		$prePageUrl =   "$url$pageStr$prePage";
		$nxtPageUrl = "$url$pageStr$nxtPage";
		
		$html = "";
		
		//��ҳ
		if ($page == 1) {
			$html .= " <strong>&nbsp;1&nbsp;</strong> ";
		} else {
			$html .= " <a href=\"$prePageUrl\" title=\"��һҳ\">��һҳ</a> ";
			if ($page > 4 ) {
				$html .= " ... ";
			}
			
			$i = max(1, $page-3);
			for (; $i < $page; $i++) {
				$html .= " <a href=\"$url$pageStr$i\">&nbsp;$i&nbsp;</a> ";
			}
			$html .= " <strong>$i</strong> ";
		}
			
		
		if ($page == $pNum) {
			$html .= '';
		} else {		
			$j = min($pNum, $page+3);
			for ($i = $page+1; $i <= $j; $i++) {
				$html .= " <a href=\"$url$pageStr$i\">$i</a> ";
			}
			if ($j == $pNum) {
				$html .= " <a href=\"$nxtPageUrl\" title=\"��һҳ\">��һҳ</a> ";
			} else {
				$html .= " ... <a href=\"$nxtPageUrl\" title=\"��һҳ\">��һҳ</a> ";
			}
		}
		
		if ($html) {
			$html .= " <cite>��".$total."��/".$pNum."ҳ</cite> ";
		}
		
		if ($html) {
		#	$html .= " <cite>��".$total."��/".$pNum."ҳ</cite> ";
			$html .= "<input size=4 type=\"text\" onkeydown=\"if(event.keyCode==13 &amp;&amp; this.value) {window.location.href='".$url.$pageStr."{hr_page}'.replace(/\{hr_page\}/, this.value);return false;}\" value=\"\" id=\"hr_pageno\" >";
			$html .= "<input type=\"button\" onclick=\"if($('#hr_pageno').val()&gt;0)window.location.href='".$url.$pageStr."{hr_page}'.replace(/\{hr_page\}/, $('#hr_pageno').val());\" value=\"GO\" >";
		}
		
		return $html;
	}
}

/**��ȡһ������,$tablename��Ϊcategory_jobs category_district
 * ZT 2012��9��26�� 09:58:14
 */
function get_parent_cagegory($tablename){
	global $db;
	$sql = "select * from ".table($tablename)." where parentid=0  order BY category_order desc, id asc";
	return $db->getall($sql);
}
/**��ȡָ����������ӷ���
 * ZT 2012��9��26�� 10:02:32
 */
function get_subcategory($tablename,$parentid){
	global $db;
	$sql = "select * from ".table($tablename)." where parentid=$parentid  order BY category_order desc, id asc";
	return $db->getall($sql);	
}


function z_get_category_jobs() {
	global $db;
	$sql = "SELECT * FROM hr_category_jobs";
	return $db->getall($sql);
}

function z_get_category_speciality() {
	global $db;
	$sql = "SELECT * FROM hr_category_speciality";
	return $db->getall($sql);
}


/**��ȡѡ������ĿHTML OPTIONS
 * ZT 2012��10��22�� 15:32:55
 * $param ,����, �����ID��
 */

function get_options_html($tablename,$ids){
	global $db;
	$res = '';
	$id_arr = explode(',', $ids);
	foreach ($id_arr as $v){
		if ($v == '') continue;
		if ($tablename == 'category'){
			$sql = "SELECT c_id,c_name FROM ".table('category')." WHERE c_id=".$v;
			$tmp = $db->getone($sql);
			$res .= '<option value="'.$tmp['c_id'].'">'.$tmp['c_name'].'</option>';
		}
		else{
			$sql = "SELECT id,categoryname FROM ".table($tablename)." WHERE id=".$v;
			$tmp = $db->getone($sql);
			$res .= '<option value="'.$tmp['id'].'">'.$tmp['categoryname'].'</option>';
		}
	}
	return $res;
}

/*
 * ��ȡ�־ӵ�
 * 2013��2��21�� 09:59:37
 */
function get_now_address($province,$city){
	static $res_dis,$res_sdis;
	$return = '';
	if($province <= 0) return $return;
	if(empty($res_dis)){
		$res_dis = get_parent_cagegory('category_district');
	}
	if(empty($res_sdis)){
		$res_sdis = get_subcategory('category_district',$province);
	}
//��������
	//����ʡ
	$select_dis = array();
	foreach ($res_dis as $v){
		$select_dis[$v['id']] = $v['categoryname'];
	}
	$return = $select_dis[$province];
	
	if ($city > 0){
		//������
		$select_sdis = array();
		foreach ($res_sdis as $v){
			$select_sdis[$v['id']] = $v['categoryname'];
		}
		$return .= '/'.$select_sdis[$city];
	}
//END		
	return $return;
}

/**��ȡ������Ϣ
 * ZT2012��11��8�� 15:18:38
 * $type, 1:����Ԥ��, 2:��ҵδ����Ԥ��,3:��ҵ������Ԥ��
 */
function assing_resume($uid,$type, $send=false){
	global $db,$smarty;
	
	if ($uid == '') return false;
	$add = '';
	if ($type == 2 || $type == 3){
		$add = ' AND status = 1';
	}
	else {
		$add = ' AND status = 0';
	}
	
	$sql = "SELECT *,(year(from_unixtime(unix_timestamp()-`birthdate`))-1970) AS ageyear FROM ".table('resume')." WHERE uid=".$uid;
	$res = $db->getone($sql);
	if ($res['complete'] <= 3){
		return -1;//����δ���
	}
	if (empty($res)){
		return -1;//����������
	}
	
	if ($res['otherlang']){
		list($res['otherlang'],$res['otherlang_degree']) = explode(':', $res['otherlang']);
	}
#	e($res);
	$res['birthdate'] = date('Y',$res['birthdate']);
	if($res['address'] == 'null') $res['address'] = '';
	
//��������
	//����ʡ
	$select_dis = array();
	$res_dis = get_parent_cagegory('category_district');
	foreach ($res_dis as $v){
		$select_dis[$v['id']] = $v['categoryname'];
	}
	$res['cencus'] = $select_dis[$res['cencus_province']];
	
	if ($res['cencus_city'] > 0){
		//������
		$select_sdis = array();
		$res_sdis = get_subcategory('category_district',$res['cencus_province']);
		foreach ($res_sdis as $v){
			$select_sdis[$v['id']] = $v['categoryname'];
		}
		$res['cencus'] .= '/'.$select_sdis[$res['cencus_city']];
	}
//END		
//�־ӵش���
	//����ʡ
	$select_dis2 = array();
	$res_dis2 = get_parent_cagegory('category_district');
	foreach ($res_dis2 as $v){
		$select_dis2[$v['id']] = $v['categoryname'];
	}
	$res['home'] = $select_dis2[$res['home_province']];
	
	if ($res['home_city'] > 0){
		//������
		$select_sdis2 = array();
		$res_sdis2 = get_subcategory('category_district',$res['home_province']);
		foreach ($res_sdis2 as $v){
			$select_sdis2[$v['id']] = $v['categoryname'];
		}
		$res['home'] .= '/'.$select_sdis2[$res['home_city']];
	}
//END
	//$res['telephone'] = preg_replace('/(\d{3})(\d{4})(\d{4})/', '\\1-\\2-\\3', $res['telephone']);
	$name = cut_str($res['fullname'],1);
	if ($type == 2 ){
		$res['fullname'] = $name.(($res['sex']==1)?'����':'Ůʿ');
	}
	$smarty->assign('user_info', $res);
	//END
	
	//��������
	$sql = "SELECT * FROM ".table('resume_education')." WHERE 1 $add AND uid=".$uid;
	$res = $db->getone($sql);
	if ($type != 2 && $type != 3 && empty($res)){
		$sql = "SELECT * FROM ".table('resume_education')." WHERE 1  AND status = 1 AND uid=".$uid;
		$res = $db->getone($sql);
	}
	if (empty($res)){
		return -2;//����������
	}
	if (!empty($res)){
		preg_match('/(\d+).*(\d+).*/', $res['start'],$m);
		$res['sdate1'] = $m[1];
		$res['sdate2'] = $m[2];
		preg_match('/(\d+).*(\d+).*/', $res['endtime'],$m);
		$res['sdate3'] = $m[1];
		$res['sdate4'] = $m[2];
//		$res['content'] = cut_str($res['content'], 200);
	}
	$smarty->assign('user_edu', $res);
	//END
	
	//��������
	$sql = "SELECT * FROM ".table('resume_work')." WHERE 1 $add AND uid=".$uid;
	$res = $db->getall($sql);
	if ($type != 2 && $type != 3 && empty($res)){
		$sql = "SELECT * FROM ".table('resume_work')." WHERE 1  AND status = 1 AND uid=".$uid;
		$res = $db->getall($sql);
	}
	if (empty($res)){
		return -3;//����������
	}
	if (!empty($res)){
		$multia = array();$multib = array();
		foreach ($res as $k=>$v){
			preg_match('/(\d+).*(\d+).*/', $res[$k]['start'],$m);
			$res[$k]['edate1'] = $m[1];
			$res[$k]['edate2'] = $m[2];
			$multia[$k] = $m[1];
			$multib[$k] = $m[2];
			$multic[$k] = $res[$k]['work_status'];
			preg_match('/(\d+).*(\d+).*/', $res[$k]['endtime'],$m);
			$res[$k]['edate3'] = $m[1];
			$res[$k]['edate4'] = $m[2];
//			$res[$k]['achievements'] = cut_str($res[$k]['achievements'], 200);
		}
		array_multisort($multic, SORT_NUMERIC,SORT_DESC,$multia, SORT_NUMERIC, SORT_DESC,$multib, SORT_NUMERIC, SORT_ASC,$res);
		$smarty->assign('user_work',$res);
	}
	
	//����
	$sql = "SELECT * FROM ".table('resume_file')." WHERE `type`!=1 AND uid=".$uid;
	$res = $db->getall($sql);
	if (!empty($res)){
		$smarty->assign('user_file', $res);	
	}
	
	//��ѯͷ������
	$sql = "SELECT * FROM ".table('resume_file')." WHERE type=1 AND uid=".$uid." ORDER BY upload_time DESC LIMIT 1";
	$res = $db->getone($sql);
	if (!empty($res)){
		$smarty->assign('personal_img', $res['path']);
	}
	//END
	
	//��������
	$sql = "SELECT * FROM ".table('resume_intention')." WHERE uid=".$uid;
	$res = $db->getone($sql);
	$res['industry'] = trim($res['industry'], ',');
	//�����λ�账��category
	if (empty($res)){
		return -4;//����������
	}
	$sql = "SELECT categoryname FROM ".table('category_jobs')." WHERE id IN (".$res['category'].")";
	$r1 = $db->getall($sql);
	$res['intention_category'] = implode(',', array_map(create_function('$v', 'return $v["categoryname"];'), $r1));
	//��������账��
	$sql = "SELECT categoryname FROM ".table('category_district')." WHERE id IN (".$res['district'].")";
	$r1 = $db->getall($sql);
	$res['intention_district'] = implode(',', array_map(create_function('$v', 'return $v["categoryname"];'), $r1));
	
	//������ҵ�账��industry
	$sql = "SELECT c_name FROM ".table('category')." WHERE c_id IN (".$res['industry'].")";
	$r1 = $db->getall($sql);
	$res['intention_industry'] = implode(',', array_map(create_function('$v', 'return $v["c_name"];'), $r1));
	
//	$res['specialty'] = cut_str($res['specialty'], 200);
//	$res['specialty_tmp'] = cut_str($res['specialty_tmp'], 200);
//	$res['self_evaluation'] = cut_str($res['self_evaluation'], 200);
//	$res['self_evaluation_tmp'] = cut_str($res['self_evaluation_tmp'], 200);
	$smarty->assign('user_intention', $res);
	//END
	
	//����ID
	$sql = "SELECT * FROM ".table('members')." WHERE uid=".$uid;
	$res = $db->getone($sql);
	$smarty->assign('reg_time', $res['reg_time']);
	//END
	
	$smarty->assign('type', $type);
	$smarty->assign('send', $send);
	
	return true;
}

//memberͳ�Ƽ�1
function z_member_num_add($uid, $key) {
	global $db;
	$db->query("UPDATE ".table('members')." SET `$key`=`$key`+1 WHERE uid='$uid' LIMIT 1");
}

//memberͳ�Ƽ�1
function z_member_num_minus($uid, $key) {
	global $db;
	$member = $db->getone("SELECT $key FROM ".table('members')." WHERE  uid='$uid'");
	if ($member[$key] > 0) {
		$db->query("UPDATE ".table('members')." SET `$key`=`$key`-1 WHERE uid='$uid' LIMIT 1");
	}
}

//���ؼ���
function z_down($company, $resume) {
	$data['resume_id'] = $resume['id'];
	$data['resume_name'] = $resume['title'];
	$data['resume_uid'] = $resume['uid'];
	$data['company_uid'] = $company['uid'];
	$data['company_name'] = $company['companyname'];
	$data['down_addtime'] = time();	
	inserttable(table('company_down_resume'),$data);	
	//ͳ��
	z_member_num_add($data['company_uid'], 'c_down_num');
}

//ɾ���ղ�
function z_favorites_del($company_uid, $idarr) {
	global $db;
	$db->query("DELETE FROM hr_company_favorites WHERE company_uid=$company_uid AND resume_id IN(".implode(',', $idarr).")");
}
//����֪ͨ
function z_interview($data) {
	inserttable(table('company_interview'),$data);
	//ͳ��
	z_member_num_add($data['company_uid'], 'c_interview_num');
	z_member_num_add($data['resume_uid'], 'p_interview_num');
}

//�Ƿ������� type:1�û�ID��2����ID
function z_check_downed($company_uid, $uid, $type=1) {
	global $db;
	if ($type == 2) {
		return $db->get_total("SELECT COUNT(*) as num FROM hr_company_down_resume WHERE company_uid=$company_uid AND resume_id=$uid");
	} else {
		return $db->get_total("SELECT COUNT(*) as num FROM hr_company_down_resume WHERE company_uid=$company_uid AND resume_uid=$uid");
	}
}

//����
function z_resume($uid, $type=1) {
	global $db;
	if ($type == 2) {
		return $db->getone("SELECT * FROM hr_resume WHERE id=$uid");
	} else {
		return $db->getone("SELECT * FROM hr_resume WHERE uid=$uid");
	}
}

//ְλ
function z_jobs($jobid, $uid) {
	global $db;
	if ($uid) {
		return $db->getone("SELECT j.*,jc.contact,jc.telephone,jc.address,jc.email 
			FROM hr_jobs j
			LEFT JOIN hr_jobs_contact jc ON jc.pid=j.id
			WHERE j.id=$jobid AND j.uid=$uid");
	} else {
		return $db->getone("SELECT j.*,jc.contact,jc.telephone,jc.address,jc.email 
			FROM hr_jobs j
			LEFT JOIN hr_jobs_contact jc ON jc.pid=j.id
			WHERE j.id=$jobid");
	}
}

//δ��˼�������ְλ
function z_jobs_apply_audit2($uid) {
	global $db;	
	return $db->getall("SELECT * FROM hr_personal_jobs_apply WHERE personal_uid=$uid AND audit=2");
}

//�Ƿ�����
function z_jobs_apply_exist($uid, $company_uid) {
	global $db;
	return $db->get_total("SELECT COUNT(*) AS num FROM hr_personal_jobs_apply WHERE personal_uid=$uid AND company_uid=$company_uid AND audit=1");
}

//�û��Ƿ����
function z_member_exist($username, $type=2) {
	global $db;
	return $db->get_total("SELECT COUNT(*) AS num FROM hr_members WHERE utype='$type' AND username='$username'");
}

//member��
function z_member($uid) {
	global $db;
	return $db->getone("SELECT m.*,mi.realname,mi.sex,mi.birthday,mi.addresses,mi.phone 
		FROM hr_members m
		LEFT JOIN hr_members_info mi ON m.uid=mi.uid
		WHERE m.uid=$uid");
}

function z_member_username($username, $type=1) {
	global $db;
	return $db->getone("SELECT * FROM hr_members WHERE utype='$type' AND username='$username'");
}

//��ҵ
function z_company($uid) {
	global $db;
	return $db->getone("SELECT * FROM hr_company_profile WHERE uid=$uid");
}

//��˸����û�
function z_admin_personal_audit($uidArr, $audit) {
	global $db;
	
	//ͨ��
	if ($audit == 1) {
		foreach ($uidArr as $uid) {
			$resume = z_resume($uid);
			//������
			if($resume['complete'] < 4) {
				continue;
			}
			
			zt_personal_audit($uid);
			$db->query("UPDATE hr_resume SET audit=$audit WHERE uid=$uid");
			
			
			//����ְλ�����ʼ�			
			//+send email ����ְλ
			$resume = z_resume($uid);
			$apply_list = z_jobs_apply_audit2($uid);
			
			if (!empty($apply_list)) {
				foreach ($apply_list as $value) {
					
					updatetable('hr_personal_jobs_apply', array('audit'=>1), array('did'=>$value['did']));
					
					$jobsInfo = z_jobs($value['jobs_id']);
					$mailArr = array(
						'to'=>$jobsInfo['email'],
						'from'=>$resume['email'],
						'fromName'=>$resume['fullname']
					);
					$data = array(
						'fullname'=>$resume['fullname'],
						'jobs_name'=>$jobsInfo['jobs_name']
					);
					z_mail('resume', $mailArr, $data, $uid);
				};
			}
			//end
			
		}
	} else {
		return $db->query("UPDATE hr_resume SET audit=$audit WHERE uid IN(".implode(',', $uidArr).")");
	}
}

//�޸Ĺ�˾��
function z_change_companyname($uid, $companyname) {
	global $db;
	$db->query("UPDATE hr_company_profile SET companyname='$companyname' WHERE uid='$uid'");
	$db->query("UPDATE hr_jobs SET companyname='$companyname' WHERE uid='$uid'");
}

//�����ҵ�û�
function z_admin_company_audit($uidArr, $audit) {
	global $db;
	if (!is_array($uidArr)) {
		$uidArr = array($uidArr);
	}
	if ($audit == 1 || $audit == 3) {
		$db->query("UPDATE hr_company_profile SET `contents`=`contents_tmp`,`contents_tmp`='' WHERE uid IN(".implode(',', $uidArr).") AND contents_tmp!=''");
		$db->query("UPDATE hr_company_profile SET `address`=`address_tmp`,`address_tmp`='' WHERE uid IN(".implode(',', $uidArr).") AND address_tmp!=''");
		$db->query("UPDATE hr_company_profile SET `contact`=`contact_tmp`,`contact_tmp`='' WHERE uid IN(".implode(',', $uidArr).") AND contact_tmp!=''");
		$db->query("UPDATE hr_company_profile SET `telephone`=`telephone_tmp`,`telephone_tmp`='' WHERE uid IN(".implode(',', $uidArr).") AND telephone_tmp!=''");
		$db->query("UPDATE hr_company_profile SET `website`=`website_tmp`,`website_tmp`='' WHERE uid IN(".implode(',', $uidArr).") AND website_tmp!=''");
	}
	$db->query("UPDATE hr_jobs SET company_audit=$audit WHERE uid IN(".implode(',', $uidArr).")");
	return $db->query("UPDATE hr_company_profile SET audit=$audit WHERE uid IN(".implode(',', $uidArr).")");
}

//���ְλ
function z_admin_job_audit($jobidArr, $audit) {
	global $db;
	if (!is_array($jobidArr)) {
		$jobidArr = array($jobidArr);
	}
	if ($audit == 1 || $audit == 3) {
		$db->query("UPDATE hr_jobs SET `contents`=`contents_tmp`,`contents_tmp`='' WHERE id IN(".implode(',', $jobidArr).") AND contents_tmp!=''");
	}
	return $db->query("UPDATE hr_jobs SET audit=$audit WHERE id IN(".implode(',', $jobidArr).")");
}

//ˢ��ְλ
function z_admin_job_refresh($jobidArr) {
	global $db;
	$time = time();
	return $db->query("UPDATE hr_jobs SET refreshtime=$time WHERE id IN(".implode(',', $jobidArr).")");
}

//ɾ�������û�
function z_admin_personal_del($uidArr) {
	global $db;
	$db->query("DELETE FROM hr_members WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_members_info WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_members_points WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_members_setmeal WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_resume WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_resume_education WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_resume_file WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_resume_intention WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_resume_work WHERE uid IN(".implode(',', $uidArr).")");
}

//ɾ����ҵ�û�
function z_admin_company_del($uidArr) {
	global $db;
	$db->query("DELETE FROM hr_members WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_members_info WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_members_points WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_members_setmeal WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_company_profile WHERE uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_company_favorites WHERE company_uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_company_down_resume WHERE company_uid IN(".implode(',', $uidArr).")");
	$db->query("DELETE FROM hr_jobs WHERE uid IN(".implode(',', $uidArr).")");
	//$db->query("DELETE FROM hr_jobs_contact uid IN(".implode(',', $uidArr).")");
}

//ɾ��ְλ
function z_admin_job_del($jobidArr) {
	global $db;
	$db->query("DELETE FROM hr_jobs WHERE id IN(".implode(',', $jobidArr).")");
}

//��ת
function z_goto($url) {
	header("location: $url");die;
}

//����ֵ
function z_config_value($key) {
	global $db;
	$r = $db->getone("SELECT `value` FROM hr_config WHERE name='$key'");
	return $r['value'];
}

//����������ѻ�Ա
function z_month_freesetmeal_num() {
	global $db;
	$time_start = strtotime(date('Y-m-01 00:00:01', time()));
	$total = $db->get_total("SELECT COUNT(*) AS num FROM hr_members_setmeal WHERE setmeal_id=2 AND starttime>=$time_start");
	return $total;
}

//���Ͷ�����ֵ
function z_queue_combination($arr) {
	$int = 0;
	if (empty($arr)) {
		return 0;
	}
	foreach ($arr as $value) {
		$int += intval($value);
	}
	return $int;
}

//���Ͷ�����ֵ
function z_queue_combination_cn($arr) {
	$cn = '';
	foreach ($arr as $value) {
		switch ($value) {
			case 1: $cn[] = 'ȫְ'; break;
			case 2: $cn[] = '��ְ'; break;
			case 4: $cn[] = 'Ӧ����'; break;
			case 8: $cn[] = 'ʵϰ'; break;
		}
	}
	return implode('/', $cn);
}

//���Ͷ�����ֵ�ֽ�
function z_queue_resolve($int, $type = '') {
	$int = intval($int);
	$arr = array();
	if (empty($int)) {
		return ;
	}
	
	$v = z_queue_resolve_v($int);
	$t = ($int - $v);
	
	$arr[] = $v;
	
	while ($t > 0) {
		$temp = z_queue_resolve_v($t);
		$temp && $arr[] = $temp;
		$t = ($t - $temp);
	}
	
	$arr = array_reverse($arr);
	
	if ($type == 'show') {
		$temp_arr = array();
		foreach ($arr as $value) {
			$temp_arr[$value] = 1;
		}
		$arr = $temp_arr;
	}
	
	return $arr;
}

function z_queue_resolve_v($int) {
	$int = intval($int);
	$pInt = 1;
	
	while ($pInt <= $int) {
		$pInt = $pInt * 2;
	}
	
	$pInt = $pInt == 1 ? 1 : $pInt / 2;
	return $pInt;
}

//�ײ�
function z_config_setmeal($id) {
	global $db;
	$setmeal = $db->getone("SELECT * FROM hr_setmeal WHERE id='$id'");
	return $setmeal;
}

//��˾�ײ�
function z_setmeal($uid) {
	global $db;
	$setmeal = $db->getone("SELECT * FROM hr_members_setmeal WHERE uid='$uid' AND  effective=1 LIMIT 1");
	return $setmeal;
}

/* ��˼���
 * 2012��11��20�� 11:24:19
 */
function zt_personal_audit($id){
	global $db;
	$sql = "SELECT audit FROM ".table('resume')." WHERE uid=".$id;
	$res = $db->getone($sql);
	$resume_status = $res['audit'];
	
	//ɾ��ԭʼ����
	$sql = "SELECT id FROM ".table('resume_education')." WHERE uid=".$id." AND status=0";
	$res = $db->getall($sql);
	if (!empty($res)){	
		$db->query("DELETE FROM ".table('resume_education')." WHERE uid=".$id." AND status=1");
	}
	$sql = "SELECT id FROM ".table('resume_work')." WHERE uid=".$id." AND status=0";
	$res = $db->getall($sql);
	if (!empty($res)){	
		$db->query("DELETE FROM ".table('resume_work')." WHERE uid=".$id." AND status=1");
	}
	//�޸ı�������״̬
	updatetable(table('resume_education'), array('status'=>'1'), " status=0 AND uid=".$id);
	updatetable(table('resume_work'), array('status'=>'1'), " status=0 AND uid=".$id);
	
	//��������ݸ���
	$db->query("UPDATE ".table('resume_intention')." SET `self_evaluation`=`self_evaluation_tmp`, `specialty`=`specialty_tmp` WHERE uid=".$id);
	
	//�޸ļ���״̬
	updatetable(table('resume'), array('audit'=>'1'), " uid=".$id);
}

//���ְλ
function z_job_audit($jobid, $audit) {
	global $db;
	updatetable(table('jobs'), array('audit'=>$audit), array('id'=>$jobid));
}

//��Ѷ
function z_article($id) {
	global $db;
	$article = $db->getone("SELECT * FROM hr_article WHERE is_display=1 AND id=$id");
	if ($article) {
		$article['addtime_cn'] = date('Y-m-d', $article['addtime']);
	}
	return $article;
}

//��Ѷ�б�
function z_article_list($type, $num = 5, $idarr = array()) {
	global $db;
	
	$addsql = '';
	if (!empty($idarr)) {
		$addsql .= ' AND id NOT IN('.implode(',', $idarr).')';
	}
	$list = $db->getall("SELECT id,title,addtime FROM hr_article WHERE is_display=1 AND type_id=$type $addsql ORDER BY addtime DESC LIMIT $num");
	foreach ($list as $key=>$value) {
		$list[$key]['addtime_cn'] = date('Y-m-d', $value['addtime']);
	}
	return $list;
}

//��Ѷ��ͼƬ��ͷ��
function z_article_pic($type) {
	global $db;
	return $db->getone("SELECT * FROM hr_article WHERE is_display=1 AND type_id=$type AND Small_img !='' AND focos=4 ORDER BY addtime DESC");
}

//��Ѷ����
function z_article_type($id) {
	global $db;
	return $db->getone("SELECT * FROM hr_article_category WHERE id=$id");
}

//�Ƽ�
function z_article_recommend($type, $num = 8) {
	global $db;
	return $db->getall("SELECT id,title,addtime FROM hr_article WHERE is_display=1 AND type_id=$type AND focos=4 ORDER BY addtime DESC LIMIT $num");
}

//������Ѷ
function z_article_new($num = 10) {
	global $db;
	return $db->getall("SELECT id,title,addtime FROM hr_article WHERE is_display=1 ORDER BY addtime DESC LIMIT $num");
}

//רҵ��id
function z_speciality_category_parentid($ids) {
	$pidArr = array();
	if (!is_array($ids)) {
		$ids = array($ids);
	}
	foreach ($ids as $value) {
		$c = z_category_speciality($value);
		$pidArr[] = $c['parentid'] ? $c['parentid'] : $value;
	}
	$pidArr = array_unique($pidArr);
	return empty($pidArr) ? '' : ",".implode(",", $pidArr).",";
}

//רҵ����
function z_category_speciality($id) {
	global $db;
	return $db->getone("SELECT * FROM hr_category_speciality WHERE id=$id");
}

//������λ��id
function z_job_category_parentid($ids) {
	$pidArr = array();
	if (!is_array($ids)) {
		$ids = array($ids);
	}
	foreach ($ids as $value) {
		$c = z_category_jobs($value);
		$pidArr[] = $c['parentid'] ? $c['parentid'] : $value;
	}
	$pidArr = array_unique($pidArr);
	return empty($pidArr) ? '' : ",".implode(",", $pidArr).",";
}

//ְλ����
function z_category_jobs($id) {
	global $db;
	return $db->getone("SELECT * FROM hr_category_jobs WHERE id=$id");
}

//ְλ�����б�
function z_category_jobs_list() {
	global $db;
	$arr = $db->getall("SELECT * FROM hr_category_jobs");
	foreach ($arr as $key=>$value) {
		$list[$value['id']] = $value;
	}
	return $list;
}


//����
function z_feedback($id, $uid = 0) {
	global $db;
	if ($uid) {
		return $db->getone("SELECT * FROM hr_feedback WHERE id=$id AND uid=$uid");
	} else {
		return $db->getone("SELECT * FROM hr_feedback WHERE id=$id");
	}
}

//ʡ��
function z_province() {
	global $db;
	return $db->getall("SELECT id,categoryname FROM hr_category_district WHERE parentid=0");
}

//����
function z_city($proviceid) {
	global $db;
	return $db->getall("SELECT id,categoryname FROM hr_category_district WHERE parentid=$proviceid");
}

//��ҵ�Ƿ����
function z_company_expire($uid) {
	$setmeal = z_setmeal($uid);
	return time() < $setmeal['endtime'] ? 0 : 1;
}

//δ����Ϣ
function z_message_unread_num($uid) {
	global $db;
	return $db->get_total("SELECT COUNT(*) num FROM hr_feedback WHERE uid=$uid AND infotype=9 AND readed=0");
}

//н��
function z_wage_cn($min, $max) {
	if ($min == 0 && $max == 0) {
		return '����';
	} elseif ($min && $max == 0) {
		return $min.'����';
	} elseif ($min == 0 && $max) {
		return 'С��'.$max;
	} else {
		return $min.'-'.$max;
	}
}

//����id���ɷ�������
function z_category_cn($ids) {
	$cateList = z_category_jobs_list();
	$nameList = array();
	$ids = trim($ids, ',');
	$arr = explode(',', $ids);
	if (!empty($arr)) {
		foreach ($arr as $value) {
			$nameList[] = $cateList[$value]['categoryname'];
		}
	}
	return implode(',', $nameList);
}

//�ղؼ���
function z_favorite_resume($uid) {
	global $db;
	$myid = $_SESSION['uid'];
	
	if ($myid) {
		$resume = z_resume($uid);
		if (!empty($resume)) {
			//�Ƿ��ղؼ���
			$favorited = $db->get_total("SELECT COUNT(*) num FROM hr_company_favorites WHERE resume_id='".$resume['id']."' AND company_uid='$myid'");
			if (!$favorited) {
				$data['resume_id'] = $resume['id'];
				$data['company_uid'] = $myid;
				$data['favoritesa_ddtime'] = time();
				inserttable(table('company_favorites'),$data);
				return 1;
			} else {
				return 2;//���ղ�
			}
		} else {
			return -2;
		}
	} else {
		return -1;
	}
}

//��ȡ����ַ���
function z_randstr($length=6) {
	$hash='';
	$chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
	$max=strlen($chars)-1;   
	mt_srand((double)microtime()*1000000);   
	for($i=0;$i<$length;$i++)   {   
		$hash.=$chars[mt_rand(0,$max)];   
	}   
	return $hash;   
}

//
function get_category_district_zt($pid='0')
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('category_district')." where parentid={$pid}  order BY category_order desc,id asc";
	return   $db->getall($sql);
}

/*
 * �û��ϴ��ļ�����
 * HTML��ǩname, ext�ļ���׺,type�Ƿ񸽼�ҳ�ϴ�,0����������ָ������
 */
function file_upload($input_name, $uid, $ext, $type=0,$size="2000"){
	global $db;
	//ID, UID, resume_id, �ļ���,�ļ�����,�ļ�·��,�ļ���С,�ļ���ʽ,�ϴ�ʱ��
	$post['uid'] = $uid;
	$sql = "SELECT id FROM ".table('resume')." WHERE uid=".$uid;
	$res = $db->getone($sql);
	$resume_id = $res['id'];
	$post['resume_id'] = $resume_id;
	if ($type){
		$post['type'] = $type;
		if ($type == 1){
			$post['name'] = '������Ƭ';
		}
		else{
			$post['name'] = '��������';
		}
	}
	else{
		$post['name'] = $_POST['name'];
		$post['type'] = $_POST['type'];
	}
	$post['upload_time'] = time();

	//�����ϴ���ͼƬp(photo),����photo(0,1),photo_img·��
	if (!empty($_FILES[$input_name]['name'])){
		require_once(QISHI_ROOT_PATH.'include/upload.php');
		$photo_dir= QISHI_ROOT_PATH."data/file/".date("Y/m/d/");
		make_dir($photo_dir);
		$filename =_asUpFiles($photo_dir, $input_name,$size,$ext,true);
		$post['path'] = date("Y/m/d/").$filename;
		$post['size'] = $_FILES[$input_name]['size'];
		$post['ext'] = strtolower(str_replace(".","",strrchr($post['path'], ".")));
	}
	inserttable(table('resume_file'), $post);
	return $post['path'];
}
?>
