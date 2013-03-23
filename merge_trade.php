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

//�޸� hr_category �����ҵ��
$sql = "SELECT * FROM ".table('category')." WHERE c_alias='QS_trade'";
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
//	$row['c_name'] = mb_convert_encoding($row['c_name'], 'utf8','gbk');
	//��������
	//���޸�����
	$new_name = '';
	switch($row['c_name']){
		case 'LED����':
			$new_name = '����/LED';
			break;
		case '���/��ѧ':
			$new_name = '��ʾ/�����ʾ';
			break;
		case '����':
			$new_name = '����/��ѧ';
			break;
		case '�Ǳ�����':
			$new_name = '�����Ǳ�/������';
			break;
		case '����':
			$new_name = '����/�Զ���';
			break;
		case '̫���ܹ��':
			$new_name = '����Դ/̫����/����';
			break;
		case '��ͨѶ':
			$new_name = 'ͨ��/��ͨѶ';
			break;
		case 'ͨ��':
			$new_name = 'ͨ��/��ͨѶ';
			break;
		case '���ӹ���':
			$new_name = '����/��Դ/΢����';
			break;
		case '��Դ':
			$new_name = '����/��Դ/΢����';
			break;
		case '����':
			$new_name = '����/����';
			break;
		case '����':
			$new_name = '����/����';
			break;
		case '���ܵ���':
			$new_name = '����/���ܵ���';
			break;
		case '����':
			$new_name = '����/���ܵ���';
			break;
		case '���':
			$new_name = '����/���ܵ���';
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

//ɾ����������, ���ַ���Ψһ
$total_cate = array();
$relation = array();
$relation_cn = array(
				'LED����'=>'����/LED',
				'���/��ѧ'=>'��ʾ/�����ʾ',
				'����'=>'����/��ѧ',
				'�Ǳ�����'=>'�����Ǳ�/������',
				'����'=>'����/�Զ���',
				'̫���ܹ��'=>'����Դ/̫����/����',
				'��ͨѶ'=>'ͨ��/��ͨѶ',
				'ͨ��'=>'ͨ��/��ͨѶ',
				'���ӹ���'=>'����/��Դ/΢����',
				'��Դ'=>'����/��Դ/΢����',
				'����'=>'����/����',
				'����'=>'����/����',
				'���ܵ���'=>'����/���ܵ���',
				'����'=>'����/���ܵ���',
				'���'=>'����/���ܵ���',
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

	//�Ȳ����һ�������¼, ɾ��֮��ķ���
	switch($row['c_name']){
		case 'ͨ��/��ͨѶ':
			//ɾ��֮��ķ���
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		case '����/��Դ/΢����':
			//ɾ��֮��ķ���
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		case '����/���ܵ���':
			//ɾ��֮��ķ���
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		case '����/����':
			//ɾ��֮��ķ���
			$sub_sql = 'SELECT c_id FROM '.table('category').' WHERE c_name="'.$row['c_name'].'" AND c_id != '.$id;
			$delete = $db->getall($sub_sql);
			break;
		default:
			continue;
	}
	if(!empty($delete)){
		$res = array_map(create_function('$v','return $v["c_id"];'), $delete);
		foreach($res as $val){
			//�����Ӧ��ϵ
			$relation[$val]['id'] = $id;
			$relation[$val]['name'] = $row['c_name'];
			unset($total_cate[$val]);
		}
		$sql = "DELETE FROM ".table('category')." WHERE c_id IN(".implode(',', $res).");";
//		echo $sql.'<br/>';
		$db->query($sql);
	}
}


//�޸� hr_jobs ���ְλ��ҵ��
//$limit = ' LIMIT 5';
$sql = "SELECT id,trade,trade_cn FROM ".table('jobs').$limit;
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$update = array();
	$trade = explode(',', trim($row['trade'], ','));
	$trade_cn = explode(',', trim($row['trade_cn'], ','));

	if(count($trade) != count($trade_cn)){//���ݲ�һ��,������
		echo 'No Match at: '. $row['id'].'<br/>';
		continue;
	}
	foreach($trade as $key=>$val){
		if(isset($relation[$val])){//����ж�Ӧ��ת������,��ɾ������,�滻���༰������
			$update['trade'][$key] = $relation[$val]['id'];
			$update['trade_cn'][$key] = $relation[$val]['name'];
		}
		//�����ı�
		else {
			$update['trade'][$key] = $val;
			$update['trade_cn'][$key] = $trade_cn[$key];
		}
	}
	foreach($trade_cn as $key=>$val){
		//����
		if(isset($relation_cn[$val])){//����ж�Ӧ��ת������,��ɾ������,�滻���༰������
			$update['trade_cn'][$key] = $relation_cn[$val];
		}
		//�����ı�
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

//�޸� hr_company_profile ��Ĺ�˾��ҵ�� trade_cn �ֶ�

//$limit = ' LIMIT 5';
$sql = "SELECT id,trade,trade_cn FROM ".table('company_profile').$limit;
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$update = array();
	$trade = explode(',', trim($row['trade'], ','));
	$trade_cn = explode(',', trim($row['trade_cn'], ','));

	if(count($trade) != count($trade_cn)){//���ݲ�һ��,������
		echo 'No Match at: '. $row['id'].'<br/>';
		continue;
	}
	foreach($trade as $key=>$val){
		if(isset($relation[$val])){//����ж�Ӧ��ת������,��ɾ������,�滻���༰������
			$update['trade'][$key] = $relation[$val]['id'];
			$update['trade_cn'][$key] = $relation[$val]['name'];
		}
		//�����ı�
		else {
			$update['trade'][$key] = $val;
			$update['trade_cn'][$key] = $trade_cn[$key];
		}
	}
	foreach($trade_cn as $key=>$val){
		//����
		if(isset($relation_cn[$val])){//����ж�Ӧ��ת������,��ɾ������,�滻���༰������
			$update['trade_cn'][$key] = $relation_cn[$val];
		}
		//�����ı�
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

//����hr_resume_intention

//$limit = ' LIMIT 50';
$sql = "SELECT id,industry FROM ".table('resume_intention').$limit;
$qry = $db->query($sql);
while($row = $db->fetch_array($qry)){
	$update = array();
	$trade = explode(',', trim($row['industry'], ','));
	foreach($trade as $key=>$val){
		if(isset($relation[$val])){//����ж�Ӧ��ת������,��ɾ������,�滻���༰������
			$update['trade'][$key] = $relation[$val]['id'];
			$update['trade_cn'][$key] = $relation[$val]['name'];
		}
		//�����ı�
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


