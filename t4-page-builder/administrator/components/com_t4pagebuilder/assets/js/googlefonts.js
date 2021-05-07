// google fonts
jQuery(document).ready(function($){
    // Open Row settings Modal
    $(document).on('click touchstart', '.btn-manager-font', function(e){
        e.preventDefault();
        var btndata = $(this).data();
        //init google font modal
        var $bodyfontmodal = $('.t4-google-font-modal');
        if(!$bodyfontmodal.parents().is('.themeConfigModal')) $bodyfontmodal.appendTo('.themeConfigModal');
        $('body').addClass('t4-modal-open');
        var nameFont = $('.load-google-fonts').val();
        if(nameFont) {
        	var nameFonts = Object.values(JSON.parse(nameFont));
        	nameFonts.forEach(function(font){
        		$bodyfontmodal.find('li[data-name="'+font.name+'"]').not('.btn').addClass('font-active');
        	});

        }
        $bodyfontmodal.modal('show');
        $('#t4-font-filter').val('');
        $('.t4-font-filter').hide();
    });
    $(document).on('click','li .t4-font-weight-popup', function(e) {
        e.stopPropagation();
    })
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

        if(offSet.top - offSetfixed.top > 360) {
            $(this).addClass('top');
        }
        if(offSet.left - offSetfixed.left < 500){
            $(this).addClass('right');
        }
        $(".t4-font-weight-popup").show();
        $('li.jub-font.font-focus').removeClass('font-focus');
        $(this).closest('li.jub-font').addClass('font-focus');

    });
    $('body').on('click','li.custom-font', function(e){
        var nameFont = $(this).data('name'),$fontType = 'custom',
            elemClass = this.className.split(" "),
            $classInputFont = $(this).closest('.t4-google-font-modal').data('input');
        $('#'+$classInputFont).val(nameFont);
        $('#'+$classInputFont).data('fontType',$fontType);
        $('#'+$classInputFont).data('loadweights','');
        $('#'+$classInputFont).trigger('change');
        $('.t4-font-weight-preview').html('');
        $('body').removeClass('t4-modal-open');
        $('.font-active').removeClass('font-active');
        $('.themeConfigModal').children().not('.t4-message-container').hide();
    });
    $('#t4-font-filter').on('keyup',function() {
    	var value = $(this).val().toLowerCase();
    	var fontCount = 0;
    	var $filterKey,$filterView;
    	if(!$('#jub-google-content').is(":visible")){
    		$filterKey = 'li.custom-google-font';
            $filterView = $('#custom-google-content');
    	}else{
    		$filterKey = 'li.jub-google-font';
            $filterView = $('#jub-google-content');
    	}
	    $($filterKey).not('#custom-local').filter(function() {
	      	$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
	      	if($(this).text().toLowerCase().indexOf(value) > -1){
	      		fontCount++;
	      	}
	    });
	    $('.t4-font-filter').show();
	    if(!value) $('.t4-font-filter').hide();
	    if(!fontCount){
	    	$filterView.find('.t4-font-filter').get(0).innerHTML = '<span class="alert alert-success">no result</span>';

	    }else{
	    	$filterView.find('.t4-font-filter').get(0).innerHTML = '<span class="alert alert-success">you have '+fontCount+' result</span>';
	    }
        $('#custom-local').hide();
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
        fontObj.name = nameFont;
        fontObj.weight = dataWeight;
        $('body').removeClass('t4-modal-open');
         $(".t4-font-weight-popup").hide();
         if(!fontInputs[nameFont]){
	        var $li = '<li class="btn btn-default"><span>'+nameFont+'</span><span class="icon-cancel"></span></li>'
	        $('.jpb-fonts-selected > ul').append($li);
         }
    	fontInputs[nameFont] = fontObj;
    	$('.load-google-fonts').val(JSON.stringify(fontInputs)).trigger('change');

    });
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
    $(document).on('click','span.icon-cancel',function(){
    	var namefont = $(this).parents('li').data('name');
    	var inputData = $('.load-google-fonts').val() ? JSON.parse($('.load-google-fonts').val()) : {};
    	delete inputData[namefont];
    	$('.t4-google-font-modal').find('li[data-name="'+namefont+'"]').removeClass('font-active');
    	$('.load-google-fonts').val(JSON.stringify(inputData)).trigger('change');
    	$(this).parents('li').remove();

    });
});
