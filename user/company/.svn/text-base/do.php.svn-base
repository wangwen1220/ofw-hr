<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"recruitment");

$uid = $_SESSION['uid'];
$optype = $_POST['optype'];

//收藏里面下载简历
if ($optype == 'down_favorites') {
	
	//权限判断
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		$link[0]['text'] = "返回";
		$link[0]['href'] = 'company_index.php';
		showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0,$link);
	}
	
	$idarr = $_POST['idarr'];#简历ID
	
	//未选择
	empty($idarr) && showmsg("请选择简历", 0);
	
	//用户套餐
	$member = z_member($uid);
	$setmeal = z_setmeal($uid);
	$down_num = $member['c_down_num'];
	$down_num_max = $setmeal['download_resume_ordinary'];
	if ($down_num >= $down_num_max) {
		showmsg("可下载简历数目不够",0);
	}
	
	$ok_cnt = 0;
	$re_cnt = 0;
	$fail_cnt = 0;
	
	//循环下载
	foreach ($idarr as $resume_id) {
		//判断是否已下载
		$downed = z_check_downed($uid, $resume_id, 2);
		if ($downed) {
			$re_cnt ++;
			continue;
		}
		
		/*++下载数限制++*/
		if ($down_num < $down_num_max) {
			//下载
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
		$msg = "成功下载了 $ok_cnt 条简历";
	}
	
	if ($re_cnt) {
		if($type != 2) $type = 1;
		$msg = $msg ? $msg.", $re_cnt 条简历已经下载不需要重复下载" : "$re_cnt 条简历已经下载不需要重复下载";
	}

	if ($fail_cnt) {
		if($type >=1) {$type = 1;}
		else{$type = 0;}
		$msg = $msg ? $msg.", $fail_cnt 条下载出错" : "$fail_cnt 条下载出错";
	}
	
	$link[0]['text'] = "简历收藏夹";
	$link[0]['href'] = 'company_recruitment.php?act=favorites_list';
	showmsg("成功下载了 $ok_cnt 条简历！",2,$link);
}

//收藏删除
elseif ($optype == 'del_favorites') {
	$idarr = $_POST['idarr'];#简历ID
	
	//未选择
	empty($idarr) && showmsg("请选择简历", 0);
	
	//删除
	z_favorites_del($uid, $idarr);
	
	$link[0]['text'] = "简历收藏夹";
	$link[0]['href'] = 'company_recruitment.php?act=favorites_list';
	showmsg("删除成功",2,$link);
}


$optype = $_GET['optype'];

//搜索列表收藏
if ($optype == 'search_favorite') {
	$ids = $_GET['ids'];
	if (empty($ids)) {
		showmsg("请选择简历",0);
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
		$msg = "成功收藏 $ok_cnt 条";
	}
	if ($re_cnt) {
		if($type != 2) $type = 1;
		$msg = $msg ? $msg.", $re_cnt 条已收藏不需要重复收藏" : "$re_cnt 条已收藏不需要重复收藏";
	}
	if ($fail_cnt) {
		if($type >=1) {$type = 1;}
		else{$type = 0;}
		$msg = $msg ? $msg.", $fail_cnt 条收藏出错" : "$fail_cnt 条收藏出错";
	}
	
	showmsg($msg,$type);
}

//收缩列表下载
elseif ($optype == 'search_down') {

	//权限判断
	$expire = z_company_expire($_SESSION['uid']);
	if ($expire) {
		showmsg("会员已过期，不能进行该操作，请联系客服！0755-83279360",0);
	}
	
	$ids = $_GET['ids'];
	empty($ids) && showmsg("请选择简历", 0);
	
	$idarr = explode(',', $ids);#简历ID
	
	//未选择
	
	//用户套餐
	$member = z_member($uid);
	$setmeal = z_setmeal($uid);
	$down_num = $member['c_down_num'];
	$down_num_max = $setmeal['download_resume_ordinary'];
	if ($down_num >= $down_num_max) {
		showmsg("可下载简历数目不够",0);
	}
	
	$ok_cnt = 0;
	$re_cnt = 0;
	$fail_cnt = 0;
	
	//循环下载
	foreach ($idarr as $resume_id) {
		//判断是否已下载
		$downed = z_check_downed($uid, $resume_id);
		if ($downed) {
			$re_cnt ++;
			continue;
		}
		
		/*++下载数限制++*/
		if ($down_num < $down_num_max) {
			//下载
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
		$msg = "成功下载了 $ok_cnt 条简历";
	}
	if ($re_cnt) {
		if($type != 2) $type = 1;
		$msg = $msg ? $msg.", $re_cnt 条简历已经下载不需要重复下载" : "$re_cnt 条简历已经下载不需要重复下载";
	}

	if ($fail_cnt) {
		if($type >=1) {$type = 1;}
		else{$type = 0;}
		$msg = $msg ? $msg.", $fail_cnt 条下载出错" : "$fail_cnt 条下载出错";
	}
	
	showmsg($msg,$type);
}

//