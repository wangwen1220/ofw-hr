<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2013-03-25 12:29 中国标准时间 */ ?>

<div class="topnav">
<a href="admin_article.php?act=newslist" <?php if ($this->_vars['act'] == 'newslist'): ?>class="select"<?php endif; ?>><u>新闻列表</u></a>
<a href="admin_article.php?act=news_add" <?php if ($this->_vars['act'] == 'news_add'): ?>class="select"<?php endif; ?>><u>添加新闻</u></a> 
<a href="?act=category" <?php if ($this->_vars['act'] == "category" || $this->_vars['act'] == "category_add" || $this->_vars['act'] == "edit_category"): ?>class="select"<?php endif; ?>><u>新闻分类</u></a>
<div class="clear"></div>
</div>