<?php
/*求职者简历*/

define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);

$admin_id = $_SESSION['admin_id'];

//管理员
if ($admin_id) {
	$id = intval($_GET['uid']);
	$smarty->assign('id', $id);
	
	$resume = z_resume($id);
	if (!empty($resume)) {
		/*简历预览*/
		$t = assing_resume($id, 'admin');
		if ($t > 0) {
			$smarty->assign('title','简历 - '.$_CFG['site_name']);
			$smarty->display('mail_template/outward.htm');
		} else {
			$links[0]['text'] = '返回';
	        $links[0]['href'] = $_CFG['site_domain'];
			showmsg('简历不完整！',0,$links);
		}
	} else {
		$links[0]['text'] = '返回';
        $links[0]['href'] = $_CFG['site_domain'];
		showmsg('简历不存在或已删除！',0,$links);
	}
	
} else {
	//用户ID
	$uid = $_SESSION['uid'];
	$id = intval($_GET['uid']);
	$act = isset($_GET['act']) ? $_GET['act'] : '';
	$smarty->assign('id', $id);
	
	//未登录
	$utype = $_SESSION['utype'];
	if (empty($uid)) {
		$link[0]['text'] = "登录";
		$link[0]['href'] = $_CFG['site_domain'];
		showmsg('请先登录',0);
	}
	
	//企业
	if ($utype == 1) {
		//是否下载简历
		$downInfo = $db->get_total("SELECT COUNT(*) num FROM hr_company_down_resume WHERE resume_uid='$id' AND company_uid='$uid'");
		$downed = empty($downInfo) ? 2 : 3;#2未下载、3已下载
			
		//+ 已投递的当成已下载来处理
		$tmp = z_jobs_apply_exist($id, $uid);
		if ($tmp) {
			$downed = 3;
		}
		//end
		
		/*下载简历*/
		if ($act == 'down') {
			if ($downed == 3) {
				$link[0]['text'] = "返回";
				$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
				showmsg('简历已经下载了，不能重复下载',0);
			} else {
				$resume = $db->getone("SELECT * FROM hr_resume WHERE uid='$id'");
				$company = $db->getone("SELECT * FROM hr_company_profile WHERE uid='$uid'");
				
				if (!empty($resume) && !empty($company)) {
					
					//权限判断
					$expire = z_company_expire($_SESSION['uid']);
					if ($expire) {
						$link[0]['text'] = "返回";
						$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
						showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0,$link);
					}
					z_down($company, $resume);
					
					$link[0]['text'] = "返回";
					$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
					showmsg("下载成功！",2,$link);
				} else {
					showmsg('参数错误',0);
				}
			}
		} 
		
		/*收藏简历*/
		elseif ($act == 'favorites') {
			$resume = $db->getone("SELECT * FROM hr_resume WHERE uid='$id'");
			
			if (!empty($resume)) {
				//是否收藏简历
				$favorited = $db->get_total("SELECT COUNT(*) num FROM hr_company_favorites WHERE resume_id='".$resume['id']."' AND company_uid='$uid'");
				if ($favorited) {
					showmsg('该简历已经收藏',0);
				} else {
					$data['resume_id'] = $resume['id'];
					$data['company_uid'] = $uid;
					$data['favoritesa_ddtime'] = time();
					!inserttable(table('company_favorites'),$data)?showmsg("收藏失败！",0):'';
					
					$link[0]['text'] = "返回";
					$link[0]['href'] = $_CFG['site_domain'].'/user/resume.php?uid='.$id;
					showmsg("收藏成功！",2,$link);
				}
			} else {
				showmsg('参数错误',0);
			}
		}
		
		/*简历预览*/
		else {
			$resume = z_resume($id);
			
			//是否对该公司隐藏
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
					$smarty->assign('title','简历 - '.$_CFG['site_name']);
					$smarty->display('mail_template/outward.htm');
				} else {
					$links[0]['text'] = '返回';
			        $links[0]['href'] = $_CFG['site_domain'];
					showmsg('简历不完整！',0,$links);
				}
			} else {
				$links[0]['text'] = '返回';
		        $links[0]['href'] = $_CFG['site_domain'];
				showmsg('简历不存在或已删除！',0,$links);
			}
		}
	}
}

