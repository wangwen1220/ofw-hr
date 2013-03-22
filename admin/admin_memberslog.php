<?php
 /*
 * 74cms 系统日志
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_memberslog_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
check_permissions($_SESSION['admin_purview'],"memberslog");
$smarty->assign('pageheader',"会员日志");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql="";
	$oederbysql=" order BY log_addtime DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE log_username like '%{$key}%'";
		if     ($key_type===2)$wheresql=" WHERE log_uid =".intval($key);
		$oederbysql="";
	}
	if (isset($_GET['log_utype']) && !empty($_GET['log_utype']))
	{
		$wheresql=" WHERE log_utype='".intval($_GET['log_utype'])."'";
	}
	if (isset($_GET['settr']) && !empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE log_addtime> ".$settr:$wheresql." AND log_addtime> ".$settr;
	}
	if (isset($_GET['log_type']) && !empty($_GET['log_type']))
	{
		$log_type=intval($_GET['log_type']);
		$wheresql=empty($wheresql)?" WHERE log_type= ".$log_type:$wheresql." AND log_type= ".$log_type;
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_memberslog_list($offset,$perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('total',$total_val);
	$smarty->assign('page',$page->show(3));
	$smarty->display('memberslog/admin_memberslog_list.htm');
}
elseif($act == 'del_memberslog')
{
	check_token();
	$id=$_REQUEST['id'];
	$dnum=del_memberslog($id);
	if ($dnum>0)
	{
	adminmsg("删除成功！共删除".$dnum."行",2);
	}
	else
	{
	adminmsg("删除失败！",0);
	}
}
?>