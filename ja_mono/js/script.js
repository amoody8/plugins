/**
 * ------------------------------------------------------------------------
 * JA Mono Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

 (function($){
  $(document).on('ready ajaxAfterRender', function(){
  ////////////////////////////////
  // equalheight for col
  ////////////////////////////////
  var ehArray = ehArray2 = [],
    i = 0;

  $('.equal-height').each (function(){
    var $ehc = $(this);
    if ($ehc.has ('.equal-height')) {
      ehArray2[ehArray2.length] = $ehc;
    } else {
      ehArray[ehArray.length] = $ehc;
    }
  });
  for (i = ehArray2.length -1; i >= 0; i--) {
    ehArray[ehArray.length] = ehArray2[i];
  }

  var equalHeight = function() {
    for (i = 0; i < ehArray.length; i++) {
      var $cols = ehArray[i].children().filter('.col'),
        maxHeight = 0,
        equalChildHeight = ehArray[i].hasClass('equal-height-child');

    // reset min-height
      if (equalChildHeight) {
        $cols.each(function(){$(this).children().first().css('min-height', 0)});
      } else {
        $cols.css('min-height', 0);
      }
      $cols.each (function() {
        maxHeight = Math.max(maxHeight, equalChildHeight ? $(this).children().first().innerHeight() : $(this).innerHeight());
      });
      if (equalChildHeight) {
        $cols.each(function(){$(this).children().first().css('min-height', maxHeight)});
      } else {
        $cols.css('min-height', maxHeight);
      }
    }
    // store current size
    $('.equal-height > .col').each (function(){
      var $col = $(this);
      $col.data('old-width', $col.width()).data('old-height', $col.innerHeight());
    });
  };

  equalHeight();

  // monitor col width and fire equalHeight
  setInterval(function() {
    $('.equal-height > .col').each(function(){
      var $col = $(this);
      if (($col.data('old-width') && $col.data('old-width') != $col.width()) ||
          ($col.data('old-height') && $col.data('old-height') != $col.innerHeight())) {
        equalHeight();
        // break each loop
        return false;
      }
    });
  }, 500);
  });

})(jQuery);

(function($){
  $(document).on('ready ajaxAfterRender', function(){
    var $container = $('.ja-isotope-wrap.packery #grid');
                         
    if ($container.length) {
      $container.isotope({
        layoutMode: 'packery',
        itemSelector: '.item',
      });
         
      // re-order when images loaded
      $container.imagesLoaded(function(){
        $container.isotope();
      
        /* fix for IE-8 */
        setTimeout (function() {
          $('.ja-isotope-wrap.packery #grid').isotope();
        }, 2000);
      });
      
      $(window).on('resize', function(){ 
        $container.isotope();
      }); 
    }
	
	var fullHeight = $(window).height();
		if($('#particles-js').length > 0) {
			$('#particles-js').css('height',fullHeight);
		}

	$(window).resize(function(){
		var fullHeight = $(window).height();
		if($('#particles-js').length > 0) {
			$('#particles-js').css('height',fullHeight);
		}
	})
	
	// Add Placeholder form contact
	var formContact = $('.com_contact');
	if (formContact.length > 0) {
		$('#jform_contact_name', formContact).attr('placeholder',Joomla.JText._('COM_CONTACT_CONTACT_EMAIL_NAME_LABEL'));
		$('#jform_contact_email', formContact).attr('placeholder',Joomla.JText._('COM_CONTACT_EMAIL_LABEL'));
		$('#jform_contact_emailmsg', formContact).attr('placeholder',Joomla.JText._('COM_CONTACT_CONTACT_MESSAGE_SUBJECT_LABEL'));
		$('#jform_contact_message', formContact).attr('placeholder',Joomla.JText._('COM_CONTACT_CONTACT_ENTER_MESSAGE_LABEL'));
		
		if($('.ie8').length > 0) {
			$("input[placeholder], textarea[placeholder]", formContact).each(function(i, e){
				if($(e).val() == "") {
					$(e).val($(e).attr("placeholder"));
				}
				$(e).blur(function(){
				if($(this).val()=="")
					$(this).val($(e).attr("placeholder"));
				}).focus(function() {
				if($(this).val() == $(e).attr("placeholder"))
					$(this).val("");
				});
			});
		}
	}
	
	// Add Affix for header
	$heightHeader = $('.t3-header').outerHeight();
	$heightBlocktop = $(window).height() - ($('.t3-header').outerHeight() * 1.5);
	$('.t3-header').affix({
	  offset: {
		top: $heightBlocktop,
	  }
	})

	
	$(window).resize(function(){
		$heightHeader = $('.t3-header').outerHeight();
		$heightBlocktop = $(window).height() - ($('.t3-header').outerHeight() * 1.5);
		$('.t3-header').affix({
		  offset: {
			top: $heightBlocktop,
		  }
		})
	})
  });
})(jQuery);


// Search
// -------------

(function($){
  $(document).ready(function(){
    if ($('html').hasClass('view-search')) {
      $('body').addClass('search-open');
      $('.btn-search').addClass('btn-open');
    }
    $('.btn-search').click(function() {
      $('body').toggleClass('search-open');
      $('.btn-search').toggleClass('btn-open');
      if ($('.btn-search').hasClass('btn-open')) $('.search-query').focus();
    });
    // fix for search page to other page
    $(document).on('ajaxBeforeRender', function(event, state){
      if ($('html').hasClass('view-search') && (!state['class'] || !state['class'].match(/view-search/))) {
        $('body').removeClass('search-open');
        $('.btn-search').removeClass('btn-open');
      }
    }).on('ajaxAfterRender', function() {
      if ($('html').hasClass('view-search')) {
        $('body').addClass('search-open');
        $('.btn-search').addClass('btn-open');
      }
      // fix for off-canvas-position
      $('.t3-off-canvas').css('top', 'auto');
      // scroll to top
      $("html, body").animate({ scrollTop: 0 }, "fast");
    });
  });
})(jQuery);

// TAB
// -----------------
(function($){
  $(document).ready(function(){
    if($('.nav.nav-tabs').length > 0 && !$('.nav.nav-tabs').hasClass('nav-stacked')){
      $('.nav.nav-tabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
      })
     }
  });
})(jQuery);

// ADD CLASS IE TO BODY
// -----------------
(function($){
  $(document).ready(function(){
  	if (detectIE()!==false) {
  		$('body').addClass('ie'+detectIE());
  	}
  	/**
	 * detect IE
	 * returns version of IE or false, if browser is not Internet Explorer
	 */
	function detectIE() {
		var ua = window.navigator.userAgent;

		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			// IE 10 or older => return version number
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}

		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			// IE 11 => return version number
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}

		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
		   // IE 12 => return version number
		   return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}

		// other browser
		return false;
	}
  	
  });
})(jQuery);