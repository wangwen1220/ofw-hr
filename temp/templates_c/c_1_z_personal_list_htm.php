<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2013-03-22 17:31 中国标准时间 */ ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="admin_main_nr_dbox">

<form id="Form1" name="Form1" action="personal.php" method="get">
<input type="hidden" name="act" value="list" />
<?php if ($this->_vars['from'] == 'audit'): ?>
<input type="hidden" name="from" value="audit" />
<?php endif; ?>
<input type="hidden" name="num" value="<?php echo $this->_vars['num']; ?>
" />
<input type="hidden" name="page" value="1" />
<div id="search" style="margin-bottom:10px;">
	UID：<input type="text" name="uid" value="<?php echo $this->_vars['search']['uid']; ?>
" size="8"/>
	用户名：<input type="text" name="username" value="<?php echo $this->_vars['search']['username']; ?>
" size="8"/>
	姓名：<input type="text" name="fullname" value="<?php echo $this->_vars['search']['fullname']; ?>
" size="8"/>
	性别：<select name="sex">
				<option>全部</option>
				<option<?php if ($this->_vars['search']['sex'] == 1): ?> selected="selected"<?php endif; ?> value="1">男</option>
				<option<?php if ($this->_vars['search']['sex'] == 2): ?> selected="selected"<?php endif; ?> value="2">女</option>
			 </select>	
	注册时间：<input id="regtime_from" readonly name="regtime_from" value="<?php echo $this->_vars['search']['regtime_from']; ?>
" size="8"/>-<input id="regtime_to" readonly name="regtime_to" value="<?php echo $this->_vars['search']['regtime_to']; ?>
" size="8"/>
	更新时间：<input id="refreshtime_from" readonly name="refreshtime_from" value="<?php echo $this->_vars['search']['refreshtime_from']; ?>
" size="8"/>-<input id="refreshtime_to" readonly name="refreshtime_to" value="<?php echo $this->_vars['search']['refreshtime_to']; ?>
" size="8"/><br /><br />
	简历完善：<select name="complete">
				<option>全部</option>
				<option<?php if ($this->_vars['search']['complete'] == 1): ?> selected="selected"<?php endif; ?> value="1">完善</option>
				<option<?php if ($this->_vars['search']['complete'] == 2): ?> selected="selected"<?php endif; ?> value="2">不完善</option>
			 </select>
	简历状态：
			<?php if ($this->_vars['from'] == 'audit'): ?>
			<select name="audit" disabled="disabled">
				<option>全部</option>
				<option value="1">审核通过</option>
				<option selected="selected" value="2">等待审核</option>
				<option value="3">审核未通过</option>
			 </select>
			<?php else: ?>
			<select name="audit">
				<option>全部</option>
				<option<?php if ($this->_vars['search']['audit'] == 1): ?> selected="selected"<?php endif; ?> value="1">审核通过</option>
				<option<?php if ($this->_vars['search']['audit'] == 2): ?> selected="selected"<?php endif; ?> value="2">等待审核</option>
				<option<?php if ($this->_vars['search']['audit'] == 3): ?> selected="selected"<?php endif; ?> value="3">审核未通过</option>
			 </select>
			<?php endif; ?>
	目前行业：<select name="trade">
				<option value="0">全部</option>
				<?php if (count((array)$this->_vars['tradeCate'])): foreach ((array)$this->_vars['tradeCate'] as $this->_vars['list']): ?>
                <option<?php if ($this->_vars['search']['trade'] == $this->_vars['list']['c_id']): ?> selected="selected"<?php endif; ?> value="<?php echo $this->_vars['list']['c_id']; ?>
"><?php echo $this->_vars['list']['c_name']; ?>
</option>
                <?php endforeach; endif; ?>
			 </select>		
	学历：<select name="education">
				<option>全部</option>
				<?php if (count((array)$this->_vars['eduCate'])): foreach ((array)$this->_vars['eduCate'] as $this->_vars['list']): ?>
				<option<?php if ($this->_vars['search']['education'] == $this->_vars['list']['c_id']): ?> selected="selected"<?php endif; ?> value="<?php echo $this->_vars['list']['c_id']; ?>
"><?php echo $this->_vars['list']['c_name']; ?>
</option>
				<?php endforeach; endif; ?>
			 </select>
	经验：<select name="experience">
						<option value="0">不限</option>
						<option<?php if ($this->_vars['search']['experience'] == 1): ?> selected="selected"<?php endif; ?> value="1" label="无经验">无经验</option>
						<option<?php if ($this->_vars['search']['experience'] == 2): ?> selected="selected"<?php endif; ?> value="2" label="1年以下">1年以下</option>
						<option<?php if ($this->_vars['search']['experience'] == 3): ?> selected="selected"<?php endif; ?> value="3" label="1-3年">1-3年</option>
						<option<?php if ($this->_vars['search']['experience'] == 4): ?> selected="selected"<?php endif; ?> value="4" label="3-5年">3-5年</option>
						<option<?php if ($this->_vars['search']['experience'] == 5): ?> selected="selected"<?php endif; ?> value="5" label="5-10年">5-10年</option>
						<option<?php if ($this->_vars['search']['experience'] == 6): ?> selected="selected"<?php endif; ?> value="6" label="10年以上">10年以上</option>
                                </select> 
			 
			 
	年龄：<input name="age_from" value="<?php echo $this->_vars['search']['age_from']; ?>
