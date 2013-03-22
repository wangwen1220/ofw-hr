<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');

//Ȩ��
$setmeal = z_setmeal($_SESSION['uid']);
if (empty($setmeal['resume_search'])) {
	$link[0]['text'] = "����";
	$link[0]['href'] = 'company_index.php';
	showmsg("��δ��ͨ�߼���Ա���������ǿͷ���ϵ��0755-83279360",0,$link);
}

//Ȩ���ж�
$expire = z_company_expire($_SESSION['uid']);
if ($expire) {
	$link[0]['text'] = "����";
	$link[0]['href'] = 'company_index.php';
	showmsg("��Ա�ѹ��ڣ����ܽ��иò���������ϵ�ͷ���0755-83279360",0,$link);
}

#e($_GET);

//�ؼ���
$keyword = mysql_real_escape_string($_GET['key']);
$keyword = addcslashes($keyword, '%_'); 


////////����ҳ
//��λ
$search_category = $_GET['category'];//��λ����array
//����������
$search_district = $_GET['district'];//����array

//�����������
$search_update = $_GET['update'];//�������
//������ʼ
$search_age1 = $_GET['age1'];//�������䷶Χ
$search_age2 = $_GET['age2'];//

//ѧ����ʼ
$search_education1 = $_GET['education1'];//
$search_education2 = $_GET['education2'];//
//����������ʼ
$search_experience = $_GET['experience'];//
//������н��ʼ
$search_exsalary1 = $_GET['exsalary1'];//
$search_exsalary2 = $_GET['exsalary2'];//
//Ӣ��ȼ�
$search_english = $_GET['english'];

///////END

////////�б�ҳ
$list_sex = intval($_GET['list_sex'])?intval($_GET['list_sex']):0;
$list_experience = intval($_GET['list_experience'])?intval($_GET['list_experience']):0;
$list_age = intval($_GET['list_age'])?intval($_GET['list_age']):0;
$list_education = intval($_GET['list_education'])?intval($_GET['list_education']):0;
$list_area = intval($_GET['list_area'])?intval($_GET['list_area']):0;
$list_wage = intval($_GET['list_salary'])?intval($_GET['list_salary']):0;
////////END


//��ҳ����
$page = intval($_GET['page'])? max(1,intval($_GET['page'])):1;

//END


//��ѯ��ֹ�������û�ID
$sql = " SELECT forbidden FROM ".table('company_profile')." WHERE uid=".$_SESSION['uid'];
$res = $db->getone($sql);
$forbid = trim($res['forbidden'],',');
if ($forbid){
	$forbidsql = "AND a.uid NOT IN($forbid)";
}


//����SQL����
$option = " WHERE 1 $forbidsql AND a.audit=1 AND a.hideall!=1 ";

if ($keyword != ''){//�ؼ���ȡ��Щ�ֶδ���
	$option .= " AND (b.`self_evaluation` LIKE '%$keyword%' OR b.`specialty` LIKE '%$keyword%' 
					OR e.`content` LIKE '%$keyword%' OR e.`school` LIKE '%$keyword%' 
					OR e.`speciality` LIKE '%$keyword%' OR w.`achievements`  LIKE '%$keyword%' 
					OR w.`jobs` LIKE '%$keyword%' OR w.`companyname`  LIKE '%$keyword%' 
				)";
}

if (!empty($search_category)){//�����λT:intention
	$option .= " AND CONCAT(',',CONCAT(b.`category`,',')) REGEXP ',(".implode('|', $search_category)."),' ";
}
if (!empty($search_district)){//����������T:intention
	$option .= " AND CONCAT(',',CONCAT(b.`district`,',')) REGEXP ',(".implode('|', $search_district)."),' ";
}

