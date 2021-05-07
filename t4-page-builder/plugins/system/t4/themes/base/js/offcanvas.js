jQuery(document).ready(function($){
	var $offcanvas = $('.t4-offcanvas');
	setTimeout(function(){
		$offcanvas.show();
	},300);
	$('.t4-wrapper').addClass('c-offcanvas-content-wrap');
	if(!$('#triggerButton').length ) $offcanvas.remove();
	$offcanvas.length && $('#triggerButton').length && $offcanvas.offcanvas({
		triggerButton: '#triggerButton' ,
		onOpen: function () {
			$('#triggerButton').addClass('active');
			bodyScrollLock.disableBodyScroll ($('.t4-off-canvas-body').get(0))
		},
		onClose: function () {
			$('#triggerButton').removeClass('active');
			setTimeout(function(){
				bodyScrollLock.enableBodyScroll ($('.t4-off-canvas-body').get(0))
			},300);
		}
	})
});