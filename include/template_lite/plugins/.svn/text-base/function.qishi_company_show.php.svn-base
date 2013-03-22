<?php
function tpl_function_qishi_company_show($params, &$smarty)
{ 
	global $db,$_CFG;
	$arr=explode(',',$params['set']);
	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "企业ID":
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
		$sql = "select * from ".table('company_profile')." WHERE  uid=".intval($aset['id'])." LIMIT  1";
		$profile=$db->getone($sql);
		if (!empty($profile) && (($_SESSION['uid'] == $profile['uid']) || ($profile['audit'] == 1 || $profile['audit'] == 4 ) ))
		{
			$profile['company_url']=url_rewrite('QS_companyshow',array('id0'=>$profile['uid'],'addtime'=>$profile['addtime']));
			$profile['trade_cn']= trim($profile['trade_cn'],',');
			$profile['company_profile']=$profile['contents'];
			$profile['description']=cut_str(strip_tags($profile['contents']),50,0,"...");
			if ($profile['logo'])
			{
				$profile['logo']=$_CFG['site_dir']."data/logo/".$profile['logo'];
			}
			else
			{
				$profile['logo']=$_CFG['site_template']."images/ofweekgs_logo.jpg";
			}
			
		}
		else
		{
			header("HTTP/1.1 404 Not Found"); 
			$smarty->display("404.htm");
			exit();
		}
		$sql = "SELECT * FROM ".table('members_setmeal')." WHERE uid=".intval($aset['id']);
		$res=$db->getone($sql);
		if ($res['endtime'] < time())
		{
			$profile['expired'] = 1;
		}
		
		
	$smarty->assign($aset['listname'],$profile);
}
?>