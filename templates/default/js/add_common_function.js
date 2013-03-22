//首页行业分站,地区分站切换JS
function secBoard(elementID,listName,elementname,n) {
	var elem = document.getElementById(elementID);
	var elemlist = elem.getElementsByTagName(elementname);
	for (var i=0; i<elemlist.length; i++) {
		elemlist[i].className = "sel02";
		var m = i+1;
		document.getElementById(listName+"_"+m).style.display = "none";
	}
	elemlist[n-1].className = "sel01";
	document.getElementById(listName+"_"+n).style.display = "block";
}

//首页岗位分类搜索切换JS
function category_page(page){
	var pages = $('#category_list .gang');

	if(page < 0){
		return false;	
	}
	else if(page > pages.length-1){
		return false;
	}
	curr_page = page;
	pages.each(function(){
		$(this).hide();
	});
	$('#category_'+page).fadeIn('fast');
	$('.fyen a').each(function(){
		$(this).removeClass();
	});
	$('.fyen a:eq('+(page+1)+')').addClass('hover');
}
