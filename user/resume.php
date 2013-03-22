<?php
/*��ְ�߼���*/

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

$admin_id = $_SESSION['admin_id'];

//����Ա
if ($admin_id) {
	$id = intval($_GET['uid']);
	$smarty->assign('id', $id);
	
	$resume = z_resume($id);
	if (!empty($resume)) {
		/*����Ԥ��*/
		$t = assing_resume($id, 'admin');
		if ($t > 0) {
			$smarty->assign('title','���� - '.$_CFG['site_name']);
			$smarty->display('mail_template/outward.htm');
		} else {
			$links[0]['text'] = '����';
	        $links[0]['href'] = $_CFG['site_domain'];
			showmsg('������������',0,$links);
		}
	} else {
		$links[0]['text'] = '����';
        $links[0]['href'] = $_CFG['site_domain'];
		showmsg('���������ڻ���ɾ����',0,$links);
	}
	
} else {
	//�û�ID
	$uid = $_SESSION['uid'];
	$id = intval($_GET['uid']);
	$act = isset($_GET['act']) ? $_GET['act'] : '';
	$smarty->assign('id', $id);
	
	//δ��¼
	$utype = $_SESSION['utype'];
	if (empty($uid)) {
		$link[0]['text'] = "��¼";
		$link[0]['href'] = $_CFG['site_domain'];
		showmsg('���ȵ�¼',0);
	}
	
	//��ҵ
	if ($utype == 1) {
		//�Ƿ����ؼ���
		$downInfo = $db->get_total("SELECT COUNT(*) num FROM hr_company_down_resume WHERE resume_uid='$id' AND company_uid='$uid'");
		$downed = empty($downInfo) ? 2 : 3;#2δ���ء�3������
			
		//+ ��Ͷ�ݵĵ���������������
		$tmp = z_jobs_apply_exist($id, $uid);
		if ($tmp) {
			$downed = 3;
		}
		//end
		
		/*���ؼ���*/
		if ($act == 'down') {
			if ($downed == 3) {
				$link[0]['text'] = "����";
				$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
				showmsg('�����Ѿ������ˣ������ظ�����',0);
			} else {
				$resume = $db->getone("SELECT * FROM hr_resume WHERE uid='$id'");
				$company = $db->getone("SELECT * FROM hr_company_profile WHERE uid='$uid'");
				
				if (!empty($resume) && !empty($company)) {
					
					//Ȩ���ж�
					$expire = z_company_expire($_SESSION['uid']);
					if ($expire) {
						$link[0]['text'] = "����";
						$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
						showmsg("��Ա�ѹ��ڣ����ܽ��иò���������ϵ�ͷ���0755-83279360",0,$link);
					}
					z_down($company, $resume);
					
					$link[0]['text'] = "����";
					$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
					showmsg("���سɹ���",2,$link);
				} else {
					showmsg('��������',0);
				}
			}
		} 
		
		/*�ղؼ���*/
		elseif ($act == 'favorites') {
			$resume = $db->getone("SELECT * FROM hr_resume WHERE uid='$id'");
			
			if (!empty($resume)) {
				//�Ƿ��ղؼ���
				$favorited = $db->get_total("SELECT COUNT(*) num FROM hr_company_favorites WHERE resume_id='".$resume['id']."' AND company_uid='$uid'");
				if ($favorited) {
					showmsg('�ü����Ѿ��ղ�',0);
				} else {
					$data['resume_id'] = $resume['id'];
					$data['company_uid'] = $uid;
					$data['favoritesa_ddtime'] = time();
					!inserttable(table('company_favorites'),$data)?showmsg("�ղ�ʧ�ܣ�",0):'';
					
					$link[0]['text'] = "����";
					$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
					showmsg("�ղسɹ���",2,$link);
				}
			} else {
				showmsg('��������',0);
			}
		}
		
		/*����Ԥ��*/
		else {
			$resume = z_resume($id);
			
			//�Ƿ�Ըù�˾����
			$undisplay = 0;
			$company = z_company($uid);
			if ($company['forbidden']) {
				$tmp = explode(',', $company['forbidden']);
				if (in_array($id, $tmp)) {
					$undisplay = 1;
				}
			}
			
			if (!empty($resume) && ($undisplay==0) && ($resume['hideall']==0) && ($resume['audit']==1 || $resume['audit']==4)) {
				$t = assing_resume($id, $downed);
				if ($t > 0) {
					$smarty->assign('title','���� - '.$_CFG['site_name']);
					$smarty->display('mail_template/outward.htm');
				} else {
					$links[0]['text'] = '����';
			        $links[0]['href'] = $_CFG['site_domain'];
					showmsg('������������',0,$links);
				}
			} else {
				$links[0]['text'] = '����';
		        $links[0]['href'] = $_CFG['site_domain'];
				showmsg('���������ڻ���ɾ����',0,$links);
			}
		}
	}
}

