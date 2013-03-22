<?php
class BaiboXMLGenerator {
    /**
     * 文件名字
     * @var string
     * @access public
     */
    public $sitemapFileName = "baibo.xml";
    /**
     * 每一个文件包含的连接数
     * 每个文件包含连接数最好不要 5,000.
     * 文件的大小最好不要超过10MB,如果网站的链接很长的话，请减少页面包含链接数
     * @var int
     * @access public
     */
    public $maxURLsPerSitemap = 1000;
    /**
     * sitemap索引的名字
     * @var string
     * @access public
     */
 
    public $sitemapIndexFileName = "baibo.xml";
    /**
     * 节点loc的日期格式
     */
    public $dateFormat = 'Y-m-d';
    /**
     * 如果该变量设置为true, 会生成两个sitemap文件，后缀名分别是.xml和.xml.gz 而且会被添加到robots.txt文件中.
     * 同时.gz文件会被提交给搜索引擎。
     * 如果每页包含的连接数大于50,000，则这个字段将被忽略，除了sitemap索引文件之外，其他sitemap文件都会被压缩
     * @var bool
     * @access public
     */
    public $createGZipFile = false;
    /**
     * 网站的url
     * 脚本向搜索引擎提交sitemap时会用到这个变量
     * @var string
     * @access private
     */
    private $baseURL;
    /**
     * 相关脚本的路径
     * 当你想把sitemap和robots文件放在不同的路径时，设置这个变量。
     * @var string
     * @access private
     */
    private $basePath;
    /**
     * url数组
     * @var array of strings
     * @access private
     */
    private $urls;
    /**
     * sitemap的数组
     * @var array of strings
     * @access private
     */
 
    private $sitemaps;
    /**
     * sitemap索引的数组
     * @var array of strings
     * @access private
     */
 
    private $sitemapIndex;
     /**
     * 当前sitemap的全路径
     * @var string
     * @access private
     */
    private $sitemapFullURL;
 
    /**
     * 构造函数
     * @param string $baseURL 你网站的URL, 以 / 结尾.
     * @param string|null $basePath sitemap和robots文件存储的相对路径.
     */
    public function __construct($baseURL, $basePath = "", $filename = "") {
        $this->baseURL = $baseURL;
        $this->basePath = $basePath;
		if($filename != '') $this->sitemapFileName = $filename;
    }
    /**
     * 使用这个方法可以同时添加多个url
     * 每个链接有4个参数可以设置
     * @param array of arrays of strings $urlsArray
     */
    public function addUrls($dataArray, $lastModified = null, $changeFrequency = null, $priority = null) {
        if (!is_array($dataArray))
            throw new InvalidArgumentException('参数$aURLs需要时数组');
        foreach ($dataArray as $data) {
			//拼接URL
			$url = 'jobs/jobs-show-'.$data['id'].'.html';//http://hr.ofweek.com/ **** 职位链接
            $this->addUrl(isset ($url) ? $this->baseURL.$url : null,
						isset ($lastModified) ? $lastModified : null,
						isset ($changeFrequency) ? $changeFrequency : null,
						isset ($priority) ? $priority : null,
						$data
					);
        }
    }
    /**
     * 使用这个方法每次添加一个连接到sitemap中
     * @param string $url URL
     * @param string $lastModified 当被修改时，使用UNIX时间戳
     * @param string $changeFrequency 搜索引擎抓取信息的频率
     * @param string $priority 你网站中连接的权重
     * @see http://en.wikipedia.org/wiki/ISO_8601
     * @see http://php.net/manual/en/function.date.php
	 * @param array $data 职位数据项
     */
	private function baibo_escape($str){
		$return = '';
		$return = strip_tags($str);
		$return = htmlspecialchars($return);
		$return = $this->XmlSafeStr($return);
		return $return;
	}
	private function XmlSafeStr($s) {
		return preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/', '',$s);
	}
	
