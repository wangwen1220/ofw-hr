ALTER TABLE `hr_members` ADD `tmp_password` VARCHAR( 100 ) NOT NULL DEFAULT '' COMMENT '密码明文';
ALTER TABLE `hr_members` ADD `login_num` INT NOT NULL DEFAULT '0' COMMENT '登录次数';
ALTER TABLE `hr_members` ADD `p_interview_num` int(10) NOT NULL DEFAULT '0' COMMENT '个人-面试通知数（累加）';
ALTER TABLE `hr_members` ADD `p_apply_num` int(10) NOT NULL DEFAULT '0' COMMENT '个人-面试申请职位数（累加）';
ALTER TABLE `hr_members` ADD `c_interview_num` int(10) NOT NULL DEFAULT '0' COMMENT '公司-面试通知数（累加）';
ALTER TABLE `hr_members` ADD `c_resume_num` int(10) NOT NULL DEFAULT '0' COMMENT '公司-面试收到简历数（累加）';
ALTER TABLE `hr_members` ADD `c_down_num` int(10) NOT NULL DEFAULT '0' COMMENT '公司-面试下载简历数（累加）';

ALTER TABLE `hr_members` ADD `mail_refresh` TINYINT NOT NULL DEFAULT '0' COMMENT '简历刷新邮件次数';
ALTER TABLE `hr_members` ADD `mail_refresh_time` int(11) NOT NULL DEFAULT '0' COMMENT '简历刷新邮件次数';
ALTER TABLE `hr_members` ADD `mail_complete` TINYINT NOT NULL DEFAULT '0' COMMENT '简历完善邮件次数';
ALTER TABLE `hr_members` ADD `mail_complete_time` int(11) NOT NULL DEFAULT '0' COMMENT '简历完善邮件次数';

ALTER TABLE `hr_jobs` ADD `wage_min` INT NOT NULL DEFAULT '0' COMMENT '薪资范围起点';
ALTER TABLE `hr_jobs` ADD `wage_max` INT NOT NULL DEFAULT '0' COMMENT '薪资范围终点';
ALTER TABLE `hr_jobs` ADD `age_min` INT NOT NULL DEFAULT '0' COMMENT '年龄要求';
ALTER TABLE `hr_jobs` ADD `age_max` INT NOT NULL DEFAULT '0' COMMENT '年龄要求';
ALTER TABLE `hr_jobs` ADD `room` INT NOT NULL DEFAULT '0' COMMENT '提供住宿0不提供，1提供';
ALTER TABLE `hr_jobs` ADD `english` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '英语等级：0不限，1一般，2良好，3优秀';
ALTER TABLE `hr_jobs` ADD `computer` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '计算机能力：0不限，1一般，2良好，3优秀';
ALTER TABLE `hr_jobs` ADD `request` VARCHAR( 100 ) NOT NULL DEFAULT '' COMMENT '专业要求';
ALTER TABLE `hr_jobs` ADD `effect` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '有效期：0长期有效，1一个月，2三个月，3六个月，4一年';
ALTER TABLE `hr_jobs` ADD `cencus_province` INT( 10 ) NULL COMMENT '户籍省';
ALTER TABLE `hr_jobs` ADD `cencus_city` INT( 10 ) NULL COMMENT '户籍市';
ALTER TABLE `hr_jobs_tmp` ADD `wage_min` INT NOT NULL DEFAULT '0' COMMENT '薪资范围起点';
ALTER TABLE `hr_jobs_tmp` ADD `wage_max` INT NOT NULL DEFAULT '0' COMMENT '薪资范围终点';
ALTER TABLE `hr_jobs_tmp` ADD `age_min` INT NOT NULL DEFAULT '0' COMMENT '年龄要求';
ALTER TABLE `hr_jobs_tmp` ADD `age_max` INT NOT NULL DEFAULT '0' COMMENT '年龄要求';
ALTER TABLE `hr_jobs_tmp` ADD `room` INT NOT NULL DEFAULT '0' COMMENT '提供住宿0不提供，1提供';
ALTER TABLE `hr_jobs_tmp` ADD `english` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '英语等级：0不限，1一般，2良好，3优秀';
ALTER TABLE `hr_jobs_tmp` ADD `computer` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '计算机能力：0不限，1一般，2良好，3优秀';
ALTER TABLE `hr_jobs_tmp` ADD `request` VARCHAR( 100 ) NOT NULL DEFAULT '' COMMENT '专业要求';
ALTER TABLE `hr_jobs_tmp` ADD `effect` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '有效期：0长期有效，1一个月，2三个月，3六个月，4一年';
ALTER TABLE `hr_jobs_tmp` ADD `cencus_province` INT( 10 ) NULL COMMENT '户籍省';
ALTER TABLE `hr_jobs_tmp` ADD `cencus_city` INT( 10 ) NULL COMMENT '户籍市';


