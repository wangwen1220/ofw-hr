////////////////////////////////////////////////////////////////////////////////
//	名称: 人才网主程序
//	作者: 王文 wangwen1220@139.com
//	说明: 依赖 jQuery v1.9.1
//	日期: 2013-3-19
////////////////////////////////////////////////////////////////////////////////

// 处理各库之间的冲突问题
window.myjq = window.myjq || jQuery.noConflict(true);

(function($) {
	/*====================常用工具=====================*/
	// IE 版本判断
	var isIE = !!window.ActiveXObject,
		isIE6 = isIE && !window.XMLHttpRequest;

	// 显示当前 jQuery 的版本
	//window.console && console.log($.fn.jquery);

	/*====================全站通用=====================*/
	$(function() {
		// 搜索框获得或失去焦点
		$('.search-wd').focus(function() {
			var $ths = $(this);
			$ths.addClass('focus');
			if ($ths.val() === this.defaultValue) {
				$ths.val('');
			}
		}).blur(function() {
			var $ths = $(this);
			$ths.removeClass('focus');
			if ($ths.val() === '') {
				$ths.val(this.defaultValue);
			}
		});

		// 查找关键词不能为空
		$('form.search').submit(function() {
			var $search_wd = $(this).find('.search-wd'),
				swd = $search_wd.val();
			if ($.trim(swd) == '' || swd == $search_wd[0].defaultValue) {
				alert('请输入你要查找的关键词！');
				return false;
			}
		});
	});

	/*!
	名称: OFweek 人才网职位选择弹窗
	维护: 王文 wangwen1220@139.com
	说明: 依赖 jQuery v1.9.1
	日期: 2013-3-21
	*/
	$(function() {
		var $jselect = $('#jselect'),
			default_text = $jselect.text(),
			$jselected = $('#jselected'),
			$jsdialog_jobs = $('#jsdialog-jobs'),
			$overlay = $('#js-overlay'),
			$jobs_selected = $jsdialog_jobs.find('.jsdialog-selected'),
			$jobs_list = $jsdialog_jobs.find('.jsdialog-list'),
			$jobs_sublist = $jsdialog_jobs.find('.jsdialog-sublist'),
			$jsdialog_search = $jsdialog_jobs.find('.jsdialog-search'),
			$jsdialog_search_resault = $jsdialog_jobs.find('.jsdialog-search-resault'),
			$search_closer = $jsdialog_search.find('.search-closer'),
			$jsdialog_closer = $jsdialog_jobs.find('.jsdialog-closer'),
			$jsdialog_clear = $jsdialog_jobs.find('.jsdialog-clear'),
			$jsdialog_submit = $jsdialog_jobs.find('.jsdialog-submit'),
			$tips = $jsdialog_jobs.find('.jsdialog-tips'),
			jobs_html = $jobs_list.html();

		// 打开职位弹窗
		$jselect.on('click', function() {
			$jsdialog_jobs.show();
			$overlay.show();
		});

		// 弹窗垂直居中
		if (!isIE6) {
			$jsdialog_jobs.css('top', ($(window).outerHeight() - $jsdialog_jobs.outerHeight()) / 2);
		}

		// 打开二级职位列表
		$jobs_list.on('click', 'a', function() {
			var $ths = $(this),
				id = $ths.attr('data-id'),
				pos = $ths.position(),
				left = pos.left,
				right = left > 250 ? $jsdialog_jobs.innerWidth() - left - $ths.outerWidth() : 'auto',
				top = pos.top;

			// 切换当前职位状态
			$ths.addClass('on');

			// 定位二级职位列表
			$jobs_sublist.css({
				display: 'block',
				left: left > 250 ? 'auto' : left,
				right: $jsdialog_jobs.innerWidth() - left - $ths.outerWidth(),
				top: top + 24
			});

			// 加载二级职位列表数据
			$.get('/plus/ajax_zt.php?action=navisub&id=' + id, function(d) {
				$jobs_sublist.html(d);
				// 设置二级职位列表状态
				$jobs_selected.find('.jsdialog-selected-item').each(function() {
					$jobs_sublist.find('#' + $(this).attr('data-id')).attr('checked', true);
					/* TODO */
					if ($jobs_sublist.find('.chooseall input').attr('checked')) {
						$jobs_sublist.find('.chooseall').next().find('input').attr('checked', true).attr('disabled', true);
					}
				});
			});

			/*if (left > 300) {
				$jobs_sublist.css({
					display: 'block',
					left: 'auto',
					right: $('#popDiv1').innerWidth() - left - $ths.outerWidth(),
					top: top + 24
				});
			} else {
				$jobs_sublist.css({
					display: 'block',
					left: left,
					top: top + 24
				});
			}*/
			return false;
		}).on('mouseleave', '.on', function(e) {
			//console.log(e.relatedTarget.id);
			//if ($(e.relatedTarget).is($('#gw-sublist')) || $jobs_list.find(e.relatedTarget).length) {
			// 这里本来用 $jobs_list.is(e.relatedTarget) 就可以检测出，但是这个版本的 jQuery 有 BUG 检测不出来
			//if (e.relatedTarget.className === 'jsdialog-sublist' || $jobs_list.find(e.relatedTarget).length) return;
			var $ths = $(this);
			//alert('test');

			// 如果是移到二级列表上则返回
			if ($jobs_sublist.is(e.relatedTarget)) return;
			//if ($jobs_sublist.is(e.relatedTarget) || $jobs_sublist.has(e.relatedTarget).length) return;
			//if ($(e.relatedTarget).hasClass('jsdialog-sublist') || $jobs_sublist.find(e.relatedTarget).length) return;

			// 重置状态
			$jobs_sublist.trigger('mouseleave');
			//$ths.removeClass('on');
			//$jobs_sublist.hide().html("<div class='loading'></div>");
			//$tips.hide();
		}).on('mouseenter', 'a', function() {
			//var $ths = $(this);
			// 如果当前职位二级列表已打开
			//if ($ths.hasClass('on')) return;

			// 重置状态
			//$ths.siblings().removeClass('on');
			//$jobs_sublist.hide().html("<div class='loading'></div>");
			//$tips.hide();
		});

		// 鼠标移到子弹窗上
		$jobs_sublist.on('mouseleave', function(e) {
			/* 如果是移到当前一级职位上 */
			if ($(e.relatedTarget).hasClass('on')) return;

			// 重置状态
			$jobs_list.find('a').removeClass('on');
			$jobs_sublist.hide().html("<div class='loading'></div>");
			$tips.hide();
		}).on('click', 'input', function(e) { // 选择二级职位
			selectJobs(e, $(this));
		});

		// 搜索结果职位选择
		$jsdialog_search_resault.on('click', 'input', function(e) {
			selectJobs(e, $(this));
		});

		// 已选岗位关闭按钮
		$jobs_selected.on('click', '.closer', function() {
			$(this).parent().remove();
		});

		// 搜索岗位
		$jsdialog_search.find('.search-wd').keyup(function() {
			var wd = $.trim(this.value),
				$closer = $(this).next('.search-closer');
			if (wd) {
				$.get('/plus/ajax_zt.php?action=navisug&key=' + encodeURIComponent(wd), function(d) {
					/* TODO */
					if (d !== '' && d == 'null' && d === '<div id="gw-search-resault" class="gw-search-resault fn-clear"></div>') {
					//if (d !== '') {
						$jobs_list.html(d);
						$tips.hide();
					} else {
						$jobs_list.html(jobs_html);
						$tips.text('没有您要搜索的关键词，建议重新搜索').show();
					}
				});
				$closer.addClass('active');
			} else {
				$jobs_list.html(jobs_html);
				$closer.removeClass('active');
				$tips.hide();
			}
		});

		// 清除搜索框
		$search_closer.click(function() {
			$(this).prev('.search-wd').val('').keyup().blur();
			$jobs_list.html(jobs_html);
		});

		// 关闭窗口
		$jsdialog_closer.on('click', function() {
			$search_closer.click();
			$jsdialog_jobs.hide();
			$overlay.hide();
		});

		// 清除窗口数据
		$jsdialog_clear.on('click', function() {
			$jobs_selected.empty();
			$search_closer.click();
		});

		// 提交窗口数据
		$jsdialog_submit.on('click', function() {
			var text = '',
				value = '',
				$selected_item = $jobs_selected.find('.jsdialog-selected-item');

			if ($selected_item.length) {
				// 组合数据
				$selected_item.each(function(i) {
					var $ths = $(this);
					text += $ths.find('.text').text();
					value += $ths.attr('data-val');

					// 如果不是最后一项
					if (i < $selected_item.length - 1) {
						text += ',';
						value += ',';
					}
				});

				// 写入数据
				$jselect.text(text).attr('title', text);
				$jselected.val(value);
			} else {
				$jselect.text(default_text).attr('title', '');
				$jselected.val('');
			}
			$jsdialog_closer.click();
		});

		// 选择二级职位函数
		function selectJobs(e, $ths) {
			var $chooseall = $ths.parents('.chooseall'),
				is_chooseall = $chooseall.length,
				text = $ths.parent().text(),
				val = $ths.val(),
				id = $ths.attr('id');

			// 如果该职位已被选中
			if ($jobs_selected.find('[data-id =' + id + ']').length) {
				$ths.attr('checked', false);
				$jobs_selected.find('[data-id =' + id + ']').remove();
				if (is_chooseall) {
					$chooseall.next().find('input').attr('checked', false).attr('disabled', false);
				}
				$tips.hide();
			} else if ($jobs_selected.find('.jsdialog-selected-item').length >= 5) {// 如果已选够 5 项
				$tips.show().text('最多只能选择5项');
				e.preventDefault();
			} else {
				// 全选
				if (is_chooseall) {
					//$chooseall.next().find('input').attr('checked', true).attr('disabled', true);
					$chooseall.next().find('input').each(function() {
						$jobs_selected.find('[data-id =' + this.id + ']').remove();
					}).attr('checked', 'checked').attr('disabled', true);
				}

				$ths.attr('checked', true);
				// 加入到已选择列表中
				$jobs_selected.append("<div class='jsdialog-selected-item' data-id='" + id + "' data-val='" + val + "'><span class='text'>" + text + "</span><a class='closer' title='点击取消' href='javascript:;'>X</a></div>");
				$tips.hide();
			}

			// 重定位
			if ($jobs_list.find('.on').length) $jobs_sublist.css({top: $jobs_list.find('.on').position().top + 24});
		}
	});

	/*====================用户注册页=====================*/
	$(function() {
		// 注册表单验证 #用户名和邮箱实时验证，返回数据是json格式：{"info":"demo info","status":"y"}
		$('#regform').Validform({
			//showAllError: true,
			tiptype: 4,
			usePlugin: {
				passwordstrength: {
					minLen: 6,//设置密码长度最小值，默认为0;
					maxLen: 16,//设置密码长度最大值，默认为30;
					trigger: function(obj, error) {
						//该表单元素的keyup和blur事件会触发该函数的执行;
						//obj:当前表单元素jquery对象;
						//error:所设密码是否符合验证要求，验证不能通过error为true，验证通过则为false;

						//console.log(error);
						//obj.siblings(".Validform_checktip").toggle(error);
						//obj.siblings(".passwordStrength").toggle(!error);
						if (error) {
							obj.siblings(".Validform_checktip").show();
							obj.siblings(".passwordStrength").hide();
						} else {
							obj.siblings(".Validform_checktip").hide();
							obj.siblings(".passwordStrength").show();
						}
					}
				}
			}
		});
	});

	/*====================浏览器兼容性=====================*/
	if (isIE6) {
		$(function() {
			// 解决 IE6 hover Bug
			//$('.ui-game-list-item').hover(function() { $(this).toggleClass('ui-game-list-item-hover'); });

			// 设置遮罩层宽高
			$('.ui-overlay').width($(window).width()).height($(window).height());
		});

		// 页面加载完执行
		$(window).load(function() {
			// 让IE6 缓存背景图片
			/* TredoSoft Multiple IE doesn't like this, so try{} it */
			try {
				document.execCommand("BackgroundImageCache", false, true);
			} catch (r) {}
		});
	}
})(myjq);