////////////////////////////////////////////////////////////////////////////////
//	名称: OFweek 人才网职位搜索程序
//	维护: 王文 wangwen1220@139.com
//	说明: 依赖 jQuery 1.4.2 及以上版本
//	日期: 2012-11-26 星期一
////////////////////////////////////////////////////////////////////////////////
(function($) {
	/*====================常用工具=====================*/
	// IE 版本判断
	var isIE = !!window.ActiveXObject,
		isIE6 = isIE && !window.XMLHttpRequest;

	// 提示信息
	function log(msg) { window.console && console.log(msg) }

	$(function() {
		/*====================通用动作=====================*/
		// 文本框获得或失去焦点
		$('.search-wd').focus(function() {
			$(this).addClass("focus");
			if ($(this).val() == this.defaultValue) {
				$(this).val("");
			}
		}).blur(function() {
			$(this).removeClass("focus");
			if ($(this).val() == '') {
				$(this).val(this.defaultValue);
			}
		});

		// 查找关键词不能为空
		$('form.search').submit(function() {
			var $search_wd = $(this).find('.search-wd'),
				wd = $search_wd.val();
			if ($.trim(wd) == '' || wd == $search_wd[0].defaultValue) {
				alert('请输入你要查找的关键词！');
				return false;
			}
		});

		/*====================意向岗位弹窗=====================*/
		var $gw_list = $('#gw-list'),
			$gw_sublist = $('#gw-sublist'),
			$gw_selected = $('#gw-selected');
			$gw_err = $('#gw-err');
		$gw_list.find("a").live('click', function() {
			var $ths = $(this),
				id = $ths.attr('data-id'),
				pos = $ths.position(),
				left = pos.left,
				top = pos.top;
			$ths.addClass('cur');
			// 加载列表数据
			$.get('/plus/ajax_zt.php?action=navisub&id=' + id, function(d) {
				$gw_sublist.html(d);
				$gw_selected.find('.gw-selected-item').each(function() {
					$gw_sublist.find('#' + $(this).attr('data-id')).attr('checked', true);
					if ($gw_sublist.find('.chooseall input').attr('checked')) {
						$gw_sublist.find('.chooseall').next().find('input').attr('checked', true).attr('disabled', true);
					}
				});
			});
			if (left > 300) {
				$gw_sublist.css({display: 'block', left: 'auto', right: $('#popDiv1').innerWidth() - left - $ths.outerWidth(), top: top + 22});
			} else {
				$gw_sublist.css({display: 'block', left: left, top: top + 20});
			}
			return false;
		}).mouseleave(function(e) {
			//console.log(e.relatedTarget.id);
			//if ($(e.relatedTarget).is($('#gw-sublist')) || $gw_sublist.find(e.relatedTarget).length) {
			// 这里本来用 $gw_sublist.is(e.relatedTarget) 就可以检测出，但是这个版本的 jQuery 有 BUG 检测不出来
			if (e.relatedTarget.id == 'gw-sublist' || $gw_sublist.find(e.relatedTarget).length) return;
			var $ths = $(this);
			$ths.removeClass('cur');
			$gw_sublist.hide().html("<div class='data-loading'></div>");
			$gw_err.hide();
		}).live('mouseenter', function() {
			var $ths = $(this);
			if (!$ths.hasClass('cur')) {
				$ths.siblings().removeClass('cur');
				$gw_sublist.hide().html("<div class='data-loading'></div>");
				$gw_err.hide();
			}
		});
		$gw_sublist.mouseleave(function(e) { // 鼠标移到子弹窗上
			if (e.relatedTarget.className == 'cur') return;
			$gw_list.find("a").removeClass('cur');
			$gw_sublist.hide().html("<div class='data-loading'></div>");
			$gw_err.hide();
		}).find('input').live('click', function(e) { // 选择子弹窗岗位
			var $ths = $(this),
				$sublist_item = $ths.parent().parent(),
				text = $ths.parent().text(),
				val = $ths.val(),
				id = $ths.attr('id');
			if ($gw_selected.find('[data-id =' + id + ']').length) {
				$ths.attr('checked', false);
				$gw_selected.find('[data-id =' + id + ']').remove();
				if ($sublist_item.hasClass('chooseall')) {
					$sublist_item.next().find('input').attr('checked', false).attr('disabled', false);
				}
				$gw_err.hide();
			} else if ($gw_selected.find('.gw-selected-item').length >= 5) {
				$gw_err.show().text('最多只能选择5项');
				e.preventDefault();
			} else {
				$ths.attr('checked', true);
				$gw_selected.append("<div class='gw-selected-item' data-id='" + id + "' data-val='" + val + "'><span class='text'>" + text + "</span><a class='closer' title='点击取消' href='javascript:;'>X</a></div>");
				if ($sublist_item.hasClass('chooseall')) {
					$sublist_item.next().find('input').each(function() {
						var $ths = $(this),
							id = this.id;
						$ths.attr('checked', true).attr('disabled', true);
						$gw_selected.find('[data-id =' + id + ']').remove();
					});
				}
				$gw_err.hide();
			}
			if ($gw_list.find("a.cur").length) $gw_sublist.css({top: $gw_list.find("a.cur").position().top + 22}); // 重定位
		});

		// 已选岗位关闭按钮
		$gw_selected.find('.closer').live('click', function() {
			$(this).parent().remove();
		});

		// 搜索岗位
		var defaut_html = $gw_list.html();
		$('#gw-search').find('.search-wd').keyup(function() {
			var wd = $.trim(this.value),
				$closer = $(this).next('.closer');
			if (wd) {
				$.get('/plus/ajax_zt.php?action=navisug&key=' + encodeURIComponent(wd), function(d) {
					if (d === '' || d == 'null' || d === '<div id="gw-search-resault" class="gw-search-resault fn-clear"></div>') {
						$gw_list.html(defaut_html);
						$gw_err.show();
					} else {
						$gw_list.html(d);
						$gw_err.hide();
					}
				});
				$closer.addClass('active');
			} else {
				$gw_list.html(defaut_html);
				$closer.removeClass('active');
				$gw_err.hide();
			}
		});

		$('#gw-search-resault').find('input').live('click', function(e) { // 选择子弹窗岗位
			var $ths = $(this),
				$sublist_item = $ths.parent().parent(),
				text = $ths.parent().text(),
				val = $ths.val(),
				id = $ths.attr('id');
			if ($gw_selected.find('[data-id =' + id + ']').length) {
				$ths.attr('checked', false);
				$gw_selected.find('[data-id =' + id + ']').remove();
				if ($sublist_item.hasClass('gw-search-resault-group-all')) {
					$sublist_item.next().find('input').attr('checked', false).attr('disabled', false);
				}
				$gw_err.hide();
			} else if ($gw_selected.find('.gw-selected-item').length >= 5) {
				$gw_err.show().text('最多只能选择5项');
				e.preventDefault();
			} else {
				$ths.attr('checked', true);
				$gw_selected.append("<div class='gw-selected-item' data-id='" + id + "' data-val='" + val + "'><span class='text'>" + text + "</span><a class='closer' title='点击取消' href='javascript:;'>X</a></div>");
				if ($sublist_item.hasClass('gw-search-resault-group-all')) {
					$sublist_item.next().find('input').each(function() {
						var $ths = $(this),
							id = this.id;
						$ths.attr('checked', true).attr('disabled', true);
						$gw_selected.find('[data-id =' + id + ']').remove();
					});
				}
				$gw_err.hide();
			}
			$gw_sublist.css({top: $gw_list.find("a.cur").position().top + 22}); // 重定位
		});

		// 清除搜索框
		$('#gw-search').find('.closer').click(function() {
			$('#gw-search').find('.search-wd').val('').keyup().blur();
			$gw_list.html(defaut_html);
		});

		$('#search_form .gw-select').click(function() {
			$('#gw-search').find('.closer').click();
		});

		// 清除窗口数据
		$('#gw-dialog-clear').live('click', function() {
			$gw_selected.empty();
			$('#gw-search').find('.closer').click();
			//$('#gw-search').find('.search-wd').val($('#gw-search').find('.search-wd')[0].defaultValue);
			//$gw_err.hide();
		});

		// 关闭窗口
		$('#gw-dialog-close').live('click', function() {
			$('#gw-search').find('.closer').click();
			closeDiv1();
		});

		// 提交窗口数据
		$('#gw-dialog-submit').live('click', function() {
			var text = '';
			$('#search_form').find('.data-gw').remove();
			$gw_selected.find('.gw-selected-item').each(function(i) {
				var $ths = $(this),
					val = $ths.attr('data-val'),
					txt = $ths.find('.text').text();
				text += txt;
				if (i < $gw_selected.find('.gw-selected-item').length - 1) text += '－';
				if (val) {
					$('#search_form').prepend('<input type="hidden" class="data-gw" name="category[' + i + ']" value="' + val + '" />');
				}
			});
			if (text) {
				var title = text;
				var i = $('#search_form').find('.jobs-select').length ? 4 : 13;
				text = text.length > i ? text.substring(0, i) + '...' : text.substring(0, i);
				$('#search_form').find('.gw-select').text(text).attr('title', title);
			} else {
				$('#search_form').find('.gw-select').text('意向岗位');
			}
			$('#gw-dialog-close').click();
			//$('#gw-search').find('.closer').click();
			//closeDiv1();
		});

		/*====================意向地区弹窗=====================*/
		// 城市次级列表弹窗
		var $area_list = $('#area-list'),
			$area_sublist = $('#area-sublist'),
			$area_selected = $('#area-selected');
			$area_err = $('#area-err');
		$area_list.find('a').live('click', function() {
			var $ths = $(this),
				id = $ths.attr('data-id'),
				pos = $ths.position(),
				left = pos.left,
				top = pos.top;
			if ($ths.hasClass('no-more')) {
				var id = $ths.attr('data-id'),
					text = $ths.text();
				if ($area_selected.find('[data-id =cbx-' + id + ']').length) {
					$area_selected.find('[data-id =cbx-' + id + ']').remove();
					$area_err.hide();
				} else if ($area_selected.find('.area-selected-item').length >= 3) {
					$area_err.show().text('最多只能选择3项');
				} else {
					$area_selected.append("<div class='area-selected-item' data-id='cbx-" + id + "' data-val='" + id + "'><span class='text'>" + text + "</span><a class='closer' title='点击取消' href='javascript:;'>X</a></div>");
					$area_err.hide();
				}
			} else {
				$ths.addClass('cur');
				// 加载列表数据
				$.get('/plus/ajax_zt.php?action=navidis&id=' + id, function(d) {
					$area_sublist.html(d);
					$area_selected.find('.area-selected-item').each(function() {
						$area_sublist.find('#' + $(this).attr('data-id')).attr('checked', true);
						if ($area_sublist.find('.chooseall input').attr('checked')) {
							$area_sublist.find('.chooseall').next().find('input').attr('checked', true).attr('disabled', true);
						}
					});
				});
				if (left > 260) {
					$area_sublist.css({display: 'block', left: 'auto', right: $('#popDiv').innerWidth() - left - $ths.outerWidth(), top: top + 20});
				} else {
					$area_sublist.css({display: 'block', left: left, top: top + 20});
				}
			}
			return false;
		}).mouseleave(function(e) {
			if (e.relatedTarget.id == 'area-sublist' || $area_sublist.find(e.relatedTarget).length) return;
			var $ths = $(this);
			$ths.removeClass('cur');
			$area_sublist.hide().html("<div class='data-loading'></div>");
			$area_sublist.hide();
			$area_err.hide();
		});

		$area_sublist.mouseleave(function(e) { // 鼠标移到子弹窗上
			if (e.relatedTarget.className == 'cur') return;
			$area_list.find("a").removeClass('cur');
			$area_sublist.hide().html("<div class='data-loading'></div>");
			$area_sublist.hide();
			$area_err.hide();
		}).find('input').live('click', function(e) { // 选择子弹窗岗位
			var $ths = $(this),
				$sublist_item = $ths.parent().parent(),
				val = $ths.val(),
				text = $ths.parent().text(),
				id = $ths.attr('id');
			if ($area_selected.find('[data-id =' + id + ']').length) {
				$ths.attr('checked', false);
				$area_selected.find('[data-id =' + id + ']').remove();
				if ($sublist_item.hasClass('chooseall')) {
					$sublist_item.next().find('input').attr('checked', false).attr('disabled', false);
				}
				$area_err.hide();
			} else if ($area_selected.find('.area-selected-item').length >= 3) {
				$area_err.show().text('最多只能选择3项');
				e.preventDefault();
			} else {
				$ths.attr('checked', true);
				$area_selected.append("<div class='area-selected-item' data-id='" + id + "' data-val='" + val + "'><span class='text'>" + text + "</span><a class='closer' title='点击取消' href='javascript:;'>X</a></div>");
				if ($sublist_item.hasClass('chooseall')) {
					$sublist_item.next().find('input').each(function() {
						var $ths = $(this),
							id = this.id;
						$ths.attr('checked', true).attr('disabled', true);
						$area_selected.find('[data-id =' + id + ']').remove();
					});
				}
				$area_err.hide();
			}
			if ($area_list.find("a.cur").length) $area_sublist.css({top: $area_list.find("a.cur").position().top + 22}); // 重定位
		});

		// 已选地区关闭按钮
		$area_selected.find('.closer').live('click', function() {
			$(this).parent().remove();
		});

		// 清除窗口数据
		$('#area-dialog-clear').live('click', function() {
			$area_selected.empty();
			//$area_err.hide();
		});

		// 提交窗口数据
		$('#area-dialog-submit').live('click', function() {
			var text = '';
			$('#search_form').find('.data-area').remove();
			$area_selected.find('.area-selected-item').each(function(i) {
				var $ths = $(this),
					val = $ths.attr('data-val'),
					txt = $ths.find('.text').text();
				text += txt;
				if (i < $area_selected.find('.area-selected-item').length - 1) text += '－';
				if (val) {
					$('#search_form').prepend('<input type="hidden" class="data-area" name="district[' + i + ']" value="' + val + '" />');
				}
			});
			if (text) {
				var title = text;
				text = text.length > 4 ? text.substring(0, 4) + '...' : text.substring(0, 4);
				$('#search_form').find('.area-select').text(text).attr('title', title);
			} else {
				$('#search_form').find('.area-select').text('意向地区');
			}
			//$('#area-dialog-clear').click();
			closeDiv();
		});

		// 招聘职位列表展开/收起
		var $jobs_list = $('#js-jobs-list'), $jobs_list_more = $('#js-jobs-list-more');
		if ($jobs_list.find('li').length > 15) {
			$jobs_list_more.show()
				.find('a').click(function() {
					var $ths = $(this);
					if ($ths.hasClass('fold')) {
						$ths.text('更多').removeClass('fold');
						$jobs_list.addClass('hr-jobs-list-fold');
					} else {
						$ths.text('收起').addClass('fold');
						$jobs_list.removeClass('hr-jobs-list-fold');
					}
				});
		}

		// 导入招聘职位浮窗
		//$('#float-jobs-trigger').powerFloat();
		/*====================浏览器兼容性解决方案=====================*/
		if (isIE6) {
			// 解决 IE6 hover Bug
			$('.ui-game-list-item').hover(function() { $(this).toggleClass('ui-game-list-item-hover'); });

			$("#bg, #bg1").width($(window).width()).height($(document).height());

			// 让IE6 缓存背景图片
			/* TredoSoft Multiple IE doesn't like this, so try{} it */
			try {
				document.execCommand("BackgroundImageCache", false, true);
			} catch (r) {}
		}
	});
})(jQuery);