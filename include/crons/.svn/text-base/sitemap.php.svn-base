<?php
/*
 1.	自动生成
 */

if(!defined('IN_QISHI')) {
	die('Access Denied!');
}

ignore_user_abort(true);
set_time_limit(180);

global $_CFG,$timestamp;


	//更新任务时间表
	if ($crons['weekday']>=0)
	{
	$weekday=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$nextrun=strtotime("Next ".$weekday[$crons['weekday']]);
	}
	elseif ($crons['day']>0)
	{
	$nextrun=strtotime('+1 months'); 
	$nextrun=mktime(0,0,0,date("m",$nextrun),$crons['day'],date("Y",$nextrun));
	}
	else
	{
	$nextrun=time();
	}
	if ($crons['hour']>=0)
	{
	$nextrun=strtotime('+1 days',$nextrun); 
	$nextrun=mktime($crons['hour'],0,0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	if (intval($crons['minute'])>0)
	{
	$nextrun=strtotime('+1 hours',$nextrun); 
	$nextrun=mktime(date("H",$nextrun),$crons['minute'],0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	$setsqlarr['nextrun']=$nextrun;
	$setsqlarr['lastrun']=time();
	updatetable(table('crons'), $setsqlarr," cronid ='".intval($crons['cronid'])."'");
	//end
	

//变量
$t = time();

//网站地图
$sitemap = new siteMapXml('sitemap.xml');

//职位
$sql = "SELECT id,refreshtime 
	FROM hr_jobs 
	WHERE (audit=1 OR audit=4) AND (company_audit=1 OR company_audit=4) AND (deadline=0 OR deadline>$t) AND (setmeal_deadline=0 OR setmeal_deadline>$t) 
	ORDER BY refreshtime DESC";
$list = $db->getall($sql);
foreach ($list as $value) {
	$sitemap->addUrl('http://hr.ofweek.com/jobs/jobs-show-'.$value['id'].'.html', date('Y-m-d',$value['refreshtime']), 'daily', '0.8');
}

//公司
$sql = "SELECT uid,refreshtime
	FROM hr_company_profile
	WHERE audit=1 OR audit=4
	ORDER BY refreshtime DESC";
$list = $db->getall($sql);
foreach ($list as $value) {
	$sitemap->addUrl('http://hr.ofweek.com/company/company-show-'.$value['uid'].'.html', date('Y-m-d',$value['refreshtime']), 'daily', '0.8');
}

//资讯
$sql = "SELECT id,addtime
	FROM hr_article
	WHERE is_display=1
	ORDER BY addtime DESC";
$list = $db->getall($sql);
foreach ($list as $value) {
	$sitemap->addUrl('http://hr.ofweek.com/news/news-show-'.$value['id'].'.html', date('Y-m-d',$value['addtime']), 'daily', '0.8');
}

$sitemap->write();

/**
 * SiteMap类
 * @author zcj
 */
class siteMapXml {
	
	//SiteMap文件
	private $sitemapFile;
	
	//文件大小
	private $fileSize;
	
	//计算大小
	private $curSize = 0;
	
	//文件列表
	private $fileArr = array();
	
	//文件数
	private $fileNum = 0;
	
	//网站根目录
	private $webroot = '';
	
	function __construct($file = 'sitemap.xml') {
		$this->sitemapFile = $file;
		$this->fileSize = 1024*1024;
		$this->webroot = QISHI_ROOT_PATH;
		
		
		//删除已有文件
		$tmp = $this->webroot.'sitemap.xml';
		if (is_file($tmp)) {
			unlink($tmp);
		}
		for ($i = 0; $i < 5; $i++) {
			$tmp = $this->webroot.'sitemap.xml_'.$i.'.xml';
			if (is_file($tmp)) {
				unlink($tmp);
			}
		}		
	}
	
	/**
	 * 增加记录
	 * @param string $loc //url
	 * @param string $lastmod //改变时间
	 * @param string $changefreq //频率
	 * @param string $priority //权值
	 */
	public function addUrl($loc, $lastmod, $changefreq, $priority) {
		
		$str = "<url><loc>".$loc."</loc><lastmod>$lastmod</lastmod><changefreq>$changefreq</changefreq><priority>$priority</priority></url>";
		
		if($this->curSize + strlen($str) > $this->fileSize){
			$this->fileNum++;
			$this->curSize = 0;
			$this->fileArr[$this->fileNum] = $str;
		} else {
			$this->fileArr[$this->fileNum] .= $str;
		}
		
		$this->curSize += strlen($str);
	}
	
	/**
	 * 生成文件
	 */
	public function write(){
		for($i=0; $i <= $this->fileNum; $i++) {
			@file_put_contents($this->webroot.$this->sitemapFile.'_'.$i.'.xml', '<?xml version="1.0" encoding="UTF-8"?><urlset>'.$this->fileArr[$i].'</urlset>');
		}
		$this->writeIndex();
	}
	
	/**
	 * 文件索引
	 */
	private function writeIndex(){
		global $_G;
		
		$text = '<?xml version="1.0" encoding="UTF-8"?>';		
		$text .= '<sitemapindex>';
		
		for($i = 0; $i <= $this->fileNum; $i++){
			$text .= '<sitemap><loc>http://hr.ofweek.com/'.$this->sitemapFile.'_'.$i.'.xml</loc><lastmod>'.date('Y-m-d').'</lastmod></sitemap>';
		}
		
		$text .= '</sitemapindex>';
		return @file_put_contents($this->webroot.$this->sitemapFile, $text);
	}	
}
