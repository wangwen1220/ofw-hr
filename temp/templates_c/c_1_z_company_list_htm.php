<?php require_once('E:\work\ofweek_hr\include\template_lite\plugins\modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2013-03-25 10:14 �й���׼ʱ�� */ ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="admin_main_nr_dbox">

<form id="Form1" name="Form1" action="company.php" method="get">
<input type="hidden" name="act" value="list" />
<?php if ($this->_vars['from'] == 'audit'): ?>
<input type="hidden" name="from" value="audit" />
<?php endif; ?>
<input type="hidden" name="num" value="<?php echo $this->_vars['num']; ?>
" />
<input type="hidden" name="page" value="1" />
<div id="search" style="margin-bottom:10px;">
	UID��<input type="text" name="uid" value="<?php echo $this->_vars['search']['uid']; ?>
" size="8"/>
	�û�����<input type="text" name="username" value="<?php echo $this->_vars['search']['username']; ?>
" size="8"/>
	��˾����<input type="text" name="companyname" value="<?php echo $this->_vars['search']['companyname']; ?>
" size="8"/>
	���״̬��
			<?php if ($this->_vars['from'] == 'audit'): ?>
			<select name="audit" disabled="disabled">
				<option value="0">ȫ��</option>
				<option value="1">���ͨ��</option>
				<option selected="selected" value="2">�ȴ����</option>
				<option value="3">���δͨ��</option>
			 </select>
			<?php else: ?>
			<select name="audit">
				<option value="0">ȫ��</option>
				<option<?php if ($this->_vars['search']['audit'] == 1): ?> selected="selected"<?php endif; ?> value="1">���ͨ��</option>
				<option<?php if ($this->_vars['search']['audit'] == 2): ?> selected="selected"<?php endif; ?> value="2">�ȴ����</option>
				<option<?php if ($this->_vars['search']['audit'] == 3): ?> selected="selected"<?php endif; ?> value="3">���δͨ��</option>
			 </select>
			<?php endif; ?>
	����״̬��<select name="expire">
				<option value="0">ȫ��</option>
				<option<?php if ($this->_vars['search']['expire'] == 1): ?> selected="selected"<?php endif; ?> value="1">�ѹ���</option>
				<option<?php if ($this->_vars['search']['expire'] == 2): ?> selected="selected"<?php endif; ?> value="2">δ����</option>
			 </select>
	��Ա���ͣ�<select name="setmeal">
				<option value="0">ȫ��</option>
				<?php if (count((array)$this->_vars['setmealCate'])): foreach ((array)$this->_vars['setmealCate'] as $this->_vars['list']): ?>
                <option<?php if ($this->_vars['search']['setmeal'] == $this->_vars['list']['id']): ?> selected="selected"<?php endif; ?> value="<?php echo $this->_vars['list']['id']; ?>
"><?php echo $this->_vars['list']['setmeal_name']; ?>
</option>
                <?php endforeach; endif; ?>
			 </select>
	��ҵ��<select name="trade">
				<option value="0">ȫ��</option>
				<?php if (count((array)$this->_vars['tradeCate'])): foreach ((array)$this->_vars['tradeCate'] as $this->_vars['list']): ?>
                <option<?php if ($this->_vars['search']['trade'] == $this->_vars['list']['c_id']): ?> selected="selected"<?php endif; ?> value="<?php echo $this->_vars['list']['c_id']; ?>
"><?php echo $this->_vars['list']['c_name']; ?>
</option>
                <?php endforeach; endif; ?>
			 </select><br /><br />
	ע��ʱ�䣺<input id="regtime_from" readonly name="regtime_from" value="<?php echo $this->_vars['search']['regtime_from']; ?>
" size="8"/>-<input id="regtime_to" readonly name="regtime_to" value="<?php echo $this->_vars['search']['regtime_to']; ?>
" size="8"/>
	����ʱ�䣺<input id="endtime_from" readonly name="endtime_from" value="<?php echo $this->_vars['search']['endtime_from']; ?>
" size="8"/>-<input id="endtime_to" readonly name="endtime_to" value="<?php echo $this->_vars['search']['endtime_to']; ?>
" size="8"/>
	��ҵ���ͣ�<select name="company_type">
				<option value="0">ȫ��</option>
				<option<?php if ($this->_vars['search']['company_type'] == 1): ?> selected="selected"<?php endif; ?> value="1">��ͨ</option>
				<option<?php if ($this->_vars['search']['company_type'] == 2): ?> selected="selected"<?php endif; ?> value="2">����</option>
			</select>
	<input type="submit" value="����" />
</div>
</form>

<form id="Form2" name="Form2" action="company.php?act=do" method="post">
<input type="hidden" name="optype" id="optype" value="" />
<input type="hidden" name="returnurl" id="returnurl" value="<?php echo $this->_vars['returnurl']; ?>
" />
<table width="100%" border="0" cellpadding="0" cellspacing="0"   class="link_lan">
	<tr>
		<td align="center" width="2%" class="admin_list_tit"><input type="checkbox" id="selectall1" title="ȫѡ/��ѡ" /></td>
		<td align="left" width="5%" class="admin_list_tit"><a href="<?php echo $this->_vars['sort_url']; ?>
uid">UID</a></td>
		<td align="left" width="13%" class="admin_list_tit">�û���</td>
		<td align="left" width="15%" class="admin_list_tit">��˾��</td>
		<td align="left" width="8%" class="admin_list_tit">��˾����</td>	
		<td align="left" width="10%" class="admin_list_tit">��ϵ��</td>
		<td align="left" width="5%" class="admin_list_tit">����</td>
		<td align="left" width="11%" class="admin_list_tit">��ϵ�绰</td> 
		<td align="left" width="10%" class="admin_list_tit">ע��ʱ��</td>
		<td align="left" width="10%" class="admin_list_tit">����ʱ��</td>
		<td align="left" width="8%" class="admin_list_tit">��˾״̬</td>
		<td align="left" width="8%" class="admin_list_tit">����</td>
	</tr>
	<?php if (count((array)$this->_vars['companyList'])): foreach ((array)$this->_vars['companyList'] as $this->_vars['list']): ?>
	<tr>
		<td  class="admin_list" align="center"><input type="checkbox" name="uid[]" id="uid_<?php echo $this->_vars['list']['uid']; ?>
" value="<?php echo $this->_vars['list']['uid']; ?>
"/></td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['uid']; ?>
</td>
		<td  class="admin_list" align="left"><span style="display:block;width:117px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_run_modifier($this->_vars['list']['username'], 'truncate', 'plugin', 1, 20, ".."); ?>
"><?php echo $this->_run_modifier($this->_vars['list']['username'], 'truncate', 'plugin', 1, 20, ".."); ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;float:left;width:130px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_run_modifier($this->_vars['list']['companyname'], 'truncate', 'plugin', 1, 20, ".."); ?>
"><?php echo $this->_run_modifier($this->_vars['list']['companyname'], 'truncate', 'plugin', 1, 20, ".."); ?>
</span><?php if ($this->_vars['list']['certificate_img']): ?><img style="float:left;" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/b_linsence.gif" /><?php endif; ?></td>
		<td  class="admin_list" align="left"><span style="display:block;width:70px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['nature_cn']; ?>
"><?php echo $this->_vars['list']['nature_cn']; ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:100px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['contact']; ?>
"><?php echo $this->_vars['list']['contact']; ?>
</span></td>
		<td  class="admin_list" align="left"><span><?php if ($this->_vars['list']['company_type'] == 2): ?><span class="f_gre">����</span><?php else: ?>��ͨ<?php endif; ?></span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:105px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_run_modifier($this->_vars['list']['telephone'], 'truncate', 'plugin', 1, 16, ".."); ?>
"><?php echo $this->_run_modifier($this->_vars['list']['telephone'], 'truncate', 'plugin', 1, 16, ".."); ?>
</span></td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['reg_time']; ?>
</td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['endtime']; ?>
</td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['audit_cn']; ?>
</td>
		<td  class="admin_list" align="left"><a href="company.php?act=setmeal&uid=<?php echo $this->_vars['list']['uid']; ?>
"><?php echo $this->_vars['list']['setmeal_name_cn']; ?>
</a>&nbsp;&nbsp;<a href="company.php?act=edit&uid=<?php echo $this->_vars['list']['uid']; ?>
">�༭</a>&nbsp;&nbsp;<a href="company.php?act=management&uid=<?php echo $this->_vars['list']['uid']; ?>
" target="_blank">�����Ա����</a></td>
	</tr>
	<?php endforeach; endif; ?>
</table>
<div style="margin-top:10px;margin-bottom:10px;">��<?php echo $this->_vars['total']; ?>
����¼&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_vars['pageHTML']; ?>
&nbsp;&nbsp;<select id="num"><option<?php if ($this->_vars['num'] == 10): ?> selected="selected"<?php endif; ?> value="10">10</option><option<?php if ($this->_vars['num'] == 20): ?> selected="selected"<?php endif; ?> value="20">20</option><option<?php if ($this->_vars['num'] == 30): ?> selected="selected"<?php endif; ?> value="30">30</option><option<?php if ($this->_vars['num'] == 60): ?> selected="selected"<?php endif; ?> value="60">60</option></select></div>
<div><input type="button" id="btn_audit_1" value="���ͨ��" />&nbsp;&nbsp;<input type="button" id="btn_audit_3" value="��˲�ͨ��" />&nbsp;&nbsp;<input type="button" id="btn_del" value="ɾ��" /></div>
</form>
</div>

<script>
$(document).ready(function(){
	$("#num").change(function(){
		var pl=$(this).children('option:selected').val();
		window.location.href="<?php echo $this->_vars['num_url']; ?>
"+pl;
	});
	$("#selectall1").click(function(){
		$("input[name='uid[]']").attr("checked",$(this).attr("checked"));
	});	
	$("#btn_audit_1").click(function(){
		$("#optype").val('audit_1');
		$("#Form2").submit();
	});
	$("#btn_audit_3").click(function(){
		$("#optype").val('audit_3');
		$("#Form2").submit();
	});
	$("#btn_del").click(function(){
		if(confirm("�û���Ϣ�ᱻ����ɾ����ȷ��ɾ����?")) {
			$("#optype").val('del');
			$("#Form2").submit();
		}
	});

	$(function(){
		$("#regtime_from").date_input();
		$("#regtime_to").date_input();
		$("#endtime_from").date_input();
		$("#endtime_to").date_input();
	});
});
</script>

</body>
</html>


<script>
var t=<?php echo $this->_vars['audit2_com_num']; ?>
;
if(t){var s='('+t+')';$("#leftFrame").contents().find("#audit2_com_num").html(s);}

</script>