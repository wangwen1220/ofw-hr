<?php
function tpl_function_qishi_pageinfo($params, &$smarty)
{
global $db,$_CFG;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "����":
		$aset['alias'] = $a[1];
		break;
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	case "����ID":
		$aset['id'] = $a[1];
		break;		
	case "school":
		$aset['school'] = $a[1];
		break;		
	case "district":
		$aset['district'] = $_SERVER['REQUEST_URI'];
		break;		
	}
}
if (is_array($aset))$aset=array_map("get_smarty_request",$aset);//��ȡ����
/*
$aset['alias']=$aset['alias']?$aset['alias']:"QS_index";
$aset['listname']=$aset['listname']?$aset['listname']:"list";
if ($alias=="QS_newslist" && $aset['id'])
{
	$sql = "select title,description,keywords from ".table('article_category')." where id = ".intval($aset['id'])." LIMIT  1";
	$info=$db->getone($sql);
}
else
{
$sql = "select title,description,keywords from ".table('page')." where alias = '".$aset['alias']."'  LIMIT  1";
$info=$db->getone($sql);
}
*/
#e($aset);

//��ȡSEO����
include_once QISHI_ROOT_PATH.'/data/seo.php';

//��˾,��ְλ
if ($aset['id']){//�д������
	$id = intval($aset['id']);
	if ($aset['alias'] == 'company_detail'){
		//��ѯ��˾�����Ϣ
		$info = $seo_content['company_detail'];
		$sql = "SELECT `companyname` FROM ".table('company_profile')." WHERE uid=".$id;
		$companyname = $db->getfirst($sql);
		foreach ($info as $k=>$v){
			$info[$k] = str_replace('{companyname}', $companyname, $v);
		}
	}
	elseif ($aset['alias'] == 'job_detail'){
		//��ѯְλ�����Ϣ
		$info = $seo_content['job_detail'];
		$sql = "SELECT `jobs_name`,`companyname` FROM ".table('jobs')." WHERE id=".$id;
		$res = $db->getone($sql);
		foreach ($info as $k=>$v){
			$info[$k] = str_replace(array('{jobname}','{companyname}'), array($res['jobs_name'],$res['companyname']), $v);
		}
	}
	elseif ($aset['alias'] == 'list'){//��ҵ
		$info = $seo_content['list'][$id];
	}

}
else if($aset['district']){
	$area_id = 0;
	$area = '';
	if(strpos($aset['district'], '?') !== FALSE){
		//��̬����
		parse_str($_SERVER['QUERY_STRING'],$url_param);
		if(!empty($url_param['district'])){//�������
			$flag = '';
			foreach($url_param['district'] as $v){
				$area .= $flag.get_categoryname_by_id($v, 'district');
				$flag = '��';
			}
		}
		else if(isset($_GET['list_areaid'])) {//��������
			$area_id = $url_param['list_areaid'];
			$area = get_categoryname_by_id($area_id,'district');
		}
	}
	else {//��д�ľ�̬����
		$type = substr(str_replace($_CFG['site_domain'], '', $aset['district']),1);
		switch($type){
		case 'beijing':
			$area_id = 1;break;
		case 'shanghai':
			$area_id = 2;break;
		case 'guangdong':
			$area_id = 20;break;
		case 'zhejiang':
			$area_id = 12;break;
		case 'jiangsu':
			$area_id = 11;break;
		case 'shandong':
			$area_id = 16;break;
		case 'fujian':
			$area_id = 14;break;
		case 'hubei':
			$area_id = 18;break;
		case 'hunan':
			$area_id = 19;break;
		case 'shanxi':
			$area_id = 27;break;
		}
		//�Ե�����Ϣ�����滻
		$area = get_categoryname_by_id($area_id,'district');
	}

	$info = $seo_content['district'];
	foreach ($info as $k=>$v){
		$info[$k] = str_replace('{district}', $area, $v);
	}
}
else{//�޲���
	if ($aset['alias'] == 'list'){
		$info = $seo_content['listpage'];
	}
	else{
		$info = $seo_content[$aset['alias']];
	}
}




if ($aset['school']){//У԰
	$info = $seo_content['education'];
}

if($_SERVER['SCRIPT_NAME'] == '/jobs/index.php'){
	if(preg_match('/jobcategory=(\d+)$/',$_SERVER['QUERY_STRING'],$m)){//��λ�����Ż�
		if(ifparent_category($m[1])){//һ����
			$info = $seo_content['category_parent'];
			$pcat = get_categoryname_by_id($m[1], 'jobs');
			foreach ($info as $k=>$v){
				$info[$k] = str_replace('{pcat}', $pcat, $v);
			}	
		}
		else {//������
			$sql = "SELECT id,parentid,categoryname FROM ".table('category_jobs')." WHERE id=".$m[1];
			$res = $db->getone($sql);
			$scat = $res['categoryname'];
			//��ѯ������Ϣ
			if($res['parentid'] > 0){
				$sql = "SELECT id,categoryname FROM ".table('category_jobs')." WHERE id=".$res['parentid'];
				$res = $db->getone($sql);
				$pcat = $res['categoryname'];
			}
			$info = $seo_content['category_sub'];
			foreach ($info as $k=>$v){
				$info[$k] = str_replace('{pcat}', $pcat, $v);
				$info[$k] = str_replace('{scat}', $scat, $info[$k]);
			}	
		
		}
	}
	else if($_GET['key'] != '') {//���������Ż�
		$keyword = $_GET['key'];
		$category = '';
		$district = '';
		if(!empty($_GET['district'])){//�������
			$flag = '';
			foreach($_GET['district'] as $v){
				$district .= $flag.get_categoryname_by_id($v, 'district');
				$flag = '��';
			}
		}
		if(!empty($_GET['category'])){//�������
			$flag = '';
			foreach($_GET['category'] as $v){
				$category .= $flag.get_categoryname_by_id($v, 'jobs');
				$flag = '��';
			}
		}
		$info = $seo_content['keyword'];
		foreach ($info as $k=>$v){
			$info[$k] = str_replace('{keyword}', $keyword, $v);
			$info[$k] = str_replace('{category}', $category, $info[$k]);
			$info[$k] = str_replace('{district}', $district, $info[$k]);
		}	


	}
}


#e($info);exit;

/*
	$info['title']=str_replace('{domain}',$_CFG['site_domain'],$info['title']);
	$info['title']=str_replace('{sitename}',$_CFG['site_name'],$info['title']);
	$info['title']=str_replace('{district}',$_CFG['subsite_districtname'],$info['title']);
	$info['description']=str_replace('{domain}',$_CFG['site_domain'],$info['description']);
	$info['description']=str_replace('{sitename}',$_CFG['site_name'],$info['description']);
	$info['description']=str_replace('{district}',$_CFG['subsite_districtname'],$info['description']);
	$info['keywords']=str_replace('{domain}',$_CFG['site_domain'],$info['keywords']);
	$info['keywords']=str_replace('{sitename}',$_CFG['site_name'],$info['keywords']);
	$info['keywords']=str_replace('{district}',$_CFG['subsite_districtname'],$info['keywords']);
*/	
	if(empty($info)){
		$info = $seo_content['listpage'];
	}
	$smarty->assign($aset['listname'],$info);
}
?>
