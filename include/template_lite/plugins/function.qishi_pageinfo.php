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
	$smarty->assign($aset['listname'],$info);
}
?>