//��������
if ($search_update){//T:resume
	switch ($search_update){
		case 1:{
			$day = 1;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 2:{
			$day = 3;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 3:{
			$day = 7;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 4:{
			$day = 30;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 5:{
			$day = 90;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 6:{
			$day = 180;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}
		case 7:{
			$day = 360;
			$option .= " AND a.`refreshtime`>=".($timestamp-$day*60*60*24);
			break;
		}	
		default:
			break;
	}
}


//����T:resume,birthdate

if ($search_age1 && !$search_age2){
	$option .= " AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970) >= $search_age1 ";
}
elseif ($search_age2 && !$search_age1){
	$option .= " AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970) <= $search_age2 ";
}
elseif ($search_age1 && $search_age2){
	$option .= " AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970) >= $search_age1 AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)  <= $search_age2 ";
}



//������нT:intention,expected_salary
if ($list_wage){
	switch ($list_wage){
		case 1:{
			$option .= " AND b.`expected_salary`=0 ";
			break;
		}
		case 2:{
			$option .= " AND b.`expected_salary`<2000 ";
			break;
		}
		case 3:{
			$option .= " AND (b.`expected_salary`>=2000 AND b.`expected_salary`<3000) ";
			break;
		}
		case 4:{
			$option .= " AND (b.`expected_salary`>=3000 AND b.`expected_salary`<5000) ";
			break;
		}
		case 5:{
			$option .= " AND (b.`expected_salary`>=5000 AND b.`expected_salary`<8000) ";
			break;
		}
		case 6:{
			$option .= " AND (b.`expected_salary`>=8000 AND b.`expected_salary`<10000) ";
			break;
		}
		case 7:{
			$option .= " AND (b.`expected_salary`>=10000 AND b.`expected_salary`<15000) ";
			break;
		}
		case 8:{
			$option .= " AND (b.`expected_salary`>=15000 AND b.`expected_salary`<30000) ";
			break;
		}
		case 9:{
			$option .= " AND (b.`expected_salary`>=30000) ";
			break;
		}
		default:
			break;
	}
}
else{
	if ($search_exsalary1 && !$search_exsalary2){
		$option .= " AND b.`expected_salary`<=".$search_exsalary1;
	}
	elseif ($search_exsalary2 && !$search_exsalary1){
		$option .= " AND b.`expected_salary`<=".$search_exsalary2;
	}
	elseif ($search_exsalary1 && $search_exsalary2){
//		$option .= " AND ((`wage_min`<=$search_exsalary1 AND `wage_max`>=$search_exsalary1) OR (`wage_min`>=$search_exsalary1 AND `wage_max`<=$search_exsalary1) ";
//		$option .= " OR (`wage_min`>=$search_exsalary1 AND `wage_min`<=$search_exsalary1) OR (`wage_max`>=$search_exsalary1 AND `wage_max`<=$search_exsalary1) ) ";
		$option .= " AND b.`expected_salary`<=".$search_exsalary2." AND b.`expected_salary`>=".$search_exsalary2;
	}
}

if ($_GET['discuss']){
	$option .= " OR b.`expected_salary`=0 ";
}

//ѧ��T:resume,education
if ($list_education){
	$option .= " AND a.`education`=".$list_education;
}
else{
	if ($search_education1 > $search_education2){//������Сֵ
		$tmp = $search_education1;
		$search_education1 = $search_education2;
		$search_education2 = $tmp;
	}
	
	if ($search_education1 && !$search_education2){
		$option .= " AND a.`education`>=".$search_education1;
	}
	elseif ($search_education2 && !$search_education1){
		$option .= " AND a.`education`<=".$search_education2;
	}
	elseif ($search_education1 && $search_education2){
		$option .= " AND (a.`education`>=".$search_education1." AND a.`education`<=".$search_education2.") ";
	}
}
if ($search_english){
	$option .= " AND a.`english_degree`=$search_english ";
}

//LIST����

if ($list_sex){//T:resume,sex
	$option .= " AND a.`sex`=$list_sex ";
}

if ($list_experience){//T:resume,experience
	if ($list_experience == 2){
		$option .= " AND a.`experience`<=$list_experience ";
	}
	else{
		$option .= " AND a.`experience`=$list_experience ";
	}
}
elseif ($search_experience){
	if ($search_experience == 2){
		$option .= " AND a.`experience`<=$search_experience ";
	}
	else{
		$option .= " AND a.`experience`=$search_experience ";
	}
}

if ($list_age){//resume
	switch ($list_age){
		case 1:{
			$option .= " AND ((year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)>=18 AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)<25 )";
			break;
		}
		case 2:{
			$option .= " AND ((year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)>=25 AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)<30 )";
			break;
		}
		case 3:{
			$option .= " AND ((year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)>=30 AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)<35 )";
			break;
		}
		case 4:{
			$option .= " AND ((year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)>=35 AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)<40 )";
			break;
		}
		case 5:{
			$option .= " AND ((year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)>=40 AND (year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)<45 )";
			break;
		}
		case 6:{
			$option .= " AND ((year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970)>=45 )";
			break;
		}
		default:
			break;
	}
}





//END



$page_num = 20;//ÿҳ��ʾ����
$sql = "SELECT *,a.id AS resumeid,(year(from_unixtime(unix_timestamp()-a.`birthdate`))-1970) AS birthyear, a.`education_cn` AS personal_edu FROM ".table('resume')." AS a 
		LEFT JOIN ".table('resume_intention')." AS b ON a.uid=b.uid 
		LEFT JOIN ".table('resume_education')." AS e ON a.uid=e.uid
		LEFT JOIN ".table('resume_work')." AS w ON a.uid=w.uid
		$option AND e.status=1 AND w.status=1 GROUP BY a.uid ";
#echo $sql;
$qry = $db->query($sql);
$total = $db->num_rows($qry);

if ($total > 0){
	$page_html = dPage($total, $page, rebuild_url('page'),$page_num);
}

$limit = " LIMIT ".(($page==0)?0:($page-1))*$page_num.", $page_num";
$order = " ORDER BY a.refreshtime DESC";
$sql .= $order.$limit;

#echo $sql;
$res = $db->getall($sql);

foreach ($res as $k=>$v){
	$name = cut_str($v['fullname'], 1);
	$res[$k]['fullname'] = $name.(($v['sex']==1)?'����':'Ůʿ');
	$res[$k]['main_content'] = cut_str($v['achievements'].$v['self_evaluation'],120);
	$res[$k]['main_content'] = str_replace($keyword, '<font color="red">'.$keyword.'</font>', $res[$k]['main_content']);
	$v['home_province'] or $v['home_province'] = 0;
	$v['home_city'] or $v['home_city'] = 0;
	$res[$k]['district_cn'] = get_now_address($v['home_province'],$v['home_city']);
}

$smarty->assign('resume_list',$res);
$smarty->assign('page_html',$page_html);


//�����̶�
$select_edu = array();
$res = get_category_zt('QS_education');
foreach ($res as $v){
	$select_edu[$v['c_id']] = $v['c_name'];
}
$smarty->assign('options_edu',array_reverse($select_edu,1));
//END

//���ʷ�Χ
$select_wage = array();
$res = get_category_zt('QS_wage');
foreach ($res as $v){
	$select_wage[$v['c_id']] = $v['c_name'];
}
$smarty->assign('options_wage',$select_wage);
//END


//��ѯ��������������



$smarty->assign('leftmenu',"recruitment");

$smarty->assign('title','�����б� - ��ҵ��Ա���� - '.$_CFG['site_name']);
$smarty->display('member_company/company_search_list.htm');
