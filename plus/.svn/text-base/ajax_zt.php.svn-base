<?php
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');

function array_iconv(&$item, $key, $prefix){
	$item = mb_convert_encoding($item, "UTF-8", "gbk");
}
switch ($_GET['action']){
	case 'jobinfo':{
		$sql = "SELECT * FROM ".table('jobs')." WHERE id=${_GET['id']}";
		$res = $db->getone($sql);
		array_walk($res,'array_iconv');
		$res['refreshtime'] = date("Y-m-d", $res['refreshtime']);
		$res['contents'] = nl2br($res['contents']);
		echo json_encode($res);
		break;
	}
	case 'autocomplete_keyword':{
		$str = mb_convert_encoding($_GET['query'], "gb2312", "utf-8");
		echo "{query:'$str',suggestions:['.net','.net','.NET']}";
/*
		$sql="SELECT * FROM ".table('hotword')." WHERE w_word like '%{$gbk_query}%' ORDER BY `w_hot` DESC LIMIT 0 , 10";
		$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			$list[]="'".$row['w_word']."'";
		}
		if ($list)
		{
		$liststr=implode(',',$list);
		$str="{";
		$str.="query:'{$gbk_query}',";
		$str.="suggestions:[{$liststr}]";
		$str.="}";
 */		
		break;
	}
	case 'autocomplete_category':{
		$str = mb_convert_encoding($_GET['query'], "gb2312", "utf-8");
		echo "{query:'$str',suggestions:['销售管理类','IT技术类','编辑类']}";
		break;
	}	
	case 'subclass':{
		$res = get_subcategory($_GET['type'], $_GET['parentid']);
		$ret = array();
		foreach ($res as $k=>$v){
			$ret[$k]['id'] = $v['id'];
			$ret[$k]['categoryname'] = mb_convert_encoding($v['categoryname'], "utf-8", "gb2312");
		}
		echo json_encode($ret);
		break;
	}
	case 'related_category':{
		$str = $_GET['key'];
		$sql = "SELECT * FROM ".table('category_jobs')." where categoryname LIKE '%".$str."%'  order BY category_order desc,stat_jobs DESC, id asc";
		$res = $db->getall($sql);
		$ret = array();
		foreach ($res as $k=>$v){
			$ret[$k]['id'] = $v['id'];
			$ret[$k]['categoryname'] = mb_convert_encoding($v['categoryname'], "utf-8", "gb2312");
			$ret[$k]['parentid'] = $v['parentid'];
		}		
		echo json_encode($ret);
		break;
	}
	//分类导航
	case 'navisub':{
		$pid = $_GET['id'];
		if ($pid == '') exit(0);
		$return = '';
		$sql = "SELECT id,categoryname FROM ".table('category_jobs')." WHERE id=".$pid. "  order BY category_order desc,stat_jobs DESC, id asc";
		$res = $db->getone($sql);
		if (empty($res)) return -1;
		$return .= '<span class="gw-sublist-item chooseall"><label for="ckbx-'.$res['id'].'"><input type="checkbox" value="'.$res['id'].'" id="ckbx-'.$res['id'].'">'.$res['categoryname'].'</label></span>';
		$return .= '<div class="gw-sublist-item-wrapper">';
		$subclass = get_subcategory('category_jobs', $pid);
		if (empty($subclass)) return -1;
		foreach ($subclass as $v){
			$return .= '<span class="gw-sublist-item"><label for="cbx-'.$v['id'].'"><input type="checkbox" value="'.$v['id'].'" id="cbx-'.$v['id'].'">'.$v['categoryname'].'</label></span>';
		}
		$return .= '</div>';
		echo $return;
		exit;
	}
	//地区导航
	case 'navidis':{
		$pid = $_GET['id'];
		if ($pid == '') exit(0);
		$return = '';
		$sql = "SELECT id,categoryname FROM ".table('category_district')." WHERE id=".$pid. "  order BY category_order desc,stat_jobs DESC, id asc";
		$res = $db->getone($sql);
		if (empty($res)) return -1;
		$return .= '<span class="area-sublist-item chooseall"><label for="ckbx-'.$res['id'].'"><input type="checkbox" value="'.$res['id'].'" id="ckbx-'.$res['id'].'">'.$res['categoryname'].'</label></span>';
		$return .= '<div class="area-sublist-item-wrapper">';
		$subclass = get_subcategory('category_district', $pid);
		if (empty($subclass)) return -1;
		foreach ($subclass as $v){
			$return .= '<span class="area-sublist-item"><label for="cbx-'.$v['id'].'"><input type="checkbox" value="'.$v['id'].'" id="cbx-'.$v['id'].'">'.$v['categoryname'].'</label></span>';
		}
		$return .= '</div>';
		echo $return;
		exit;
	}
	//分类导航关键词
	case 'navisug':{
		include_once QISHI_ROOT_PATH."data/cache/category_jobs.php";
		
		if (empty($catelist)){
			$list = $db->getall("SELECT id,parentid,categoryname FROM ".table('category_jobs')." ORDER BY parentid ASC , category_order desc,stat_jobs DESC");
			$catelist = array();
			foreach ($list as $value) {
				if ($value['parentid']) {
					$catelist[$value['parentid']]['child'][$value['id']] = $value;
				} else {
					$catelist[$value['id']] = $value;
				}
			}
			$write = '<?php $catelist = '. var_export($catelist,true).' ;?>';
			file_put_contents(QISHI_ROOT_PATH."data/cache/category_jobs.php", $write);
		}

		
#		e($catelist);
		$keyw = mb_convert_encoding($_GET['key'], "gb2312", "utf-8");
		if ($keyw == '') {
			exit;	
		}
#		$keyw = '销售';
		
		$return = '';
		$sql = "SELECT id,parentid,categoryname FROM ".table('category_jobs')." where categoryname LIKE '%".$keyw."%'  order BY parentid ASC, category_order desc,stat_jobs DESC, id asc";
		$result = $db->getall($sql);
		
		$list = array();
		if (!empty($result)){
			foreach ($result as $key=>$value) {
				$pid = $value['parentid'];
				$id = $value['id'];
				
				if ($pid == 0) {#1 level
					$tmp = $catelist[$id];unset($tmp['child']);
					$list[$id] = $tmp;
				} else {# 2 level
					if (empty($list[$pid])) {#parentid no exist
						$tmp = $catelist[$pid];unset($tmp['child']);
						$list[$pid] = $tmp;
					}
					
					$tmp = $catelist[$pid]['child'][$id];
					$list[$pid]['child'][$id] = $tmp;
				}
			}
		}
#		e($list);

		$return .= '<div id="gw-search-resault" class="gw-search-resault fn-clear">';
		foreach ($list as $v){
			$return .= '<div class="gw-search-resault-group clear">';
			$return .= '<div class="gw-search-resault-group-header">';
			
			$return .= preg_replace("/($keyw)/", '<em class="highlight-red">\\1</em>', $v['categoryname']);
			$return .= '</div>';
			$return .= '<div class="fn-clear">';
			
			$return .= '<div class="gw-search-resault-group-all chooseall fn-fl"><label for="ckbx-'.$v['id'].'"><input type="checkbox" value="'.$v['id'].'" id="ckbx-'.$v['id'].'">';
			$return .= preg_replace("/($keyw)/", '<em class="highlight-red">\\1</em>', $v['categoryname']);
			$return .= '</label></div>';
			$return .= '<div class="gw-search-resault-group-item fn-fl">';
			foreach ($v['child'] as $val){
				$return .= '<span><label for="cbx-'.$val['id'].'" class="">';
				$return .= '<input type="checkbox" value="'.$val['id'].'" id="cbx-'.$val['id'].'">';
				$return .= preg_replace("/($keyw)/", '<em class="highlight-red">\\1</em>', $val['categoryname']);
				$return .= '</label></span>';
			}
			$return .= '</div></div></div>';
		}
		$return .= '</div>';
		echo $return;
		exit;
	}
	
	case 'recommend_email':{
		$jobsid = $_POST['jobsid'];
		$subject = $_POST['title'];
		$to = $_POST['email'];
		
		$memberInfo = z_member($_SESSION['uid']);
		$jobsInfo = z_jobs($jobsid);
		if (!empty($memberInfo) && !empty($jobsInfo) && $to) {
			
			if (empty($jobsInfo['request'])) {
				$jobsInfo['request'] = '不限制';
			}
			if (empty($jobsInfo['district_cn'])) {
				$jobsInfo['district_cn'] = '不限制';
			}
			
			if ($jobsInfo['$english'] == 1) {
				$english_cn = '一般';
			} elseif ($jobsInfo['$english'] == 2) {
				$english_cn = '良好';
			} elseif ($jobsInfo['$english'] == 3) {
				$english_cn = '优秀';
			} else {
				$english_cn = '不限';
			}
			
			if ($jobsInfo['age_min'] == 0 && $jobsInfo['age_max'] == 0) {
				$age_cn = '不限';
			} else {
				$age_cn = $jobsInfo['age_min'].'-'.$jobsInfo['age_max'];
			}
			
			$amount_cn = ($jobsInfo['amount'] == 0) ? '不限' : $jobsInfo['amount'];
			
			//+send email 推荐职位给好友
			$subject = iconv("UTF-8","gb2312//IGNORE",$subject);
			$mailArr = array(
				'to'=>$to,
				'subject'=>$subject,
				'from'=>$memberInfo['email'],
				'fromName'=>$memberInfo['realname']
			);
			$data = array(
				'friend_name'=>$to,
				'fullname'=>$memberInfo['realname'],
				'id'=>$jobsInfo['id'],
				'uid'=>$jobsInfo['uid'],
				'companyname'=>$jobsInfo['companyname'],
				'jobs_name'=>$jobsInfo['jobs_name'],
				'request'=>$jobsInfo['request'],
				'education_cn'=>$jobsInfo['education_cn'],
				'wage_cn'=>z_wage_cn($jobsInfo['wage_min'], $jobsInfo['wage_max']),
				'district_cn'=>$jobsInfo['district_cn'],
				'english_cn'=>$english_cn,
				'age_cn'=>$age_cn,
				'experience_cn'=>$jobsInfo['experience_cn'],
				'amount_cn'=>$amount_cn,
				'nature_cn'=>$jobsInfo['nature_cn'],
				'contents'=>$jobsInfo['contents']
			);
			z_mail('recommend_job', $mailArr, $data);
			//end
			
			echo 1;
		} else {
			echo 0;
		}		
		exit;
	}
}
exit;