" size="2"/>-<input name="age_to" value="<?php echo $this->_vars['search']['age_to']; ?>
" size="2"/>
	来源：<select name="operatetype">
				<option value="0">不限</option>
				<option<?php if ($this->_vars['search']['operatetype'] == 2): ?> selected="selected"<?php endif; ?> value="2">OFweek会员</option>
				<option<?php if ($this->_vars['search']['operatetype'] == 1): ?> selected="selected"<?php endif; ?> value="1">人才网注册</option>
			</select>
	<input type="submit" value="搜索" />
</div>
</form>

<form id="Form2" name="Form2" action="personal.php?act=do" method="post">
<input type="hidden" name="optype" id="optype" value="" />
<input type="hidden" name="returnurl" id="returnurl" value="<?php echo $this->_vars['returnurl']; ?>
" />
<table width="100%" border="0" cellpadding="0" cellspacing="0"   class="link_lan">
	<tr>
		<td align="center" width="4%" class="admin_list_tit"><input type="checkbox" id="selectall1" title="全选/反选" /></td>
		<td align="left" width="4%" class="admin_list_tit"><a href="<?php echo $this->_vars['sort_url']; ?>
uid">UID</a></td>
		<td align="left" width="8%" class="admin_list_tit">用户名</td>
		<td align="left" width="8%" class="admin_list_tit">姓名</td>
		<td align="left" width="10%" class="admin_list_tit">简历完善度</td>
		<td align="left" width="10%" class="admin_list_tit">简历状态</td> 
		<td align="left" width="10%" class="admin_list_tit">学历</td>
		<td align="left" width="10%" class="admin_list_tit">经验</td>
		<td align="left" width="6%" class="admin_list_tit">年龄</td>
		<td align="left" width="10%" class="admin_list_tit">注册时间</td>	
		<td align="left" width="10%" class="admin_list_tit">更新时间</td>	
		<td align="center" width="10%" class="admin_list_tit">操作</td>
	</tr>
	<?php if (count((array)$this->_vars['resumeList'])): foreach ((array)$this->_vars['resumeList'] as $this->_vars['list']): ?>
	<tr>
		<td  class="admin_list" align="center"><input type="checkbox" name="uid[]" id="uid_<?php echo $this->_vars['list']['uid']; ?>
" value="<?php echo $this->_vars['list']['uid']; ?>
"/></td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['uid']; ?>
</td>
		<td  class="admin_list" align="left"><span style="display:block;width:77px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['username']; ?>
"><?php echo $this->_vars['list']['username']; ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:75px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['fullname']; ?>
"><?php echo $this->_vars['list']['fullname']; ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:100px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['complete_cn']; ?>
"><?php echo $this->_vars['list']['complete_cn']; ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:97px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" ><?php echo $this->_vars['list']['audit_cn']; ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:93px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['education_cn']; ?>
"><?php echo $this->_vars['list']['education_cn']; ?>
</span></td>
		<td  class="admin_list" align="left"><span style="display:block;width:93px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $this->_vars['list']['experience_cn']; ?>
"><?php echo $this->_vars['list']['experience_cn']; ?>
</span></td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['birthdate']; ?>
</td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['reg_time']; ?>
</td>
		<td  class="admin_list" align="left"><?php echo $this->_vars['list']['refreshtime']; ?>
</td>
		<td  class="admin_list" align="center"><a href="/user/resume.php?uid=<?php echo $this->_vars['list']['uid']; ?>
" target="_blank">查看简历</a>&nbsp;&nbsp;<a href="personal.php?act=edit&uid=<?php echo $this->_vars['list']['uid']; ?>
" target="_blank">编辑</a>&nbsp;&nbsp;<a href="personal.php?act=management&uid=<?php echo $this->_vars['list']['uid']; ?>
" target="_blank">进入会员中心</a></td>
	</tr>
	<?php endforeach; endif; ?>
</table>
<div style="margin-top:10px;margin-bottom:10px;">共<?php echo $this->_vars['total']; ?>
条记录&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_vars['pageHTML']; ?>
&nbsp;&nbsp;<select id="num"><option<?php if ($this->_vars['num'] == 10): ?> selected="selected"<?php endif; ?> value="10">10</option><option<?php if ($this->_vars['num'] == 20): ?> selected="selected"<?php endif; ?> value="20">20</option><option<?php if ($this->_vars['num'] == 30): ?> selected="selected"<?php endif; ?> value="30">30</option><option<?php if ($this->_vars['num'] == 60): ?> selected="selected"<?php endif; ?> value="60">60</option></select></div>
<div><input type="button" id="btn_audit_1" value="审核通过" />&nbsp;&nbsp;<input type="button" id="btn_audit_3" value="审核不通过" />&nbsp;&nbsp;<input type="button" id="btn_del" value="删除" /></div>
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
		if(confirm("用户信息会被彻底删除，确定删除吗?")) {
			$("#optype").val('del');
			$("#Form2").submit();
		}
	});

	$(function(){
		$("#regtime_from").date_input();
		$("#regtime_to").date_input();
		$("#refreshtime_from").date_input();
		$("#refreshtime_to").date_input();
	});
});
</script>

</body>
</html>