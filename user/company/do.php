<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"recruitment");

$uid = $_SESSION['uid'];
$optype = $_POST['optype'];

//�ղ��������ؼ���
if ($optype == 'down_favorites') {
	
	//Ȩ���ж�
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		$link[0]['text'] = "����";
		$link[0]['href'] = 'company_index.php';
		showmsg("��Ա�ѹ��ڣ����ܽ��иò���������ϵ�ͷ���0755-83279360",0,$link);
	}
	
	$idarr = $_POST['idarr'];#����ID
	
	//δѡ��
	empty($idarr) && showmsg("��ѡ�����", 0);
	
	//�û��ײ�
	$member = z_member($uid);
	$setmeal = z_setmeal($uid);
	$down_num = $member['c_down_num'];
	$down_num_max = $setmeal['download_resume_ordinary'];
	if ($down_num >= $down_num_max) {
		showmsg("�����ؼ�����Ŀ����",0);
	}
	
	$ok_cnt = 0;
	$re_cnt = 0;
	$fail_cnt = 0;
	
	//ѭ������
	foreach ($idarr as $resume_id) {
		//�ж��Ƿ�������
		$downed = z_check_downed($uid, $resume_id, 2);
		if ($downed) {
			$re_cnt ++;
			continue;
		}
		
		/*++����������++*/
		if ($down_num < $down_num_max) {
			//����
			$company = z_company($uid);
			$resume = z_resume($resume_id, 2);
			if (!empty($company) && !empty($resume)) {
				z_down($company, $resume);
			}
			$down_num ++;
			$ok_cnt ++;
			
			z_member_num_add($uid, 'c_down_num');
		} else {
			break;
		}		
	}
	$fail_cnt = count($idarr) - ($ok_cnt + $re_cnt);
	
	if ($ok_cnt) {
		$type = 2;
		$msg = "�ɹ������� $ok_cnt ������";
	}
	
	if ($re_cnt) {
		if($type != 2) $type = 1;
		$msg = $msg ? $msg.", $re_cnt �������Ѿ����ز���Ҫ�ظ�����" : "$re_cnt �������Ѿ����ز���Ҫ�ظ�����";
	}

	if ($fail_cnt) {
		if($type >=1) {$type = 1;}
		else{$type = 0;}
		$msg = $msg ? $msg.", $fail_cnt �����س���" : "$fail_cnt �����س���";
	}
	
	$link[0]['text'] = "�����ղؼ�";
	$link[0]['href'] = 'company_recruitment.php?act=favorites_list';
	showmsg("�ɹ������� $ok_cnt ��������",2,$link);
}

//�ղ�ɾ��
elseif ($optype == 'del_favorites') {
	$idarr = $_POST['idarr'];#����ID
	
	//δѡ��
	empty($idarr) && showmsg("��ѡ�����", 0);
	
	//ɾ��
	z_favorites_del($uid, $idarr);
	
	$link[0]['text'] = "�����ղؼ�";
	$link[0]['href'] = 'company_recruitment.php?act=favorites_list';
	showmsg("ɾ���ɹ�",2,$link);
}


$optype = $_GET['optype'];

//�����б��ղ�
if ($optype == 'search_favorite') {
	$ids = $_GET['ids'];
	if (empty($ids)) {
		showmsg("��ѡ�����",0);
	}
	
	$idarr = explode(',', $ids);
	
	$ok_cnt = 0;
	$re_cnt = 0;
	foreach ($idarr as $value) {
		$r = z_favorite_resume($value);
		switch ($r) {
			case 1: $ok_cnt++;break;
			case 2: $re_cnt++;break;
		}
	}
	$fail_cnt = count($idarr) - ($ok_cnt + $re_cnt);
	
	if ($ok_cnt) {
		$type = 2;
		$msg = "�ɹ��ղ� $ok_cnt ��";
	}
	if ($re_cnt) {
		if($type != 2) $type = 1;
		$msg = $msg ? $msg.", $re_cnt �����ղز���Ҫ�ظ��ղ�" : "$re_cnt �����ղز���Ҫ�ظ��ղ�";
	}
	if ($fail_cnt) {
		if($type >=1) {$type = 1;}
		else{$type = 0;}
		$msg = $msg ? $msg.", $fail_cnt ���ղس���" : "$fail_cnt ���ղس���";
	}
	
	showmsg($msg,$type);
}

//�����б�����
elseif ($optype == 'search_down') {

	//Ȩ���ж�
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		showmsg("��Ա�ѹ��ڣ����ܽ��иò���������ϵ�ͷ���0755-83279360",0);
	}
	
	$ids = $_GET['ids'];
	empty($ids) && showmsg("��ѡ�����", 0);
	
	$idarr = explode(',', $ids);#����ID
	
	//δѡ��
	
	//�û��ײ�
	$member = z_member($uid);
	$setmeal = z_setmeal($uid);
	$down_num = $member['c_down_num'];
	$down_num_max = $setmeal['download_resume_ordinary'];
	if ($down_num >= $down_num_max) {
		showmsg("�����ؼ�����Ŀ����",0);
	}
	
	$ok_cnt = 0;
	$re_cnt = 0;
	$fail_cnt = 0;
	
	//ѭ������
	foreach ($idarr as $resume_id) {
		//�ж��Ƿ�������
		$downed = z_check_downed($uid, $resume_id);
		if ($downed) {
			$re_cnt ++;
			continue;
		}
		
		/*++����������++*/
		if ($down_num < $down_num_max) {
			//����
			$company = z_company($uid);
			$resume = z_resume($resume_id);
			if (!empty($company) && !empty($resume)) {
				z_down($company, $resume);
			}
			$down_num ++;
			$ok_cnt ++;
			
			z_member_num_add($uid, 'c_down_num');
		} else {
			break;
		}		
	}
	$fail_cnt = count($idarr) - ($ok_cnt + $re_cnt);
	
	
	if ($ok_cnt) {
		$type = 2;
		$msg = "�ɹ������� $ok_cnt ������";
	}
	if ($re_cnt) {
		if($type != 2) $type = 1;
		$msg = $msg ? $msg.", $re_cnt �������Ѿ����ز���Ҫ�ظ�����" : "$re_cnt �������Ѿ����ز���Ҫ�ظ�����";
	}

	if ($fail_cnt) {
		if($type >=1) {$type = 1;}
		else{$type = 0;}
		$msg = $msg ? $msg.", $fail_cnt �����س���" : "$fail_cnt �����س���";
	}
	
	showmsg($msg,$type);
}

//