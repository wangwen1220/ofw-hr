<?php 
define('IN_QISHI', true);
$alias="QS_index";
require_once(dirname(__FILE__).'/include/common.inc.php');

ignore_user_abort(true);
set_time_limit(180);
ini_set("memory_limit", '1024M');
ini_set('display_errors', 1);
error_reporting(E_ALL^E_NOTICE);

require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);

/********************END header************************/

//修改 hr_category 表的行业名
$sql = "SELECT * FROM ".table('category')." WHERE c_alias='QS_trade'";
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
//	$row['c_name'] = mb_convert_encoding($row['c_name'], 'utf8','gbk');
	//调整名称
	//先修改名称
	$new_name = '';
	switch($row['c_name']){
		case 'LED照明':
			$new_name = '照明/LED';
			break;
		case '光电/光学':
			$new_name = '显示/光电显示';
			break;
		case '激光':
			$new_name = '激光/光学';
			break;
		case '仪表仪器':
			$new_name = '仪器仪表/传感器';
			break;
		case '工控':
			$new_name = '工控/自动化';
			break;
		case '太阳能光伏':
			$new_name = '新能源/太阳能/风能';
			break;
		case '光通讯':
			$new_name = '通信/光通讯';
			break;
		case '通信':
			$new_name = '通信/光通讯';
			break;
		case '电子工程':
			$new_name = '电子/电源/微电子';
			break;
		case '电源':
			$new_name = '电子/电源/微电子';
			break;
		case '节能':
			$new_name = '节能/环保';
			break;
		case '环保':
			$new_name = '节能/环保';
			break;
		case '智能电网':
			$new_name = '电力/智能电网';
			break;
		case '电力':
			$new_name = '电力/智能电网';
			break;
		case '风电':
			$new_name = '电力/智能电网';
			break;
		default:
			$new_name = 'pass';
			continue;
	}
	if($new_name != 'pass'){
		$sub_sql = 'UPDATE '.table('category').' SET c_name="'.$new_name.'" WHERE c_id='.$row['c_id'];
//		echo $sub_sql.'<br/>';
		$db->query($sub_sql);
	}
#	echo 'update '.$row['c_name'].' to: '.$new_name.'<br/>';
}

//删除重名分类, 保持分类唯一
$total_cate = array();
$relation = array();
$relation_cn = array(
				'LED照明'=>'照明/LED',
				'光电/光学'=>'显示/光电显示',
				'激光'=>'激光/光学',
				'仪表仪器'=>'仪器仪表/传感器',
				'工控'=>'工控/自动化',
				'太阳能光伏'=>'新能源/太阳能/风能',
				'光通讯'=>'通信/光通讯',
				'通信'=>'通信/光通讯',
				'电子工程'=>'电子/电源/微电子',
				'电源'=>'电子/电源/微电子',
				'节能'=>'节能/环保',
				'环保'=>'节能/环保',
				'智能电网'=>'电力/智能电网',
				'电力'=>'电力/智能电网',
				'风电'=>'电力/智能电网',
				);
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$total_cate[$row['c_id']] = $row;
}
foreach($total_cate as &$row){
//	e($row);
	$id = $row['c_id'];
	$sub_sql = '';
	$delete = array();

	//先查出第一条分类记录, 删除之后的分类
	switch($row['c_name']){
		case '通信/光通讯':
			//删除之后的分类
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		case '电子/电源/微电子':
			//删除之后的分类
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		case '电力/智能电网':
			//删除之后的分类
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		case '节能/环保':
			//删除之后的分类
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		default:
			continue;
	}
	if(!empty($delete)){
		$res = array_map(create_function('$v','return $v["c_id"];'), $delete);
		foreach($res as $val){
			//整理对应关系
			$relation[$val]['id'] = $id;
			$relation[$val]['name'] = $row['c_name'];
			unset($total_cate[$val]);
		}
		$sql = "DELETE FROM ".table('category')." WHERE c_id IN(".implode(',', $res).");";
//		echo $sql.'<br/>';
		$db->query($sql);
	}
}