ALTER TABLE `hr_jobs` CHANGE `trade` `trade` VARCHAR( 100 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_jobs` CHANGE `trade_cn` `trade_cn` VARCHAR( 255 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_jobs` CHANGE `category` `category` VARCHAR( 100 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_jobs` CHANGE `category_cn` `category_cn` VARCHAR( 255 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;

ALTER TABLE `hr_jobs_tmp` CHANGE `trade` `trade` VARCHAR( 100 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_jobs_tmp` CHANGE `trade_cn` `trade_cn` VARCHAR( 255 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_jobs_tmp` CHANGE `category` `category` VARCHAR( 100 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_jobs_tmp` CHANGE `category_cn` `category_cn` VARCHAR( 255 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;

ALTER TABLE `hr_jobs` ADD `sort` INT NOT NULL DEFAULT '0' COMMENT '排序';
ALTER TABLE `hr_jobs` ADD `contents_tmp` VARCHAR( 1800 ) NOT NULL COMMENT '描述新' AFTER `contents` ;

ALTER TABLE `hr_jobs` ADD `tmp_id` INT NOT NULL DEFAULT '0' COMMENT 'jobID,301转向用';



ALTER TABLE `hr_company_profile` ADD `contents_tmp` TEXT NOT NULL COMMENT '公司介绍_改后';
ALTER TABLE `hr_company_profile` ADD `fax` VARCHAR( 130 ) NOT NULL COMMENT '传真';
ALTER TABLE `hr_company_profile` CHANGE `trade` `trade` VARCHAR( 100 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_company_profile` CHANGE `trade_cn` `trade_cn` VARCHAR( 255 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL;
ALTER TABLE `hr_company_profile` ADD `register_year` SMALLINT NOT NULL DEFAULT '0' COMMENT '注册年',
ADD `register_month` SMALLINT NOT NULL DEFAULT '0' COMMENT '注册月',
ADD `register_day` SMALLINT NOT NULL DEFAULT '0' COMMENT '注册日';
ALTER TABLE `hr_company_profile` CHANGE `audit` `audit` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '2'

ALTER TABLE `hr_resume` ADD `audit_reason` VARCHAR( 1000 ) NOT NULL COMMENT '审核不通过原因';

ALTER TABLE `hr_company_interview` ADD `interview_time` INT NOT NULL COMMENT '面试时间',
ADD `contact` VARCHAR( 80 ) NOT NULL COMMENT '联系人',
ADD `telephone` VARCHAR( 80 ) NOT NULL COMMENT '联系电话',
ADD `address` VARCHAR( 80 ) NOT NULL COMMENT '面试地址',
ADD `email` VARCHAR( 80 ) NOT NULL COMMENT '联系邮箱';
ALTER TABLE `hr_company_interview` CHANGE `notes` `notes` VARCHAR( 2000 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL ;
ALTER TABLE `hr_company_interview` ADD `delete` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '删除，1表示企业删掉了，2表示个人删掉了';

TRUNCATE TABLE `hr_article_category`;
INSERT INTO `hr_article_category` (`id`, `parentid`, `categoryname`, `category_order`, `title`, `description`, `keywords`, `admin_set`) VALUES
(1, 0, '职场动态', 0, '', '', '', 1),
(2, 0, '面试指南', 0, '', '', '', 1),
(3, 0, '简历指导', 0, '', '', '', 1),
(4, 0, '职业规划', 0, '', '', '', 1);
DELETE FROM `ofweekhr`.`hr_article_property` WHERE `hr_article_property`.`id` =2 LIMIT 1 ;

DELETE FROM `ofweekhr`.`hr_article_property` WHERE `hr_article_property`.`id` =3 LIMIT 1 ;


DELETE FROM `hr_category` WHERE `c_alias`='QS_trade';
REPLACE INTO `hr_category` (`c_id`, `c_parentid`, `c_alias`, `c_name`, `c_order`, `c_index`, `c_note`, `stat_jobs`, `stat_resume`) VALUES
(1, 0, 'QS_trade', 'LED照明', 0, '', '', '', ''),
(2, 0, 'QS_trade', '太阳能光伏', 0, '', '', '', ''),
(3, 0, 'QS_trade', '光通讯', 0, '', '', '', ''),
(4, 0, 'QS_trade', '激光', 0, '', '', '', ''),
(5, 0, 'QS_trade', '电子工程', 0, '', '', '', ''),
(6, 0, 'QS_trade', '工控', 0, '', '', '', ''),
(7, 0, 'QS_trade', '光电/光学', 0, '', '', '', ''),
(8, 0, 'QS_trade', '智能电网', 0, '', '', '', ''),
(9, 0, 'QS_trade', '仪表仪器', 0, '', '', '', ''),
(10, 0, 'QS_trade', '节能', 0, '', '', '', '');
REPLACE INTO `hr_category` (`c_id`, `c_parentid`, `c_alias`, `c_name`, `c_order`, `c_index`, `c_note`, `stat_jobs`, `stat_resume`) VALUES
(62, 0, 'QS_jobs_nature', '全职', 1, '', '', '', ''),
(63, 0, 'QS_jobs_nature', '兼职', 2, '', '', '', ''),
(64, 0, 'QS_jobs_nature', '实习', 4, '', '', '', ''),
(176, 0, 'QS_jobs_nature', '应届生', 3, '', '', '', '');

INSERT INTO `ofweekhr`.`hr_config` (
`id` ,
`name` ,
`value`
)
VALUES (
NULL , 'month_freesetmeal_num', '1000'
);

ALTER TABLE `hr_feedback` ADD `file` VARCHAR( 255 ) NOT NULL COMMENT '上传的附件';
ALTER TABLE `hr_feedback` ADD `title` VARCHAR( 1000 ) NOT NULL COMMENT '标题';
ALTER TABLE `hr_feedback` CHANGE `feedback` `feedback` VARCHAR( 2000 ) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL 
ALTER TABLE `hr_feedback` ADD `read` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '0未阅，1已阅';
ALTER TABLE `hr_jobs` CHANGE `subclass` `subclass` VARCHAR( 100 ) NOT NULL COMMENT '岗位父ID';
ALTER TABLE `hr_members_setmeal` ADD `resume_search` TINYINT NOT NULL DEFAULT '0' COMMENT '简历搜索权限';
ALTER TABLE `hr_setmeal` ADD `resume_search` TINYINT NOT NULL DEFAULT '0' COMMENT '简历搜索权限';



/**ZT 简历表字段添加**/
ALTER TABLE `hr_resume` ADD `nation` VARCHAR( 30 ) NULL COMMENT '民族',
ADD `cencus_province` INT( 1 ) NULL COMMENT '户籍省',
ADD `cencus_city` INT( 1 ) NULL COMMENT '户籍市',
ADD `english_ability` TINYINT( 1 ) NULL COMMENT '英语能力',
ADD `english_degree` TINYINT( 1 ) NULL COMMENT '英语证书等级',
ADD `otherlang` VARCHAR( 255 ) NULL COMMENT '其它外语情况',
ADD `computer_degree` TINYINT( 1 ) NULL COMMENT '电脑水平'

ALTER TABLE `hr_resume` ADD `workyear` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '工作年限' AFTER `district_cn` 

ALTER TABLE  `hr_resume_education` ADD  `content` VARCHAR( 2000 ) NOT NULL COMMENT  '内容', ADD  `status` TINYINT( 1 ) NOT NULL COMMENT  '区分新旧记录'

/**ZT 新加求职意向及特长表**/
CREATE TABLE `ofweekhr`.`hr_resume_intention` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`pid` INT( 10 ) NOT NULL COMMENT '简历表外键',
`uid` INT( 10 ) NOT NULL COMMENT '用户ID',
`industry` VARCHAR( 255 ) NOT NULL COMMENT '意向行业',
`category` VARCHAR( 255 ) NOT NULL COMMENT '意向岗位',
`district` VARCHAR( 255 ) NOT NULL COMMENT '意向地区',
`work_status` TINYINT( 1 ) NOT NULL COMMENT '求职状态',
`time_2work` TINYINT( 1 ) NOT NULL COMMENT '到岗期限',
`expected_salary` INT( 10 ) NOT NULL COMMENT '期望月薪',
`self_evaluation` TEXT NOT NULL COMMENT '自我评价',
`specialty` TEXT NOT NULL COMMENT '优势特长'
) ENGINE = MYISAM COMMENT = '求职意向及特长';

/**ZT 新加附件表**/
CREATE TABLE `ofweekhr`.`hr_resume_file` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`uid` INT( 10 ) NOT NULL COMMENT '用户名',
`resume_id` INT( 10 ) NOT NULL COMMENT '简历ID',
`name` VARCHAR( 50 ) NOT NULL COMMENT '文件名',
`type` TINYINT( 1 ) NOT NULL COMMENT '文件类型',
`path` VARCHAR( 100 ) NOT NULL COMMENT '文件路径',
`size` VARCHAR( 50 ) NOT NULL COMMENT '文件大小',
`ext` VARCHAR( 10 ) NOT NULL COMMENT '文件格式',
`upload_time` INT( 10 ) NOT NULL COMMENT '上传时间',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;

/**更改生日字体**/
ALTER TABLE `hr_resume` CHANGE `birthdate` `birthdate` INT( 10 ) UNSIGNED NOT NULL 
ALTER TABLE `hr_resume` ADD UNIQUE (
`uid`
)

/**简历表增加隐私控制字段**/
ALTER TABLE `hr_resume` ADD `hideall` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '隐藏简历',
ADD `hidesome` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '部分隐藏',
ADD `hidestr` TEXT NULL COMMENT '对包含此字符串的公司名隐藏'

/**计划任务删除***/
DELETE FROM `ofweekhr`.`hr_crons` WHERE `hr_crons`.`cronid` =1 LIMIT 1 ;

DELETE FROM `ofweekhr`.`hr_crons` WHERE `hr_crons`.`cronid` =2 LIMIT 1 ;

DELETE FROM `ofweekhr`.`hr_crons` WHERE `hr_crons`.`cronid` =4 LIMIT 1 ;

DELETE FROM `ofweekhr`.`hr_crons` WHERE `hr_crons`.`cronid` =5 LIMIT 1 ;

DELETE FROM `ofweekhr`.`hr_crons` WHERE `hr_crons`.`cronid` =6 LIMIT 1 ;
/**END**/

/**自我介绍及特长备份**/
ALTER TABLE `hr_resume_intention` ADD `self_evaluation_tmp` TEXT NOT NULL COMMENT '自我介绍备份' AFTER `self_evaluation` 
ALTER TABLE `hr_resume_intention` ADD `specialty_tmp` TEXT NOT NULL COMMENT '特长备份'
/**END**/

/*****/
ALTER TABLE `hr_personal_jobs_apply` ADD `education_cn` VARCHAR( 255 ) NOT NULL COMMENT '学历' AFTER `del` 
ALTER TABLE `hr_personal_jobs_apply` ADD `wage_cn` VARCHAR( 255 ) NOT NULL COMMENT '工资' AFTER `education_cn` 
ALTER TABLE `hr_personal_jobs_apply` ADD `experience_cn` VARCHAR( 255 ) NOT NULL COMMENT '工作经验' AFTER `wage_cn` 
/***END**/