    public function addUrl($url, $lastModified = null, $changeFrequency = null, $priority = null, $data) {
        if ($url == null)
            throw new InvalidArgumentException("URL 是必填项.");
        $urlLength = extension_loaded('mbstring') ? mb_strlen($url) : strlen($url);
        if ($urlLength > 2048)
            throw new InvalidArgumentException("URL的长度不能超过2048。
                                                请注意，见此url长度需要使用mb_string扩展.
                                                请确定你的服务器已经打开了这个模块");
        $tmp = array();
        $tmp['loc'] = $url;
        $tmp['lastmod'] = date($this->dateFormat,$lastModified);
        $tmp['changefreq'] = $changeFrequency;
        $tmp['priority'] = $priority;

		//处理职位数据项, 过滤等操作在此处完成
//		e($data);
		//中文字符转码mb_convert_encoding($url['data'][$key], 'utf8','gbk')

		$tmp['data']['title'] = mb_convert_encoding($data['jobs_name'], 'utf8','gbk');
		$tmp['data']['expirationdate'] = $data['deadline']?date($this->dateFormat,$data['deadline']):'无限制';
		$tmp['data']['type'] = mb_convert_encoding($data['nature_cn'], 'utf8','gbk');
		$tmp['data']['city'] = $data['district_cn']?mb_convert_encoding($data['district_cn'], 'utf8','gbk'):'未知';
		$tmp['data']['employer'] = mb_convert_encoding($data['companyname'], 'utf8','gbk');
		$tmp['data']['education'] = mb_convert_encoding($data['education_cn'], 'utf8','gbk');
		$tmp['data']['experience'] = mb_convert_encoding($data['experience_cn'], 'utf8','gbk');
		$tmp['data']['startdate'] = date($this->dateFormat,$data['refreshtime']);
		$tmp['data']['enddate'] = $data['deadline']?date($this->dateFormat,$data['deadline']):'长期有效';
		$tmp['data']['salary'] = mb_convert_encoding(z_wage_cn($data['wage_min'], $data['wage_max']), 'utf8','gbk');

		$tmp['data']['source'] = 'OFweek人才网';//OFweek人才网, 待定
		$tmp['data']['sourcelink'] = 'http://hr.ofweek.com';//OFweek人才网, 待定
		$tmp['data']['route'] = '请通过百度地图查询乘车路线'; //乘车路线,待定
		$tmp['data']['tips'] = '温馨提示：“面试时请携带个人简历”';//待定

		//过滤描述文件
		$data['contents'] = $this->baibo_escape($data['contents']);
		$tmp['data']['description'] = mb_convert_encoding($data['contents'], 'utf8','gbk');//CDATA数据
		$tmp['data']['industry'] = mb_convert_encoding(trim($data['trade_cn'],','), 'utf8','gbk');//CDATA数据

		$jobfirstclass_id = explode(',',trim($data['subclass'],','));
		$jobfirstclass = '';
		$flag = '';
		foreach ($jobfirstclass_id as $v){
			if ($v == '') continue;
			$jobfirstclass .= $flag.get_categoryname_by_id($v,'jobs');
			$flag = ',';
		}

		$tmp['data']['jobfirstclass'] = mb_convert_encoding($jobfirstclass, 'utf8','gbk'); //需处理
		$tmp['data']['jobsecondclass'] = mb_convert_encoding(trim($data['category_cn'],','), 'utf8','gbk');

		$company = z_company($data['uid']);

		$tmp['data']['employertype'] = mb_convert_encoding($company['nature_cn'], 'utf8','gbk');//公司类型, 需查询
		$tmp['data']['email'] = $company['email']; //公司邮箱,需查询
		$tmp['data']['workplace'] = mb_convert_encoding($company['address'], 'utf8','gbk');//待查询 若为空，则读取该公司的地区
		$tmp['data']['number'] = $data['amount']; //招聘人数
		//过滤描述文件
		$company['contents'] = $this->baibo_escape($company['contents']);
		$tmp['data']['introduction'] = mb_convert_encoding($company['contents'], 'utf8','gbk'); //公司简介, 需查询
		$tmp['data']['companyaddress'] = mb_convert_encoding($company['address'], 'utf8','gbk'); //公司地址,需查询
		$tmp['data']['contactname'] =  mb_convert_encoding($company['contact'], 'utf8','gbk'); //公司联系人,需查询
		$tmp['data']['tel'] = mb_convert_encoding($company['telephone'], 'utf8','gbk'); //公司电话,需查询
		
//		e($tmp);
        $this->urls[] = $tmp;
    }
    /**
     * 在内存中创建sitemap.
     */
    public function createSitemap() {
        if (!isset($this->urls))
            throw new BadMethodCallException("请先加载addUrl或者addUrls方法.");
        if ($this->maxURLsPerSitemap > 5000)
            throw new InvalidArgumentException("每个sitemap中的链接不能超过50,000个");
 
        $sitemapHeader = '<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>';
        $sitemapIndexHeader = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex></sitemapindex>';
        foreach(array_chunk($this->urls,$this->maxURLsPerSitemap) as $sitemap) {		//超过50000条部分进行分组,分组即进行压缩处理
            $xml = new SimpleXMLExtended($sitemapHeader);//分组生成索引sitemap
            foreach($sitemap as $url) {
                $row = $xml->addChild('url');
                $row->addChild('loc',htmlspecialchars($url['loc'],ENT_QUOTES,'UTF-8'));
                $row->addChild('lastmod', $url['lastmod']);
                $row->addChild('changefreq',$url['changefreq']);
                $row->addChild('priority',$url['priority']);
				//此处需对职位详细数据项进行添加
				$data = $row->addChild('data');
				$display = $data->addChild('display');
				foreach($url['data'] as $key=>$val){
					//这里判断是添加普通节点还是CDATA节点
					if($key == 'description' 
						|| $key == 'industry' 
						|| $key == 'introduction' 
						|| $key == 'employer'
						|| $key == 'jobfirstclass'
						|| $key == 'jobsecondclass'
					){
						$node = $display->addChild($key);
						$node->addCData($val);
					}
					else {
//						$display->addChild($key, $val);
						$node = $display->addChild($key);
						$node->addText($val);

					}
				}

            }
//			echo $xml->asXML();
            $this->sitemaps[] = $xml->asXML();//索引xml数据
        }


        if (count($this->sitemaps) > 1000){//索引文件数超过限制
            throw new LengthException("sitemap索引文件最多可以包含1000条索引.");
		}
        if (count($this->sitemaps) > 1) {//
            for($i=0; $i<count($this->sitemaps); $i++) {
                $this->sitemaps[$i] = array(
                    str_replace(".xml", ($i+1).".xml", $this->sitemapFileName),
                    $this->sitemaps[$i]
                );//sitemaps为array类型,项0为文件名,项1为XML数据
            }
            $xml = new SimpleXMLElement($sitemapIndexHeader);//初始化索引XML文件
            foreach($this->sitemaps as $sitemap) {//开始赋值索引XML
                $row = $xml->addChild('sitemap');
                $row->addChild('loc',$this->baseURL.htmlentities($sitemap[0]));
                $row->addChild('lastmod', date($this->dateFormat));
            }
            $this->sitemapFullURL = $this->baseURL.$this->sitemapIndexFileName;
            $this->sitemapIndex = array(
                $this->sitemapIndexFileName,
				$xml->asXML()
			);
        }
        else {//如果只有单个xml数据
			$this->sitemapFullURL = $this->baseURL.$this->sitemapFileName;
            $this->sitemaps[0] = array(//赋值
                $this->sitemapFileName,
				$this->sitemaps[0]
			);
        }
    }
    /**
     * 如果你不想生成sitemap文件，指向用其中的内容，这个返回的数组就包含了对应的信息.
     * @return 字符串数组
     * @access public
     */
    public function toArray() {
        if (isset($this->sitemapIndex))
            return array_merge(array($this->sitemapIndex),$this->sitemaps);
        else
            return $this->sitemaps;
    }
    /**
     * 写sitemap文件
     * @access public
     */
    public function writeSitemap() {
        if (!isset($this->sitemaps)){//无内容
            throw new BadMethodCallException("请先加载createSitemap方法.");
		}
        if (isset($this->sitemapIndex)) {//有索引文件
            $this->_writeFile($this->sitemapIndex[1], $this->basePath, $this->sitemapIndex[0]);
            foreach($this->sitemaps as $sitemap) {
                $this->_writeFile($sitemap[1], $this->basePath, $sitemap[0]);
            }
        }
        else {//只有单条数据 
            $this->_writeFile($this->sitemaps[0][1], $this->basePath, $this->sitemaps[0][0]);
        }
    }
    /**
     * 保存文件
     * @param string $content
     * @param string $filePath
     * @param string $fileName
     * @return bool
     * @access private
     */
    private function _writeFile($content, $filePath, $fileName) {
		if (!is_dir($filePath)){
    		mkdir($filePath);
    	}
        $file = fopen($filePath.$fileName, 'w');
        fwrite($file, $content);
        return fclose($file);
    }
}

class SimpleXMLExtended extends SimpleXMLElement {
	public function addCData($cdata_text) {
		$node = dom_import_simplexml($this); 
		$no   = $node->ownerDocument; 
		$node->appendChild($no->createCDATASection($cdata_text)); 
	} 
	public function addText($cdata_text) {
		$node = dom_import_simplexml($this); 
		$no   = $node->ownerDocument; 
		$node->appendChild($no->createTextNode($cdata_text)); 
	} 
}

/*

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <?php
            include 'SitemapGenerator.php';
            $sitemap = new SitemapGenerator("http://www.smartwei.com/");
 
            //添加url，如果你的url是通过程序生成的，这里就可以循环添加了。
            $sitemap->addUrl("http://www.smartwei.com/",  date('c'),  'daily',  '1');
            $sitemap->addUrl("http://www.smartwei.com/speed-up-firefox.html", date('c'),  'daily',    '0.5');
            $sitemap->addUrl("http://www.smartwei.com/php-form-check-class.html", date('c'),  'daily');
            $sitemap->addUrl("http://www.smartwei.com/32-userful-web-design-blogs.html", date('c'));
 
            //创建sitemap
            $sitemap->createSitemap();
 
            //生成sitemap文件
            $sitemap->writeSitemap();
 
            //更新robots.txt文件
            $sitemap->updateRobots();
 
            //提交sitemap到搜索引擎
            $sitemap->submitSitemap();
        ?>
    </body>
</html>

*/
