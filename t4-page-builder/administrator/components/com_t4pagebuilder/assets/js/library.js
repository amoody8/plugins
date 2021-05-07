(function($){
    // set first tab active
    var tabAnchor = $("#importTabTabs li:first a");
    tabAnchor.click();
	var pageLibs = $(".load-libs");
	$(document).on('click','.t4b-libs__sidebar li',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(".t4b-libs__sidebar li").removeClass('t4b-active');
		$(this).addClass('t4b-active');
	});
	$(document).on('onmouseover onmouseleave','.hasTooltip',function(e){
		e.preventDefault();
		e.stopPropagation();
	});
})(jQuery);