<?php require_once('E:\work\ofweek_hr\include\template_lite\plugins\modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  require_once('E:\work\ofweek_hr\include\template_lite\plugins\modifier.qishi_parse_url.php'); $this->register_modifier("qishi_parse_url", "tpl_modifier_qishi_parse_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2013-03-25 12:29 �й���׼ʱ�� */ ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="admin_main_nr_dbox">
<div class="pagetit">
	<div class="ptit"> <?php echo $this->_vars['pageheader']; ?>
</div>
  <div class="clear"></div>
</div>
<div class="seltpye_x">
		<div class="left">������ʾ</div>	
		<div class="right">
		<a href="<?php echo $this->_run_modifier("infotype:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['infotype'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("infotype:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['infotype'] == "1"): ?>class="select"<?php endif; ?>>���</a>
		<a href="<?php echo $this->_run_modifier("infotype:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['infotype'] == "2"): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("infotype:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['infotype'] == "3"): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("infotype:4", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['infotype'] == "4"): ?>class="select"<?php endif; ?>>Ͷ��</a>
		<a href="<?php echo $this->_run_modifier("infotype:9", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['infotype'] == "9"): ?>class="select"<?php endif; ?>>ϵͳ��Ϣ</a>
		<div class="clear"></div>
		</div>
		<div class="clear"></div>
</div>
<div class="seltpye_x">
		<div class="left">�Ƿ�ظ�</div>	
		<div class="right">
		<a href="<?php echo $this->_run_modifier("replyinfo:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['replyinfo'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("replyinfo:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['replyinfo'] == "1"): ?>class="select"<?php endif; ?>>δ�ظ�</a>
		<a href="<?php echo $this->_run_modifier("replyinfo:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['replyinfo'] == "2"): ?>class="select"<?php endif; ?>>�ѻظ�</a>
		<div class="clear"></div>
		</div>
		<div class="clear"></div>
</div>
<div class="seltpye_x">
		<div class="left">��Ա����</div>	
		<div class="right">
		<a href="<?php echo $this->_run_modifier("usertype:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['usertype'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("usertype:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['usertype'] == "1"): ?>class="select"<?php endif; ?>>��ҵ��Ա</a>
		<a href="<?php echo $this->_run_modifier("usertype:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['usertype'] == "2"): ?>class="select"<?php endif; ?>>���˻�Ա</a>
		<div class="clear"></div>
		</div>
		<div class="clear"></div>
</div>
 <div class="seltpye_x">
		<div class="left">���ʱ��</div>	
		<div class="right">
		<a href="<?php echo $this->_run_modifier("settr:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("settr:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "3"): ?>class="select"<?php endif; ?>>������</a>
		<a href="<?php echo $this->_run_modifier("settr:7", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "7"): ?>class="select"<?php endif; ?>>һ����</a>
		<a href="<?php echo $this->_run_modifier("settr:30", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "30"): ?>class="select"<?php endif; ?>>һ����</a>
		<a href="<?php echo $this->_run_modifier("settr:180", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "180"): ?>class="select"<?php endif; ?>>������</a>
		<a href="<?php echo $this->_run_modifier("settr:360", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "360"): ?>class="select"<?php endif; ?>>һ����</a>
		<div class="clear"></div>
		</div>
		<div class="clear"></div>
  </div>
  
  <form id="form1" name="form1" method="post" action="?act=del_feedback">
  <?php echo $this->_vars['inputtoken']; ?>

  <table width="100%" border="0" cellpadding="0" cellspacing="0"  id="list" class="link_lan">
    <tr>
      <td width="80"   class="admin_list_tit admin_list_first" >
     <label id="chkAll"><input type="checkbox" name=" " title="ȫѡ/��ѡ" id="chk"/>����</label></td>
      <td width="40%"  class="admin_list_tit"  >����</td>
      <td align="center"  class="admin_list_tit">�Ƿ�ظ�</td>
      <td align="center" class="admin_list_tit">������Ա</td>
      <td align="center"  class="admin_list_tit">��Ա����</td>
      <td width="120" align="center" class="admin_list_tit">���ʱ��</td>
      <td width="8%" align="center"  class="admin_list_tit">����</td>
    </tr>
	<?php if (count((array)$this->_vars['list'])): foreach ((array)$this->_vars['list'] as $this->_vars['li']): ?>
	<tr>
      <td   class="admin_list admin_list_first" >
      <input name="id[]" type="checkbox" id="id" value="<?php echo $this->_vars['li']['id']; ?>
"/> 
	   <?php if ($this->_vars['li']['infotype'] == "1"): ?>���<?php endif; ?>
		<?php if ($this->_vars['li']['infotype'] == "2"): ?>����<?php endif; ?>
		<?php if ($this->_vars['li']['infotype'] == "3"): ?>����<?php endif; ?>
		<?php if ($this->_vars['li']['infotype'] == "4"): ?>Ͷ��<?php endif; ?>	
		<?php if ($this->_vars['li']['infotype'] == "9"): ?>ϵͳ<?php endif; ?>	
	 </td>
      <td  class="admin_list vtip" title="<?php echo $this->_run_modifier($this->_vars['li']['feedback'], 'nl2br', 'PHP', 1); ?>
" ><?php echo $this->_vars['li']['title'];  if ($this->_vars['li']['file']): ?>&nbsp;&nbsp;<a href="/data/file/<?php echo $this->_vars['li']['file']; ?>
" target="_blank">�鿴ͼƬ</a><?php endif; ?></td>
      <td align="center"  class="admin_list">
	  <?php if ($this->_vars['li']['replyinfo'] == "1"): ?>δ�ظ�<?php else: ?><span style="color:#999999">�ѻظ�</span><?php endif; ?>	  </td>
      <td align="center" class="admin_list"><?php echo $this->_vars['li']['username']; ?>
</td>
      <td align="center"  class="admin_list">
	  <?php if ($this->_vars['li']['usertype'] == "1"): ?>��ҵ��Ա<?php endif; ?>
		<?php if ($this->_vars['li']['usertype'] == "2"): ?>���˻�Ա<?php endif; ?>	  </td>
      <td align="center" class="admin_list"><?php echo $this->_run_modifier($this->_vars['li']['addtime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
</td>
      <td align="center"  class="admin_list"><?php if ($this->_vars['li']['infotype'] != "9"): ?><a href="?act=reply_feedback&id=<?php echo $this->_vars['li']['id']; ?>
">�ظ�</a><?php endif; ?></td>
    </tr>
	 <?php endforeach; endif; ?>
  </table>
  <?php if (! $this->_vars['list']): ?>
<div class="admin_list_no_info">û���κ���Ϣ��</div>
<?php endif; ?>
<table width="100%" border="0" cellspacing="10" cellpadding="0" class="admin_list_btm">
      <tr>
        <td>
<input name="del" type="submit" class="admin_submit" id="ButDel" value="ɾ����ѡ"/>
		</td>
        <td width="305" align="right">
	    </td>
      </tr>
  </table>
  </form>
<div class="page link_bk"><?php echo $this->_vars['page']; ?>
</div>
</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>