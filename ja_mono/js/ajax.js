;(function($){
	var ajax_duration = 500,
		curr_state = $.extend({}, history.state, {title: document.title, url: location.href.split('#')[0], 'class': $('html').attr('class'), dir: ''}),
		timer = 0;
	
	if (!curr_state.order) curr_state.order = 1;	

	var makeLinkAjaxable = function ($links) {
		$links.each(function() {
			var $a = $(this);
			// click handled, do nothing
			if ($a.data('events') && $a.data('events').click) return;
			// ajax check
			if (!$a.data('ajax')) return;

			$a.on('click', function(e) {
				do_ajax(this.href.split('#')[0], null, $a.attr('rel'));
				// hide off-canvas - fix for T3
				$('.t3-wrapper').trigger('click', e);

		        return false;
			});

			clearTimeout(timer);
			timer = setTimeout( function() {
				if ($('html').hasClass('com_search view-search')) {
					var $form_search = $('form.form-search');

					$('[name=searchphrase]').off('change').on('change', function(e) {
						e.preventDefault();
						$form_search.trigger('submit');
					})

					$('[name=ordering]').off('change').on('change', function(e) {
						e.preventDefault();
						$form_search.trigger('submit');
					})

					$('[name="areas[]"]').off('change').on('change', function(e) {
						e.preventDefault();
						$form_search.trigger('submit');
					})

					$('[name=limit]').removeAttr('onchange').off('change').on('change', function(e) {
						e.preventDefault();
						e.stopPropagation();
						$form_search.trigger('submit');
					})
				}
			}, 500)
		});
	};

	var do_ajax = function (url, data, dir) {
		// ignore if same link
	    var newurl = url,
    		currurl = location.href.split('#')[0];
        if ((newurl == currurl || newurl + '/' == currurl) && !data) return false;

        // show loading
    	$('html').addClass('ajax-loading');

    	// custom xhr object https://stackoverflow.com/questions/6136534
    	var xhr = new XMLHttpRequest();

		// call ajax
		$.ajax({
        	url: newurl,
        	method: 'POST',
        	data: $.extend({}, data, {jsa: 1}),
        	async: true,
        	success: function(html, status, req ) {
        		// url
        		// build state
        		var $result = $('<div>').html(html),
        			$ajax_blocks = $result.find('[data-ajax-block]'),
        			page_title = $result.find('meta[name="page-title"]').attr('content'),
        			page_class = $result.find('meta[name="page-class"]').attr('content'),
        			state = {title: page_title, 'class': page_class, blocks: '', dir: dir};

        		$ajax_blocks.each(function(){
        			state.blocks += this.outerHTML;
        		});
        		if (!page_class) {
        			matches = html.match(/class=('|")([^'"]*)\1/);
        			state['class'] = matches[2];
        		}
        		state.url = xhr.responseURL.replace(/(\?|&)jsa=1/, '');

        		show_new_page(state);
        	},
        	xhr: function() {
        		return xhr;
        	}
        });
	};

	var show_new_page = function(state) {
		$(document).trigger('ajaxBeforeRender', state);
		var dir = state.dir;
		// push history
		if (!state.order) {
			state.order = curr_state.order + 1;
			if (history.pushState) history.pushState (state, state.title, state.url);
		} else {
			dir = (state.order < curr_state.order && curr_state.dir != 'prev') || (state.order > curr_state.order && state.dir == 'prev') ? 'prev' : 'next';
		}

		var $new_links = $left = $right = $();

		var $blocks = $(state.blocks),
			$scripts = detact_scripts($blocks);
		$new_links = $blocks.find('a');
		// remove jsa=1 in link
		$new_links.each(function(){
			this.href = this.href.replace(/(\?|&)jsa=1/, '');
		});

		$blocks.each(function(){
			var $block = $(this),
				name = $block.data('ajax-block'),
				$old_block = $('[data-ajax-block="' + name + '"]');
			// check text to detect new content
			var old_text = $old_block.text().replace(/^\s+|\s+$/g, ""),
				new_text = $block.text().replace(/^\s+|\s+$/g, ""),
				old_html = $old_block.html().replace(/\s/g, ''),
				new_html = $block.html().replace(/\s/g, '');
			if (new_text != old_text || (new_text == '' && new_html != old_html)) {
				$old_block.addClass('block-old-content');
				$block.addClass('block-new-content');
				// check if invisible
				if ($block.hasClass('block-invisible')) {
					$old_block.replaceWith($block);
				} else {
					$block.insertAfter($old_block);
				}
			}
		});

		// update title
		document.title = state.title;
		var ajax_class = 'ajax-show-content' + (dir == 'prev' ? ' ajax-prev' : '');
		// prepare to show new content
		$('html').addClass(ajax_class)
		// start show new content
		setTimeout (function(){$('html').addClass('ajax-showing-content');}, 100);
		// replace html class
		setTimeout (function(){$('html').attr('class', state['class'] + ' ajax-showing-content ' + ajax_class)}, 400);
		// end animation
		setTimeout (function () {
			$('.block-old-content').remove();
			$('.block-new-content').removeClass('block-new-content');
			$('html').removeClass('ajax-show-content ajax-showing-content ajax-prev');
			// update # link
			$('a[href="#"]').attr('href', state.url+'#')
			// trigger event ajaxAfterRender for other proccessing
			$(document).trigger('ajaxAfterRender');
			// fix for addthis
			if (window.addthis) {
				window.addthis.cleanup();
			    $('#at20mc').remove();
			    if (state.blocks.match(/addthis\.com/))
			    	window.addthis = window._adr = window._atc = window._atd = window._ate = window._atr = window._atw = null;
			}
			// inject
			for(var i=0; i<$scripts.length; i++) {
				$('#script-' + i).replaceWith($scripts[i]);
			}				
		}, ajax_duration + 200);
		// enable ajax for new link
		makeLinkAjaxable ($new_links);

		// update mega-menu active item
		var match;
		if (match = state['class'].match(/itemid-(\d+)(\s|$)/)) {
			var itemid = match[1],
				$active = $('ul.nav a[data-itemid="'+itemid+'"]').first();
			$('ul.nav li.active').removeClass('active');
			$active.parents('li').addClass('active');
		}
		// update curr_state
		curr_state = state;
		$('body').trigger('subform-row-add')
	};

	var detact_scripts = function ($elems) {
		var $scripts = $elems.find('script');
		for(var i=0; i<$scripts.length; i++) {
			$($scripts[i]).replaceWith('<meta id="script-' + i + '" />');
		}
		return $scripts;
	}

	var enable_search_ajax = function () {
		var $form_search = $('form.form-search');
		if ($form_search.data('events') && $form_search.data('events').submit) return;
		$form_search.on('submit', function(e) {
			e.preventDefault();
			var url = this.action, formdata = $(this).serializeArray(), 
				data={},
				$html = $('html');

			for(var i=0; i<formdata.length; i++) 
				data[formdata[i].name] = formdata[i].value;

			// apply advanced search option on search view
			if ($html.hasClass('com_search view-search')) {
				var searchphrase = $('[name=searchphrase]:checked').val(),
					ordering = $('[name=ordering]').val(),
					areas = [],
					limit = $('[name=limit]').val();

				if (searchphrase) {
					data['searchphrase'] = searchphrase;
				}

				if (ordering) {
					data['ordering'] = ordering;
				}

				$('[name="areas[]"]:checked').each( function(idx, item ) {
					areas.push( $(item).val() );
				})

				if (areas.length) {
					data['areas'] = areas;
				}

				data['limit'] = limit;
			}
			data['tmpl'] = 'component';
			do_ajax(url, data, null);
			return false;
		});
	}

	// init navigation event
	$(window).on('popstate', function (e) {
		if (e.originalEvent.state != null)
			show_new_page(e.originalEvent.state);
	});

	$(document).ready(function(){
		curr_state.blocks = '';
		jQuery('[data-ajax-block]').each(function(){
		  curr_state.blocks += this.outerHTML;
		});

		if (history.replaceState) history.replaceState(curr_state, curr_state.title);
		// init links
		makeLinkAjaxable($('a'));

		// search
		enable_search_ajax();
		$(document).on('ajaxAfterRender', enable_search_ajax);
	});

})(jQuery);