//修改 hr_jobs 表的职位行业名
//$limit = ' LIMIT 5';
$sql = "SELECT id,trade,trade_cn FROM ".table('jobs').$limit;
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$update = array();
	$trade = explode(',', trim($row['trade'], ','));
	$trade_cn = explode(',', trim($row['trade_cn'], ','));

	if(count($trade) != count($trade_cn)){//数据不一致,不处理
		echo 'No Match at: '. $row['id'].'<br/>';
		continue;
	}
	foreach($trade as $key=>$val){
		if(isset($relation[$val])){//如果有对应的转换数据,已删除分类,替换分类及分类名
			$update['trade'][$key] = $relation[$val]['id'];
			$update['trade_cn'][$key] = $relation[$val]['name'];
		}
		//不做改变
		else {
			$update['trade'][$key] = $val;
			$update['trade_cn'][$key] = $trade_cn[$key];
		}
	}
	foreach($trade_cn as $key=>$val){
		//改名
		if(isset($relation_cn[$val])){//如果有对应的转换数据,已删除分类,替换分类及分类名
			$update['trade_cn'][$key] = $relation_cn[$val];
		}
		//不做改变
		else {
//			$update['trade_cn'][$key] = $update['trade_cn'][$key];
		}
	}
#	e($update);
	$sql_trade = ','.implode(',', $update['trade']).',';
	$sql_trade_cn = ','.implode(',', $update['trade_cn']).',';
	$sql = "UPDATE ".table('jobs')." SET trade='$sql_trade',trade_cn='$sql_trade_cn' WHERE id=".$row['id'];
//	echo $sql;
	$db->query($sql);
#	break;

}

//修改 hr_company_profile 表的公司行业名 trade_cn 字段

//$limit = ' LIMIT 5';
$sql = "SELECT id,trade,trade_cn FROM ".table('company_profile').$limit;
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$update = array();
	$trade = explode(',', trim($row['trade'], ','));
	$trade_cn = explode(',', trim($row['trade_cn'], ','));

	if(count($trade) != count($trade_cn)){//数据不一致,不处理
		echo 'No Match at: '. $row['id'].'<br/>';
		continue;
	}
	foreach($trade as $key=>$val){
		if(isset($relation[$val])){//如果有对应的转换数据,已删除分类,替换分类及分类名
			$update['trade'][$key] = $relation[$val]['id'];
			$update['trade_cn'][$key] = $relation[$val]['name'];
		}
		//不做改变
		else {
			$update['trade'][$key] = $val;
			$update['trade_cn'][$key] = $trade_cn[$key];
		}
	}
	foreach($trade_cn as $key=>$val){
		//改名
		if(isset($relation_cn[$val])){//如果有对应的转换数据,已删除分类,替换分类及分类名
			$update['trade_cn'][$key] = $relation_cn[$val];
		}
		//不做改变
		else {
//			$update['trade_cn'][$key] = $update['trade_cn'][$key];
		}
	}
#	e($update);
	$sql_trade = ','.implode(',', $update['trade']).',';
	$sql_trade_cn = ','.implode(',', $update['trade_cn']).',';
	$sql = "UPDATE ".table('company_profile')." SET trade='$sql_trade',trade_cn='$sql_trade_cn' WHERE id=".$row['id'];
//	echo $sql;
	$db->query($sql);
#	break;

}

//更新hr_resume_intention

//$limit = ' LIMIT 50';
$sql = "SELECT id,industry FROM ".table('resume_intention').$limit;
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$update = array();
	$trade = explode(',', trim($row['industry'], ','));
	foreach($trade as $key=>$val){
		if(isset($relation[$val])){//如果有对应的转换数据,已删除分类,替换分类及分类名
			$update['trade'][$key] = $relation[$val]['id'];
			$update['trade_cn'][$key] = $relation[$val]['name'];
		}
		//不做改变
		else {
			$update['trade'][$key] = $val;
			$update['trade_cn'][$key] = $trade_cn[$key];
		}
	}
#	e($update);
	$sql_trade = ','.implode(',', $update['trade']).',';
	$sql = "UPDATE ".table('resume_intention')." SET industry='$sql_trade' WHERE id=".$row['id'];
//	echo $sql;
	$db->query($sql);
#	break;
}


