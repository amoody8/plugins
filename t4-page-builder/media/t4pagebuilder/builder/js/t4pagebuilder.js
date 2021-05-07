(function($) {
    'use strict';
    $(document).ready(function() {
        var form = $('.t4b-form.form');
        form.submit(function(e) {
            e.preventDefault();
            let that = $(this);
        		var platform = that.attr('platform');
            var form_data = $(this).serializeArray();
            if (platform) {
            	form_data.push({
	                name: "platform",
	                value: platform
	            });
            }
            that.find('.contact-act').prop('disabled', true);
            var data = {};
            data.subject = form_data["subject"];
            data.data = form_data;
            if(platform){
            	 data.platform = platform;
            }
            $('.t4b-message-success, .t4b-message-error').remove();
            $.ajax({
              type: "POST",
              url: that.attr("action"),
              data: data,
            })
              .done(function (e) {
                that.hide();
                that.find('.contact-act').prop('disabled', false);
                $('.t4b-message-success').remove();
                if (e.message) {
                  that
                    .parent()
                    .prepend(
                      "<div class='alert alert-success t4b-message-success'>" +
                        e.message +
                        "</div>"
                    );
                } else {
                  that
                    .parent()
                    .prepend(
                      "<div class='alert alert-error t4b-message-error'>" +
                        e.error +
                        "</div>"
                    );
                    that.show();
                }
              })
              .fail(function (e) {
                console.log("fail:", e);
              });
        });
    });
})(jQuery);
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByTagName('form');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
jQuery(document).ready(function($){

	// process dropdown hover event
	var navitem_selector = '#t4b-header-menu .nav-item',
		navitem_selector_dropdown = '#t4b-header-menu .nav-item.dropdown',
		$activeitem = null, timeout = 0;

	var hideDropdowns = function () {
		var $opens = $(navitem_selector_dropdown + '.show');
		$opens.each(function() {
			var $item = $(this);
			if ($activeitem && $activeitem.closest($item).length) return;
			$item.removeClass('show').find('.dropdown-menu').removeClass('show');
			endAnimating($item);
		})
	}


	var pos = function () {
		var $dropdown = $activeitem.addClass('show').children('.dropdown-menu').addClass('show');
		//$activeitem.addClass('show').find('.dropdown-menu').addClass('show');
		var rtl = $('html').attr('dir') == 'rtl',
			dw = $dropdown.outerWidth(),
			ww = $(window).width(),
			dl = $dropdown.offset().left,
			iw = $activeitem.width(),
			il = $activeitem.offset().left,
			ml = null,
			align = $activeitem.data('align');

		ml = align == 'center' ? (iw-dw)/2 : (align == 'right' ? iw-dw : 0);
		if (dw < ww) {
			if (il + ml < 20) ml = 20 - il;
			if (il + ml + dw > ww - 20) ml = ww - 20 - il - dw;
		} else {
			ml = (ww-dw)/2 - il;
		}

		$dropdown.css('margin-left', ml);

	}

	var posrtl = function () {
		var $dropdown = $activeitem.addClass('show').children('.dropdown-menu').addClass('show');
		//$activeitem.addClass('show').find('.dropdown-menu').addClass('show');
		var rtl = $('html').attr('dir') == 'rtl',
			dw = $dropdown.width(),
			ww = $(window).width(),
			dl = $dropdown.offset().left,
			iw = $activeitem.width(),
			il = $activeitem.offset().left,
			ml = null,
			align = $activeitem.data('align');

		ml = align == 'center' ? (dw-iw)/2 : (align == 'right' ? dw-iw : 0);
		if (dw < ww) {
			if (il + iw + ml > ww - 20) ml = ww - 20 - il - iw;
			if (il + iw + ml < dw + 20) ml = dw + 20 - il - iw;
		} else {
			ml = ww - il - iw + (dw - ww)/2;
		}
		$dropdown.css('margin-right', -ml);
	}

	var showDropdown = function () {

		if ($activeitem.is('.dropdown')) {
			var $dropdown = $activeitem.addClass('show').children('.dropdown-menu').addClass('show');

			// with animation, start animating after some ms
			startAnimating ($activeitem);

			if ($('html').attr('dir') == 'rtl') {
				posrtl();
			} else {
				pos();
			}
		}
		// hide other dropdown
		hideDropdowns();
	}

	var startAnimating = function ($item) {
		// get duration
		var $menu = $item.closest('#t4b-header-menu');

		if (!$menu.hasClass('animate')) return;


		clearTimeout($item.data('animating-timer'));
		$item.data('animating-timer', setTimeout(function() {
			$item.addClass('animating');
		}, 10));
	}

	var endAnimating = function ($item) {

		// remove animating class to make sure the dropdown is totally hidden
		// get duration
		var $menu = $item.closest('#t4b-header-menu');
		if (!$menu.hasClass('animate')) return;
		var duration = parseInt($menu.data('duration')) || 400;
		clearTimeout($item.data('animating-timer'));
		$item.data('animating-timer', setTimeout(function() {
			$item.removeClass('animating');
		}, duration + 10))
	}

	$('body').on('mouseenter', navitem_selector, function(e) {
		var $this = $(this), $menu = $this.closest('#t4b-header-menu'),
			id = $menu.attr('id'), $toggle = $('.navbar-toggler[data-target="#' + id + '"]');
			// fix dropdown menu offset right to let
		var rt = ($(window).width() - ($(this).offset().left + $(this).outerWidth())); 
		if($(window).width() > 991 && rt < 150 && $(this).hasClass('dropright')){
			$(this).removeClass('dropright').addClass('dropleft');
		}
		if ($toggle.length && $toggle.is(':visible')) {
			// mobile, then remove animation and ignore
			if ($menu.hasClass('animate')) $menu.removeClass('animate').addClass('animate-bak');
			return;
		} else {
			if ($menu.hasClass('animate-bak')) $menu.removeClass('animate-bak').addClass('animate');
		}
		if ($this.closest(navitem_selector_dropdown).length) {
			if (timeout) {
				clearTimeout(timeout);
				timeout = 0;
			}

			var $_activeitem = $this.closest(navitem_selector_dropdown);
			if (!$_activeitem.hasClass('show') && !$(e.target).is($_activeitem) && !$(e.target).parent().is($_activeitem)) return;

			$activeitem = $_activeitem;
			showDropdown();
		} else {
			//timeout = setTimeout(function() {
				$activeitem = null;
				hideDropdowns();
			//}, 200)
		}
	}).on('mouseleave', navitem_selector, function() {
		var $this = $(this);
		timeout = setTimeout(function() {
			if ($activeitem && $activeitem.is($this)) {
				$activeitem = $this.parent().closest(navitem_selector);
				hideDropdowns();
			}
		}, 200)

	});

	// if menu open, just open the link
	var lastClickItem = null;
	$('.nav-item.dropdown a').on('click', function(e) {
		var $this = $(this);
		var parentDrpEl = $(this).closest('ul.dropdown-menu');
		if ($this.is(lastClickItem) || $this.parent().is('.show') || (!parentDrpEl.length && $(this).next().is(":visible"))) {
			var arr1 = this.href.split('#'),
				arr2 = location.href.split('#');
			if (arr1[0] == arr2[0])	{
				if (arr1.length > 1 && arr1[1]) location.hash =  '#' + arr1[1];
			} else {
				location.href = this.href;
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		} else {
			location.hash = "";
			var arr1 = this.href.split('#'),
				arr2 = location.href.split('#');
			if (arr1[0] == arr2[0])	{
				if (arr1.length > 1 && arr1[1]) location.hash =  '#' + arr1[1];
			}
			lastClickItem = $this;

			if(location.hash && !$this.is('.separator')){
				$('.js-offcanvas-close').trigger('click');
			}
		}

		return true;
	})

	// show toggler
	$('#t4b-header-menu').each(function() {
		$toggle = $('.navbar-toggler[data-target="#' + this.id + '"]');
		if ($toggle.length == 1) $toggle.removeAttr('style');
	})
    

})

/**
 *monitor the element scroll to add class into body for style effect later
 */
jQuery(window).ready(function($) {
    if (
        "IntersectionObserver" in window &&
        "IntersectionObserverEntry" in window &&
        "intersectionRatio" in window.IntersectionObserverEntry.prototype
    ) {
        var options = {
            root: null,
            rootMargin: "0px",
            threshold: 0,
        };
        var sections = document.querySelectorAll(".t4b-section");
        var maxIdx = 0;
        var sticky = document.querySelectorAll(".t4b-sticky-yes");

        function isValid(el) {
            return (
                el.offsetTop < window.innerHeight &&
                el.offsetTop + el.offsetHeight < window.innerHeight + 200
            );
        }

        function doChange(changes, observer) {
            changes.forEach(function(change) {
                var clientRect = change.boundingClientRect,
                    target = change.target;
                if (clientRect.top <= -clientRect.height) {
                    document.body.setAttribute("data-t4b-" + target.id, "over");
                    document.body.classList.add("t4b-top-away");
                } else {
                    document.body.setAttribute("data-t4b-" + target.id, "under");
                    document.body.classList.remove("t4b-top-away");
                }
            });
        }

        var observer = new IntersectionObserver(doChange, options);

        for (var i = 0; i < sections.length; i++) {
            var el = sections[i];
            if (isValid(el)) {
                el.idx = i;
                observer.observe(el);
            } else {
                maxIdx = i - 1;
                break;
            }
        }
        var top = 0;
        var zindex = 300;
        for (var i = 0; i < sticky.length; i++) {
            var elSk = sticky[i];
            top += elSk.offsetHeight;
            if (typeof sticky[i + 1] != "undefined") {
                $(elSk).css({ "z-index": zindex });
                zindex -= 1;
                $(sticky[i + 1]).css({ top: top, "z-index": zindex });
            }
        }
        // monitor not at top
        var options2 = {
            root: null,
            rootMargin: "0px",
            threshold: 0,
        };

        function doChange2(changes) {
            var clientRect = changes[0].boundingClientRect;
            if (clientRect.top < -20) {
                document.body.classList.add("t4b-not-top");
            } else {
                document.body.classList.remove("t4b-not-top");
            }
        }
        var observer2 = new IntersectionObserver(doChange2, options2);
        var anchorEl = $('<a name="t4b-top-anchor">').prependTo(".jpb-page");
        if (anchorEl.get(0)) observer2.observe(anchorEl.get(0));
    }

    $(document).on('show.bs.modal', '#imageModal_jpb-field-pagetext_media', function() {
        var modal = this;
        var btnClose = $(this).find('.close');
        btnClose.off('click').on('click', function(e) {
            e.preventDefault();
            $(modal).find('.modal-body').empty();
            $(modal).modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        });
    });
});

jQuery(document).ready(function($) {
	

});