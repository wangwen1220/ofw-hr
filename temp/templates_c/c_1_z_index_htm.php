<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2013-03-22 17:31 中国标准时间 */ ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="admin_main_nr_dbox">
<div class="pagetit">
	<div class="ptit">欢迎登录 管理中心！</div>
  <div class="clear"></div>
</div>
<span id="version"></span>

<div class="toptit">待处理事务</div>
<div style="margin-left:50px;">
	<ul>
	<li>昨日注册简历数：<a href="<?php echo $this->_vars['total_1_url']; ?>
"><?php echo $this->_vars['total_1']; ?>
</a></li>
	<li>今日注册简历数：<a href="<?php echo $this->_vars['total_2_url']; ?>
"><?php echo $this->_vars['total_2']; ?>
</a></li>
	<li>待审核简历数：<a href="<?php echo $this->_vars['total_3_url']; ?>
"><?php echo $this->_vars['total_3']; ?>
</a></li>
	<li>昨日注册企业数：<a href="<?php echo $this->_vars['total_4_url']; ?>
"><?php echo $this->_vars['total_4']; ?>
</a></li>
	<li>今日注册企业数：<a href="<?php echo $this->_vars['total_5_url']; ?>
"><?php echo $this->_vars['total_5']; ?>
</a></li>
	<li>本周投递简历数：<?php echo $this->_vars['total_6']; ?>
</li>
	<li>上周投递简历数：<?php echo $this->_vars['total_7']; ?>
</li>
	<li>待审核企业：<a href="<?php echo $this->_vars['total_8_url']; ?>
"><?php echo $this->_vars['total_8']; ?>
</a></li>
	<li>过期企业：<a href="<?php echo $this->_vars['total_9_url']; ?>
"><?php echo $this->_vars['total_9']; ?>
</a></li>
	<li>审核未通过企业：<a href="<?php echo $this->_vars['total_10_url']; ?>
"><?php echo $this->_vars['total_10']; ?>
</a></li>
	<li>昨日职位发布数：<a href="<?php echo $this->_vars['total_11_url']; ?>
"><?php echo $this->_vars['total_11']; ?>
</a></li>
	<li>今日职位发布数：<a href="<?php echo $this->_vars['total_12_url']; ?>
"><?php echo $this->_vars['total_12']; ?>
</a></li>
	<li>待审核职位：<a href="<?php echo $this->_vars['total_13_url']; ?>
"><?php echo $this->_vars['total_13']; ?>
</a></li>
	<li>过期职位数：<a href="<?php echo $this->_vars['total_14_url']; ?>
"><?php echo $this->_vars['total_14']; ?>
</a></li>
	<!-- 
	<li>本月开通的免费企业会员数：<a href="admin_set_com.php?act=set_meal"><?php echo $this->_vars['total_16']; ?>
</a></li>
	<li>本月可设置免费企业会员数：<a href="admin_set_com.php?act=set_meal"><?php echo $this->_vars['total_15']; ?>
</a></li>
	 -->
	</ul>
</div>

</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>