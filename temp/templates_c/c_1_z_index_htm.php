<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2013-03-22 17:31 �й���׼ʱ�� */ ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="admin_main_nr_dbox">
<div class="pagetit">
	<div class="ptit">��ӭ��¼ �������ģ�</div>
  <div class="clear"></div>
</div>
<span id="version"></span>

<div class="toptit">����������</div>
<div style="margin-left:50px;">
	<ul>
	<li>����ע���������<a href="<?php echo $this->_vars['total_1_url']; ?>
"><?php echo $this->_vars['total_1']; ?>
</a></li>
	<li>����ע���������<a href="<?php echo $this->_vars['total_2_url']; ?>
"><?php echo $this->_vars['total_2']; ?>
</a></li>
	<li>����˼�������<a href="<?php echo $this->_vars['total_3_url']; ?>
"><?php echo $this->_vars['total_3']; ?>
</a></li>
	<li>����ע����ҵ����<a href="<?php echo $this->_vars['total_4_url']; ?>
"><?php echo $this->_vars['total_4']; ?>
</a></li>
	<li>����ע����ҵ����<a href="<?php echo $this->_vars['total_5_url']; ?>
"><?php echo $this->_vars['total_5']; ?>
</a></li>
	<li>����Ͷ�ݼ�������<?php echo $this->_vars['total_6']; ?>
</li>
	<li>����Ͷ�ݼ�������<?php echo $this->_vars['total_7']; ?>
</li>
	<li>�������ҵ��<a href="<?php echo $this->_vars['total_8_url']; ?>
"><?php echo $this->_vars['total_8']; ?>
</a></li>
	<li>������ҵ��<a href="<?php echo $this->_vars['total_9_url']; ?>
"><?php echo $this->_vars['total_9']; ?>
</a></li>
	<li>���δͨ����ҵ��<a href="<?php echo $this->_vars['total_10_url']; ?>
"><?php echo $this->_vars['total_10']; ?>
</a></li>
	<li>����ְλ��������<a href="<?php echo $this->_vars['total_11_url']; ?>
"><?php echo $this->_vars['total_11']; ?>
</a></li>
	<li>����ְλ��������<a href="<?php echo $this->_vars['total_12_url']; ?>
"><?php echo $this->_vars['total_12']; ?>
</a></li>
	<li>�����ְλ��<a href="<?php echo $this->_vars['total_13_url']; ?>
"><?php echo $this->_vars['total_13']; ?>
</a></li>
	<li>����ְλ����<a href="<?php echo $this->_vars['total_14_url']; ?>
"><?php echo $this->_vars['total_14']; ?>
</a></li>
	<!-- 
	<li>���¿�ͨ�������ҵ��Ա����<a href="admin_set_com.php?act=set_meal"><?php echo $this->_vars['total_16']; ?>
</a></li>
	<li>���¿����������ҵ��Ա����<a href="admin_set_com.php?act=set_meal"><?php echo $this->_vars['total_15']; ?>
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