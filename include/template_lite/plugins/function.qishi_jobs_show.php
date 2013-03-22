<?php
/*********************************************
*企业简介
* *******************************************/
function tpl_function_qishi_jobs_show($params, &$smarty)
{
	global $db,$timestamp,$_CFG;
	$arr=explode(',',$params['set']);
	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "职位ID":
			$aset['id'] = $a[1];
			break;
		case "列表名":
			$aset['listname'] = $a[1];
			break;
		}
	}
	$aset=array_map("get_smarty_request",$aset);
	$aset['id']=$aset['id']?intval($aset['id']):0;
	$aset['listname']=$aset['listname']?$aset['listname']:"list";
//	$option = " WHERE 1 AND (audit=1 OR audit=4)  AND display=1 AND ($timestamp <= deadline OR deadline=0 ) AND $timestamp <= setmeal_deadline AND (company_audit=1 OR company_audit=4) ";
	$option = " WHERE 1 ";
	$option .= " AND id={$aset['id']} ";
	
	$sql = "select * from ".table('jobs').$option." LIMIT 1";
	$val=$db->getone($sql);
	if (!empty($val) && (($_SESSION['uid'] == $val['uid']) || (($val['deadline'] >= $timestamp || $val['deadline']==0) && ($timestamp <= $val['setmeal_deadline']) && ($val['audit'] == 1 || $val['audit'] == 4) 
		&& $val['display'] == 1 && ($val['company_audit'] == 1 || $val['company_audit'] == 4)))
	){
		if ($val['setmeal_deadline']<time() && $val['setmeal_deadline']<>"0" && $val['add_mode']=="2"){
			$val['deadline']=$val['setmeal_deadline'];
		}
		$val['amount']=$val['amount']=="0"?'若干':$val['amount'];
		$profile=GetJobsCompanyProfile($val['uid']);
		$val['company']=$profile;
		$val['expire']=sub_day($val['deadline'],time());	
		$val['contents']= nl2br($val['contents']);
		$val['company_url']= '/company/company-show-'.$val['uid'].'.html';
		$val['company']['trade_cn']  = trim($val['company']['trade_cn'],',');
		if ($val['company']['logo'])
		{
			$val['company']['logo']=$_CFG['site_dir']."data/logo/".$val['company']['logo'];
		}
		else
		{
			$val['company']['logo']=$_CFG['site_template']."images/ofweekgs_logo.jpg";
		}
		if ($val['tag'])
		{
			$tag=explode('|',$val['tag']);
			$taglist=array();
			if (!empty($tag) && is_array($tag))
			{
				foreach($tag as $t)
				{
				$tli=explode(',',$t);
				$taglist[]=array($tli[0],$tli[1]);
				}
			}
			$val['tag']=$taglist;
		}
		else
		{
			$val['tag']=array();
		}		
	}
	else
	{
		header("HTTP/1.1 404 Not Found"); 
		$smarty->display("404.htm");
		exit();
		
	}
$smarty->assign($aset['listname'],$val);
}
function GetJobsCompanyProfile($id)
{
	global $db;
	$sql = "select * from ".table('company_profile')." where uid=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}
?>