
/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 *------------------------------------------------------------------------------
 */

// google fonts
jQuery(document).ready(function($){
    $(document).on('click','li .t4-font-weight-popup', function(e) {
        e.stopPropagation();
    })
    var t4b = window.t4b || window.parent.t4b;
    $(document).on('click','li .jub-font-container', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $('.t4-font-weight-popup').appendTo($(this));
        $('.jub-font-container.top').removeClass('top');
        $('.jub-font-container.left').removeClass('left');
        $('.jub-font-container.right').removeClass('right');
        var styles = $(this).closest('.jub-font').data('styles'),
        fontActive = false,
        nameFont = $(this).closest('.jub-font').data('name'),
        dataWeight = "",
        inputData = $('.load-google-fonts').val() ? JSON.parse($('.load-google-fonts').val()) : {};
        if($(this).closest('.jub-font').hasClass('font-active')){
            fontActive = true;
            dataWeight = inputData[nameFont].weight;
        }
        if(typeof dataWeight == 'undefined') dataWeight = "";
        var options = {
            styles: styles,
            name: nameFont,
            fontActive: fontActive,
            dataWeight: dataWeight
        }
        fontWeightRenderPopup($('.t4-font-weight-popup'),options);
        var offSet = $(this).find('.jub-font-styles').offset();
        var offSetfixed = $(this).closest('.t4-google-content').offset();
        var offSetScroll = $(this).closest('#managerTabContent').scrollTop();

        if(offSet.top - offSetfixed.top > 360 && offSetScroll != 0) {
            $(this).addClass('top');
        }
        if(offSet.left - offSetfixed.left < 500){
            $(this).addClass('right');
        }
        $(".t4-font-weight-popup").show();
        $('li.jub-font.font-focus').removeClass('font-focus');
        $(this).closest('li.jub-font').addClass('font-focus');

    });
    $(document).on('keyup','#t4-font-filter',function() {
    	var value = $(this).val().toLowerCase();
    	var fontCount = 0;
    	var $filterKey = 'li.jub-google-font', $filterView = $('.t4b-fonts-search');
	    $('li.jub-google-font').filter(function() {
	      	$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
	      	if($(this).text().toLowerCase().indexOf(value) > -1){
	      		fontCount++;
	      	}
	    });
	    $('.t4-font-filter').show();
	    if(!value) $('.t4-font-filter').hide();
	    if(!fontCount){
	    	$filterView.find('.t4-font-filter').get(0).innerHTML = '<span class="result-message no-result">No result</span>';

	    }else{
	    	$filterView.find('.t4-font-filter').get(0).innerHTML = '<span class="result-message">Found <strong>'+fontCount+'</strong> results</span>';
	    }
        $('#custom-local').hide();
    });
    //icons filter
    $(document).on('keyup','#t4-icon-filter',function() {
        var value = $(this).val().toLowerCase();
        $('li.t4b-icon').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    var update = function(fonts){
    	var fontInputs = $('.load-google-fonts').val() ? JSON.parse($('.load-google-fonts').val()) : {};
    	fontInputs[fonts.name] = fonts;
    	console.log(fontInputs);
    	$('.load-google-fonts').val(JSON.stringify(fontInputs)).trigger('change');
    }
    var fontWeight_NumbertoText = function ($number) {
        var text = '';
        switch ($number) {
            case '100':
                text = 'Thin';
                break;
            case '100italic':
            case '100i':
                text = 'Thin italic';
                break;
            case '200':
                text = 'Extra-Light';
                break;
            case '200italic':
            case '200i':
                text = 'Extra-Light italic';
                break;
            case '300':
                text = 'Light';
                break;
            case '300italic':
            case '300i':
                text = 'Light italic';
                break;
            case '400':
            case 'regular':
                text = 'Regular';
                break;
            case 'italic':
            case '400italic':
            case '400i':
                text = 'Regular italic';
                break;
            case '500':
                text = 'Medium';
                break;
            case '500italic':
            case '500i':
                text = 'Medium italic';
                break;
            case '600':
                text = 'Semi-Bold';
                break;
            case '600italic':
            case '600i':
                text = 'Semi-Bold italic';
                break;
            case '700':
                text = 'Bold';
                break;
            case '700italic':
            case '700i':
                text = 'Bold italic';
                break;
            case '800':
                text = 'Extra-Bold';
                break;
            case '800italic':
            case '800i':
                text = 'Extra-Bold italic';
                break;
            case '900':
                text = 'Ultra-Bold';
                break;
            case '900italic':
            case '900i':
                text = 'Ultra-Bold italic';
                break;
            default:
                text = $number;
                break;
        }
        return text;
    }
    var fontWeight_TexttoNumber = function ($text) {
        var number = '';
        switch ($text) {
            case 'Thin':
                number = 100;
                break;
            case '100italic':
            case '100 italic':
            case 'Thin italic':
                number = '100i';
                break;
            case 'Extra-Light':
                number = 200;
                break;
            case '200italic':
            case '200 italic':
            case 'Extra-Light italic':
                number = '200i';
                break;
            case 'Light':
                number = 300;
                break;
            case '300italic':
            case '300 italic':
            case 'Light italic':
                number = '300i';
                break;
            case 'Regular':
            case 'regular':
                number = 400;
                break;
            case '400 italic':
            case 'italic':
            case 'Regular italic':
                number = '400i';
                break;
            case 'Medium':
                number = 500;
                break;
            case '500italic':
            case 'Medium italic':
                number = '500i';
                break;
            case 'Semi-Bold':
                number = 600;
                break;
            case '600italic':
            case 'Semi-Bold italic':
                number = '600i';
                break;
            case 'Bold':
                number = 700;
                break;
            case '700italic':
            case 'Bold italic':
                number = '700i';
                break;
            case 'Extra-Bold':
                number = 800;
                break;
            case '800italic':
            case 'Extra-Bold italic':
                number = '800i';
                break;
            case 'Ultra-Bold':
                number = 900;
                break;
            case '900italic':
            case 'Ultra-Bold italic':
                number = '900i';
                break;

            default:
                number = $text;
                break;
        }
        return number;
    }
    var fontWeightRenderPopup = function($selector,$data){
        var $div = "";
        //convert data to array
        var weightArr = $data.styles.split(","),dataW = $data.dataWeight;
        if(weightArr.length){
            if(dataW && typeof dataW == 'string') dataW = JSON.parse(dataW);
            $div += "<ul>";
            weightArr.forEach(function(w){
                w = fontWeight_NumbertoText(w);
                w_number = fontWeight_TexttoNumber(w);
                var $checked = "";
                if($data.fontActive && dataW.includes(w_number.toString())){
                    $checked = 'checked=""';
                }
                $div += "<li class='t4-form-checkbox'>";
                $div += "<label class='checkbox-label'>";
                $div += '<input type="checkbox" class="form-check-input" value="" data-value="'+w_number+'" '+$checked+'>'+w;
                $div += "</label></li>";
            });
            $div += "</ul>";
        }
        if($div){
            $div += '<div class="btn-actions"><button  type="button"data-action="btn.cancel" class="t4-btn btn-action">Cancel</button>';
            $div += '<button type="button" data-action="btn.selected" data-name_font="'+$data.name+'"  class="t4-btn btn-action btn-primary">Select font</button></div>';
        }
        $selector.html($div);

    }
    $(document).on('click','.t4-btn[data-action="btn.selected"]', function(e) {
        var dataWeight = [],nameFont = $(this).data('name_font'),fontObj = {};
        var fontInputs = $('.load-google-fonts').val() ? JSON.parse($('.load-google-fonts').val()) : {};
        $('.t4-form-checkbox').find('.form-check-input').each(function(){
            if($(this).prop('checked')){
                dataWeight.push($(this).data('value').toString());
            }
        });
        $(this).closest('.jub-font').addClass('font-active');
        fontObj.name = nameFont;
        fontObj.weight = dataWeight;
        $('body').removeClass('t4-modal-open');
         $(".t4-font-weight-popup").hide();
         if(!fontInputs[nameFont]){
	        var $li = '<li class="btn btn-default"><div class="font-selected"><span class="font-name" style="font-family:'+nameFont+'">'+nameFont+'</span><span class="font-styles">'+dataWeight.join(',')+'</span></div><span class="fal fa-times jpb-font-deleted"></span></li>'
	        $('.jpb-fonts-selected > ul').append($li);
         }
    	fontInputs[nameFont] = fontObj;

        updateHead(JSON.stringify(fontInputs));
    	$('.load-google-fonts').val(JSON.stringify(fontInputs)).trigger('change');

    });
    var updateHead = function (data){
        let nameFonts = Object.values(JSON.parse(data)),fontname = [];
        nameFonts.forEach(function(font){
            let fontval = font.name;
            if(font.weight.length) fontval += ":"+font.weight.join(',');
            fontname.push(fontval);
        });
        $('head').find('.loadfonts').remove();
        $('<link class="loadfonts" href="https://fonts.googleapis.com/css?family='+escape(fontname.join('|'))+'" rel="stylesheet" type="text/css" />').appendTo($('head'));
    }
    $(document).on('click','.t4-btn[data-action="btn.cancel"]', function(e) {
        $(".t4-font-weight-popup").hide();
    });
    $(document).on('click','.t4-font-update',function(){
    	$('.tpb-input.input > ul').find('li').remove();
    	var $lis = $('.jpb-fonts-selected > ul').find('li');
    	$('.tpb-input.input > ul').append($lis.clone());
    	$('.t4-google-font-modal').modal('hide');
    });
    $(document).on('click','.t4-font-cancel',function(){
        $('.t4-google-font-modal').modal('hide');
    });
    $(document).on('click', '.t4-fonts-manager', function(e) {
         $(".t4-font-weight-popup").hide();
         $('li.jub-font.font-focus').removeClass('font-focus');
    });
    $(document).on('click','span.jpb-font-deleted',function(){
    	var namefont = $(this).parents('li').data('name');
    	var inputData = $('.load-google-fonts').val() ? JSON.parse($('.load-google-fonts').val()) : {};
    	delete inputData[namefont];
    	$('.t4-google-font-modal').find('li[data-name="'+namefont+'"]').removeClass('font-active');
    	$('.load-google-fonts').val(JSON.stringify(inputData)).trigger('change');
    	$(this).parents('li').remove();

    });
    $(document).on('click','.onoffswitch',function(e){
        e.preventDefault();
        let fontConfig = $(this).data('icon');
        let dataConfig = $(this).closest('.load-font-'+fontConfig).data('val');
        let checked = $(this).find('input').prop('checked');
        let loadFontIcons = JSON.parse($('#load_font_icons').val());

        let customLoadFont = $(this).closest('.load-font-'+fontConfig).find('.custom-load-font');
        if(checked){
           $(this).find('input').prop('checked',false);
            dataConfig[fontConfig] = false;

           customLoadFont.slideUp();
        }else{
            $(this).find('input').prop('checked',true);
            dataConfig[fontConfig] = true;
            customLoadFont.slideDown();
        }
        console.log('data: ',$(this).closest('.load-font-'+fontConfig));
        $(this).closest('.load-font-'+fontConfig).data('val',dataConfig);
        loadFontIcons[fontConfig] = dataConfig;
        $('#load_font_icons').val(JSON.stringify(loadFontIcons));
    });
    $(document).on('click','.url-type .btn',function(){
        let urlVal = $(this).data('val');
        $(this).closest('#url-icons').find('.btn.active').removeClass('active');
        $(this).addClass('active');
        let loadFontIcons = JSON.parse($('#load_font_icons').val());
        let fontConfig = $(this).closest('#url-icons').data('icon');
        let dataConfig = $(this).closest('.load-font-'+fontConfig).data('val');
        let customUrl = $(this).closest('fieldset').find('.control-group.custom-url');
        if(urlVal == 'cdn'){
            customUrl.hide();
            dataConfig.url_type = 'cdn';

        }else{
            customUrl.show();
            dataConfig.url_type = 'url';

        }
        $(this).closest('.load-font-'+fontConfig).data('val',dataConfig);
        loadFontIcons[fontConfig] = dataConfig;
        $('#load_font_icons').val(JSON.stringify(loadFontIcons));
    });
    $(document).on(
      "change",
      "#url_material_icons, #url_awesome_icons",
      function () {
        let fontConfig = $(this).data("icon");
        let loadFontIcons = JSON.parse($("#load_font_icons").val());
        let dataConfig = $(this)
          .closest(".load-font-" + fontConfig)
          .data("val");
        let inputValue = $(this).val();
        dataConfig.custom_url = inputValue;
        $(this)
          .closest(".load-font-" + fontConfig)
          .data("val", dataConfig);
        loadFontIcons[fontConfig] = dataConfig;
        $("#load_font_icons").val(JSON.stringify(loadFontIcons));
      }
    );
    // export
    $(document).on('click','.btn-action[data-action="font.addfont"]', function() {
        $('.add-more-custom-font .btn-action.active').removeClass('active');
        $(this).addClass('active');
        // show addon form
        $('.custom-font-form').show();
        $('#custom-font-input').val("");
        $('.custom-font-url').show();
        $('.add-font-name').show();
        $('.custom-css').hide();

    });
    // export
    $(document).on('click','.btn-action[data-action="font.addcss"]', function() {
        $('.add-more-custom-font .btn-action.active').removeClass('active');
        $(this).addClass('active');
        // show addon form
        $('.custom-font-form').show();
        $('#custom-font-input').val("");
        $('.custom-font-url').hide();
        $('.add-font-name').hide();
        $('.custom-css').show();
    });

    $(document).on('click','.btn-action[data-action="fonts.save"]', function() {
        var type = $(this).data('type');
        var pageid = $(this).data('pageid'); 
        doSave(type,pageid);
    })
    $(document).on('click','.btn-action[data-action="fonts.remove"]', function(e) {
        e.preventDefault();e.stopPropagation();
        doRemove(this);
    })
    var doSave = function (typefont,pageid) {
        var url = location.pathname + '/index.php?option=com_t4pagebuilder&view=page&format=json&act=customfont&task=add';
        var typeActive = $('.add-more-custom-font').find('.btn-action.active').data('type');
        var fonts = {
            type: typeActive,
            pageid: pageid
        };
        if (!fonts.type) {
            var mesg = $('<div class="message alert" />').append('Font Type can not be empty');
            if(!$('.control-group.custom-font-type').find('.message.alert').length){
                $('.control-group.custom-font-type').append(mesg);
            }
            return;
        }
        if(fonts.type == 'css'){
            var $css = $('.'+typefont+' #custom-css').val().trim();
            if ($css) {
                fonts.css = $css.split('\n');

            }
            if (!fonts.css) {
                var mesg = $('<div class="message alert" />').append(t4b.langs.t4bCustomFontCssMissed);
                if(!$('.control-group.custom-css').find('.message.alert').length){
                    $('.control-group.custom-css').append(mesg);
                }

                return;
            }
        }else{
            var font = $('.'+typefont+' #custom-font-url').val().trim();
            if (font) {
                fonts.font = font.split('\n');
            }
            if (!fonts.font) {
                var mesg = $('<div class="message alert" />').append(t4b.langs.t4bCustomFontFileMissed);
                if(!$('.control-group.custom-font-url').find('.message.alert').length){
                    $('.control-group.custom-font-url').append(mesg);
                }
                return;
            }
        }
        $.post(url, {fonts:fonts}).then(function(response) {
            if (response.ok && response.fonts) {
                // hide form
                if(response.fonts.length){
                    var $font = response.fonts;
                    for (var i = 0; i < $font.length; i++) {
                        var $li = $('.custom-font#custom-local').clone();
                        $li.data('name',$font[i]['name']);
                        $li.removeAttr('id').removeClass('hide');
                        $li.find('.custom-font-container').attr('title',$font[i]['name']);
                        $li.find('.custom-font-container').data('type',$font[i]['type']);
                        $li.find('.custom-font-name').html($font[i]['name']);
                        $li.insertBefore($('.custom-font#custom-local')).show();
                    }
                }
                $('.custom-font-input').val('');
                alert(t4b.langs.t4bCustomFontAdded);
            } else {
                alert(response.error);
            }
        })
    }
    var doRemove = function (btn) {
        var $btn = $(btn),pageid = $(btn).data('pageid'),
            $fontName = $btn.closest('li').find('.custom-font-name').text();
        if (!$fontName) return;
        let conf = confirm(t4b.langs.t4bCustomFontConfirmRemove);
        if(conf){
            var url = location.pathname + '/index.php?option=com_t4pagebuilder&view=page&format=json&act=customfont&task=remove';
            $.post(url, {name: $fontName,pageid}).then(function(response) {
                if (response.ok) {
                    // remove addon
                    $btn.closest('li').remove();
                } else {
                    alert(response.error);
                }
            })
        }else{
            console.log('xxxx');
             return false;
        }
    }
});
