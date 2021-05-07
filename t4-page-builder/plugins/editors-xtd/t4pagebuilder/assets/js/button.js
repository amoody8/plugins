let t4bPattern = /\<p data-name="t4b" data-content="t4b:(\d+):start"\>(.*)\<\/p\>/;

;(function(window,Joomla,document){
	"use strict";
	Joomla.JTextf = function(text,replace){

	}
	// This line is for Mootools b/c
	window.getSize = window.getSize || function(){return {x: window.innerWidth, y: window.innerHeight};};

	// @deprecated 4.0 Use directly Joomla.editors.instances[editor].replaceSelection(text);
	window.jInsertEditorText = function ( text, editor ) {
		Joomla.editors.instances[editor].replaceSelection(text);
	};

	window.T4confirm = function(params){
        if(jQuery('#confirmOverlay').length){
            // A confirm is already shown on the page:
            return false;
        }
        //create html button
        var buttonHTML = '';
        jQuery.each(params.buttons,function(name,obj){

            // Generating the markup for the buttons:

            buttonHTML += '<a href="#" class="btn button '+obj['class']+'">'+name+'<span></span></a>';

            if(!obj.action){
                obj.action = function(){};
            }
        });

        //create markup append to body
        var markup = [
            '<div id="confirmOverlay" class="t4-modal">',
            '<div id="confirmBox" class="modal-content">',
            '<div class="modal-body"><p>',params.message,'</p></div>',
            '<div class="modal-footer"><div id="confirmButtons">',
            buttonHTML,
            '</div></div></div></div>'
        ].join('');
        jQuery(markup).hide().appendTo('body').fadeIn();

        var buttons = jQuery('#confirmBox .button'),i = 0;

        jQuery.each(params.buttons,function(name,obj){
            buttons.eq(i++).click(function(){

                // Calling the action attribute when a
                // click occurs, and hiding the confirm.

                obj.action();
                window.T4confirm.hide();
                return false;
            });
        });
    };

    window.T4confirm.hide = function(){
        jQuery('#confirmOverlay').fadeOut(function(){
            jQuery(this).remove();
        });
    };

	var t4PageBuilder = {
		/**
		 * Find all TinyMCE elements and initialize TinyMCE instance for each
		 *
		 * @param {HTMLElement}  target  Target Element where to search for the editor element
		 *
		 * @since 3.7.0
		 */
		 page_id : "",
		 options : {},
		 styles : [],
		 editorExist :(window.tinyMCE !== undefined) ? "tinyMCE" : ((window.CodeMirror != undefined)? 'codemirror' : "") ,

		setupEditors: function ( target ) {
			target = target || document;
			var t4bOptions = Joomla.getOptions('xtd-t4pagebuilder', {}),
				editors = target.querySelectorAll('.t4b_editor');
			var t4b = window.t4b || {};
			t4b.langs = JSON.parse(t4bOptions['t4b-builder'].t4blangs);
			window.t4b = t4b;
			for(var i = 0, l = editors.length; i < l; i++) {
				var editor = editors[i].querySelector('textarea');
				this.setupEditor(editor, t4bOptions);
			}
		},
		/**
		 * Initialize TinyMCE editor instance
		 *
		 * @param {HTMLElement}  element
		 * @param {Object}       pluginOptions
		 *
		 * @since 3.7.0
		 */
		setupEditor: function ( element, t4bOptions ) {
			var $ = jQuery;
			let name = element ? element.getAttribute('name').replace(/\[\]|\]/g, '').split('[').pop() : 'default', // Get Editor name
			    options = t4bOptions ? t4bOptions['t4b-builder'] || {} : {}; 
			// Avoid an unexpected changes, and copy the options object
			options = Joomla.extend({}, options);
			if (element) {
				// We already have the Target, so reset the selector and assign given element as target
				options.selector = null;
				options.target   = element;
			}
			if(!options.page_id){
				// options.page_id = this.getT4bPageId($(element).val());
			}
			if(Joomla.editors.instances[options.element_name].instance.buttons){
				let buttonxtd = Joomla.editors.instances[options.element_name].instance.buttons;
				let xtdarr = {};
				Object.keys(buttonxtd).forEach(btn => {
					if(btn.indexOf('button-') > -1){
						xtdarr[btn] = buttonxtd[btn];
					}
				});
				options.buttonxtd = [];
			}
			this.page_id = options.page_id;
			this.options = options;
			this.elId = "#jform_"+name;
			this.element = element;
			this.name = name;
			if(options.asset_id){
				let data = {};
				data.title = jQuery("#jform_title").val();
				data.alias = jQuery("#jform_alias").val();

				data.id = options.page_id;
				data.asset_name = options.asset_name;
				data.created_by = options.user_id;
				data.access = 1;
				data.asset_id = options.asset_id;
				this.updatePageBuilder(data);
			}
			var content = element.value,
				$item =  $(element);
				$('#'+this.name+'-text').val(content);

				// <meta name="jpb" content="65:start">
				var t4bStatus = options.t4editor_status;
				// show preview and xxx
				$item.data('itemId',options.page_id);
				$item.data('template',options.templateDefault);
				$item.data('name','jpb-field-'+this.name);
				if(options.buttons) $item.data('buttons',options.buttons);
				this.showPreview($item, options.page_id);
			if (t4bStatus == 1) {
				jQuery('.t4b-editor').hide();
				this.isToggle(jQuery('.t4b-editor'));
			} else {
				jQuery('.t4b-editor').show();
				this.isToggle(jQuery('.t4b-editor'));
			}
		},
		toggleT4Builder(name) {
			var page_id = this.page_id;
			let newContent = "";
			if(!this.options.asset_id){
				if("jform.description" == this.options.asset_name){
					Joomla.renderMessages({'error':[Joomla.JText._("T4_PAGE_BUIDLER_WARNING_CREATE_NEW_CATEGORY")]});
				}else if ("jform.content" == this.options.asset_name) {
					Joomla.renderMessages({'error':[Joomla.JText._("T4_PAGE_BUIDLER_WARNING_CREATE_NEW_MOD")]});
				}else if ("jform.articletext" == this.options.asset_name) {
					Joomla.renderMessages({'error':[Joomla.JText._("T4_PAGE_BUIDLER_WARNING_CREATE_NEW_ARTICLE")]});
				}
				return false;
			}
			var content = this.t4bGetValue(this.editorExist);
			if(name == 't4b_disabled_editor'){

				// var warning = confirm(Joomla.JText._("T4_PAGE_BUIDLER_BUTTON_DISABLED_WARNING"));
				T4confirm({
					'title': "",
					'message': Joomla.JText._("T4_PAGE_BUIDLER_BUTTON_DISABLED_WARNING"),
					'buttons':{
		                'Yes'   : {
		                    'class' : 'btn-primary',
		                    'action': function(){
		                       let match = content.match(t4bPattern);
								window.Joomla.t4PageBuilder.t4bSetValue(cleanHtml(clearT4bEditor(content)),this.editorExist);
								window.Joomla.t4PageBuilder.t4bUpdateData(window.Joomla.t4PageBuilder.options.asset_name);
								// Joomla.submitbutton('article.apply');
		                    }
		                },
		                'No'    : {
		                    'class' : 'btn-default',
		                    'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
		                }
		            }
				});
			}else {
				if(!page_id && this.options.asset_id) {
					this.createNewPage(this.options.asset_name);
				}
			}

		},
		t4CreateNewDefaultData (asset_name) {
			let newPage = {};
			/*switch (asset_name) {
				case 'jform.articletext':
				case 'jform.categoriestext':
					newPage.title = jQuery("#jform_title").val();
					newPage.alias = jQuery("#jform_alias").val() || "";
					newPage.state = 1;
					break;
				case 'jform.content':
					newPage.title = jQuery("#jform_title").val();
					newPage.alias = jQuery("#jform_alias").val() || "";
					newPage.state = 1;
					break;
				case 'jform.description':
					
					break;
				default:
					// statements_def
					break;
			}*/
			newPage.title = jQuery("#jform_title").val();
			newPage.alias = jQuery("#jform_alias").val() || "";
			newPage.state = 1;
			newPage.asset_name = asset_name;
			newPage.asset_id = this.options.asset_id;
			newPage.page_html = cleanHtml(clearT4bEditor(this.options.target.value));
			newPage.created_by = this.options.user_id;

			return newPage;
		},
		createNewPage (asset_name) {
			let newpage = this.t4CreateNewDefaultData(asset_name);
			let ajax_url = this.options.ajax_url,
				builderParam = this.options.builderParam;

			fetch(
            	ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=create',{
				method: "POST",
                body: JSON.stringify(newpage),
                headers: {
                    "Content-Type": "application/json"
                }
	    	}).then(response => {
	            return response.json();
	        }).then(data => {
	        	if(data.error){
	        		Joomla.renderMessages({'error':[data.error]});
	        		return false;
	        	}
	        	Joomla.t4PageBuilder.options.page_id = data.newId;
	            Joomla.t4PageBuilder.page_id = data.newId;
	        	let content = this.t4bGetValue(Joomla.t4PageBuilder.editorExist);
				// create new value
				Joomla.t4PageBuilder.t4bSetValue(content,Joomla.t4PageBuilder.editorExist);
				var taskSubmit = this.t4SubmitButtonTask(asset_name);
				Joomla.submitbutton(taskSubmit);
	        })
		},
		t4bUpdateData(asset_name){
			let builderUrl = this.options.builderUrl,
				com_t4b_Path = this.options.jabuilderPath,
				builderParam = this.options.builderParam,
				ajax_url = this.options.ajax_url,
				baseUrl = this.options.siteUrl,
				$t4b_id = this.name;
				let data = {};
				data.id = this.options.page_id;
			fetch(
	            	ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=removeItem',{
	            		method: 'POST', // *GET, POST, PUT, DELETE, etc.
					    mode: 'cors', // no-cors, *cors, same-origin
					    cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
					    credentials: 'same-origin', // include, *same-origin, omit
					    headers: {
					      'Content-Type': 'application/json'
					      // 'Content-Type': 'application/x-www-form-urlencoded',
					    },
					    redirect: 'follow', // manual, *follow, error
					    referrerPolicy: 'no-referrer', // no-referrer, *client
					    body: JSON.stringify(data) // body data type must match "Content-Type" header
	            	}
	        	).then(response => {
	                return response.json();
	            }).then(data => {
	            	if(data.data){
	            		var taskSubmit = this.t4SubmitButtonTask(asset_name);
						Joomla.submitbutton(taskSubmit);

	            	}
	            })
		},
		t4bSetValue(text,type){
			text.replace("/<p style='display:none;'>T4bPreview</p>/","").trim();
			switch (type) {
				case 'codemirror':
					var editor = jQuery('.CodeMirror').get(0).CodeMirror;
			    	editor.setValue(text);
					break;
				case 'tinyMCE':
					window.tinyMCE.activeEditor.setContent(text);
					break;
				default:
					var key = Object.keys(window.Joomla.editors.instances);
					window.Joomla.editors.instances[key].setValue(text);
					break;
			}
		},
		t4bGetValue(type){
			var content = "";
			switch (type) {
				case 'codemirror':
					var editor = jQuery('.CodeMirror').get(0).CodeMirror;
			    	content = editor.getValue();
					break;
				case 'tinyMCE':
					content = window.tinyMCE.activeEditor.getContent();
					if(!content) content = "";

					break;
				default:
					var key = Object.keys(window.Joomla.editors.instances);
					window.Joomla.editors.instances[key].getValue();
					break;
			}
			return content;
		},
		isToggle(element){
			if(element.is(":hidden")){
				jQuery('.t4b-editor').show();
				jQuery('.t4b_editor').hide();
			}else{
				jQuery('.t4b-editor').hide();
				jQuery('.t4b_editor').show();
			}
		},
		showPreview($item,page_id,working){
			console.log('xx:', page_id);
			let styles, scripts, css, blockscss,page_css,
				builderUrl = this.options.builderUrl,
				com_t4b_Path = this.options.jabuilderPath,
				builderParam = this.options.builderParam,
				ajax_url = this.options.ajax_url,
				baseUrl = this.options.siteUrl,
				$t4b_id = this.name;

			// jQuery('#item-form').find('fieldset.adminform').find('#jform_articletext').css({'display':'none'});
			jQuery('.t4b_editor').css({'display':'none'});
			//add style to artile
			if(page_id) $item.data('page_id',page_id);
			jQuery('head').append(`<link rel="stylesheet" href="${builderUrl}css/style.css" />`);
	        // jQuery('head').append(`<link rel="stylesheet" href="${builderUrl}css/editor.css" />`);
	        jQuery('head').append(`<link rel="stylesheet" href="${com_t4b_Path}assets/css/ja-pagebuilder.css" />`);
	        var $preview = $item.data('preview') || jQuery(`<div class="jpb-preview">
	                    	<textarea id="${$t4b_id}-text" class="jpb-text"></textarea>
	                    	<iframe allowfullscreen="allowfullscreen"></iframe>
		                </div>
						<div class="jpb-edit" style="">
							<span class="btn btn-customize jpb-btn-edit"><span class="icon-pencil-2"></span><strong>${Joomla.JText._("T4_PAGE_BUIDLER_BUTTON_EDIT")}</strong></span>
						</div>
	                    <div class="btn-group btn-t4pagebuilder">
							<span class="btn btn-default btn-primary btn-t4b-disabled" onclick="Joomla.t4PageBuilder.toggleT4Builder('t4b_disabled_editor')"><span class="icon-pencil-2"></span> ${Joomla.JText._("T4_PAGE_BUIDLER_BUTTON_DISABLED")}</span>
						</div>
	                `),
	            $iframe = $preview.find('iframe'),
	            iframe = $iframe.get(0),
	            html = $item.val(),
	            $textarea = $preview.find('#' + $t4b_id + '-text');
	        	$textarea.val(html).hide();
        	if(localStorage.getItem('pageid') == itemid && localStorage.getItem('editpage') == 'true'){
	            jQuery('body').addClass('jpb-loading');
	        }else{
	            jQuery('body').removeClass('jpb-loading');
	        }
	        // default, get item value to text and hide
	        if (!$item.data('preview')) {
	        	jQuery('<div class="t4b-editor"/>').insertAfter(jQuery('.t4b_editor'))
	            $preview.appendTo(jQuery('.t4b-editor'));

	            $item.data('preview', $preview);
	            $item.data('textarea', $textarea);

	            jQuery(document).on('click', '.jpb-btn-edit', function(e) {
	            	Joomla.removeMessages();
	                localStorage.setItem("editpage", true);
	                if(Joomla.t4PageBuilder.options.front_end){
	                	localStorage.setItem("T4EDIT_VISITED", true);
	                }else{
	                	localStorage.setItem("T4EDIT_VISITED", false);
	                }
	                localStorage.setItem("pageid", page_id);
	                Joomla.t4PageBuilder.loadEditor($item);
	                return false;
	            })
	            // fix enter on input cause the editor reload
	            $preview.on('keypress', function(e) {
	                if (e.originalEvent.keyCode == 13) {
	                    e.stopPropagation();
	                    return false;
	                }
	            })
	        }
	        if(page_id) $item.data('itemId',page_id);
	        // load preview content
	        var itemid = page_id,
	             template = $item.data('template'),
	            previewUrl = ajax_url + '&' + builderParam + '=preview&id=' + itemid + (working ? '&working=1' : '') + (template ? '&template=' + template : '');
	        jQuery.getJSON(previewUrl, function(data) {
	            styles = data.styles;
	            scripts = data.scripts;
	            css = data.css.join('\n');
	            page_css = data.data.page_css;
	            blockscss = data.data.blockscss;
	            // add param to style url to prevent cache
	            var t = new Date().getTime();
	            styles = styles.map(url => url.match(/t=/) ? url : url + (url.match(/\?/) ? '&' : '?') + 't=' + t);
	            
	            var icons = JSON.parse(Joomla.t4PageBuilder.options.loadIcons);
	            var materialIcon = icons.material_icons.material_icons ? ((icons.material_icons.url_type == 'cdn') ? "cdn" : icons.material_icons.custom_url) : "";
	            var awsIcon = icons.awesome_icons.awesome_icons ? ((icons.awesome_icons.url_type == 'cdn') ? "cdn" : icons.awesome_icons.custom_url) : "";
	            
	            var head = '';
	            styles.forEach(url => { head += `<link rel="stylesheet" href="${url}" />\n` });    
	            // for preview only
	            head += `<link rel="stylesheet" href="${builderUrl}css/editor-preview.css" />\n`;
	            head += `<link rel="stylesheet" href="${builderUrl}css/ja_pagebuilder.css" />\n`;
	            head += `<link rel="stylesheet" href="${builderUrl}vendors/animate/animate.css" />\n`;
	            if(materialIcon && materialIcon == 'cdn'){
	                head += `<link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons" />\n`;


	            }else if(materialIcon && materialIcon !== 'cdn'){
	                head += `<link rel="stylesheet" href="${materialIcon}" />\n`;
	            }
	            head += `<link rel="stylesheet" href="${builderUrl}css/googlefonts.css" />\n`;
	            if(awsIcon && awsIcon == 'cdn'){
	               head += `<link rel="stylesheet" href="${builderUrl}css/awesome_5.11.2.min.css" />\n`;

	            }else if(awsIcon && awsIcon !== 'cdn'){
	                head += `<link rel="stylesheet" href="${awsIcon}" />\n`;
	            }
	            head += `<link rel="stylesheet" href="${builderUrl}css/googlefonts.css" />\n`;

	            //init google fonts
	            let googlfonts = JSON.parse(Joomla.t4PageBuilder.options.loadgooglefont);
	            let fontArr = Object.values(googlfonts),fontname = [];
	            fontArr.forEach(function(font){
	                let fontval = font.name;
	                if(font.weight.length) fontval += ":"+font.weight.join(',');
	                fontname.push(fontval);
	            });
              	if(fontname){
                    head += '<link id="linkloadfonts" href="https://fonts.googleapis.com/css?family='+escape(fontname.join('|'))+'" rel="stylesheet" type="text/css" />';
                }
	            scripts.forEach(url => { head += `<script type="text/script" src="${url}"></script>\n` });

	            // using working html if exist
	            if (data.html) {
	                html = data.html;
	            }

	            /*jQuery('.jpb-add-block').show();
	            jQuery('.jpb-edit').hide();*/

	            // set value for $preview
	            var htmlcontent = $item.val();
	            /*if(htmlcontent){
	                jQuery('.jpb-edit').show();
	                jQuery('.jpb-add-block').hide();
	            }*/
	            $textarea.val(htmlcontent);
	            $preview.data('html', htmlcontent);
	            var cls = 'jpb-editor jpb-page';
	            if (data.data.commands && data.data.commands.indexOf('sw-dark-theme') > -1) cls += ' jpb-dark';

	            var previewHtml = `<!DOCTYPE html>
	                <html lang="en">
	                <head>
	                  <base href="${baseUrl}" />
	                  <meta charset="UTF-8">
	                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	                  <title>T4 Item Preview</title>

	                  ${head}
	                </head>

	                <body class="${cls}">
	                    <div class="jpb">
	                        ${html}
	                    </div>
	                </body>
	            </html>`;
	           	var doc = iframe.contentWindow.document;
	            doc.open();
	            doc.clear();
	            doc.write(previewHtml);
	            doc.close();
	           	var styleid = data.data.states && data.data.states.templatestyle || '';
	            // fetch(siteUrl + (siteUrl.match(/\?/) ? '&' : '?') + builderParam + 'head=1&templateStyle=' + styleid)
	            fetch(
	            	Joomla.t4PageBuilder.buildAjaxUrl('head',{styleid})
	        	).then(response => {
	                return response.json();
	            }).then(urls => {
	                // apply new style
	                const head = doc.querySelector('head');
	                if (!urls) return;
	                for (var url in urls) {
	                    let link = doc.createElement('link');
	                    link.rel = 'stylesheet';
	                    link.type = 'text/css';
	                    link.href = url;
	                    link.setAttribute('templatestyle', '');
	                    head.appendChild(link);
	                }
	                let cssStyle = doc.createElement('style');
	                cssStyle.setAttribute('type',"text/css");
	                 if (css || blockscss || page_css){
                        let cssEl = "";
                        if(page_css) cssEl += page_css;
                        if(blockscss) cssEl += blockscss;
                        if(css) cssEl += css;
                        cssStyle.innerHTML = cssEl;
                        head.appendChild(cssStyle);
                    }
	                jQuery('body').removeClass('jpb-loading');
	                if(localStorage.getItem('pageid') == itemid && localStorage.getItem('editpage') == 'true'){
		                Joomla.t4PageBuilder.loadEditor($item);
		                if(Joomla.t4PageBuilder.options.front_end){
		                	localStorage.setItem("T4EDIT_VISITED", true);
		                }else{
		                	localStorage.setItem("T4EDIT_VISITED", false);
		                }
		            }
	            })
	        });
		},
		t4SubmitButtonTask(asset_name){
			switch (asset_name) {
				case 'jform.articletext':
				case 'jform.categoriestext':
	            	return 'article.apply';
	            	
				case 'jform.content':
					if(Joomla.t4PageBuilder.options.front_end) return 'config.save.modules.apply';
	            	return 'module.apply';

				case 'jform.description':
	            	return 'category.apply';
				default:
					return 'apply';
			}
		},
		buildAjaxUrl (action, data, callback) {
			var baseUrl = this.options.baseUrl,
	    		builderUrl = this.options.builderUrl,
	    		siteUrl = this.options.siteUrl,
	    		builderParam = this.options.builderParam,
	    		ajax_url = this.options.ajax_url/*,*/


	    		;
	        // specific for fetch head urls for preview
	        if (action == 'head') {
	            return ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=head&'+ builderParam + 'head=1&templateStyle=' + data.styleid
	        }


	        // ajax url
	        var url = ajax_url;
	        url += (url.match(/\?/) ? '&' : '?') + builderParam + '=' + action;
	        if (typeof(data) === 'object' && data) {
	            url += '&' + Object.keys(data).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k])).join('&');
	        }
	        return url;
	    },
		loadEditor($item){
	    	var $ = jQuery;
	    	let jpb_devmode = this.options.devmode,
	    		baseUrl = this.options.baseUrl,
	    		builderUrl = this.options.builderUrl,
	    		siteUrl = this.options.siteUrl,
	    		builderParam = this.options.builderParam,
	    		ajax_url = this.options.ajax_url,
	    		loadIcons = JSON.parse(this.options.loadIcons);
    		this.styles.push(builderUrl+'css/editor-canvas.css');
	        var cof = $('body').css('overflow');
	        $('body').css('overflow', 'hidden').data('cof', cof);

	        let $editingItem = $item;
	        let itemHTML = $item.val();
	        // create editor iframe
	        var id = $item.data('editorid');
	        var $editor = $(`<div class="jpb-editor" id="t4b-content-editor">
	                    <iframe allowfullscreen="allowfullscreen"></iframe>
	                </div>`),
	            iframe = $editor.find('iframe').get(0);
	            // baseUrl = getBaseUrl();

	        var itemId = ($item.data('page_id') ? $item.data('page_id') : Joomla.t4PageBuilder.page_id )|| 0,
	            itemHtml = $item.data('textarea').val();
	        $editor.insertAfter(jQuery('.t4b_editor'));

	        // list buttons
	        var buttons = [];

	        if ($item.data('buttons') != undefined) {
	            $item.data('buttons').each((i, button) => {
	                var $button = $(button),
	                    label = $button.attr('title'),
	                    name = label.toLowerCase().replace(' ', '-');
	                $button.data('name', name);
	                buttons.push({ name, label });
	            })
	        }

	        var t = new Date().getTime(),
            gjs = jpb_devmode ? "0.16.27" : t,
            t4template = $item.data("template") ? $item.data("template") : "",
            //var styles = ${JSON.stringify(styles.concat(baseUrl + 'media/'+template+'/'+tempId()+'.css?t=' + t,baseUrl + 'templates/'+template+'/css/off-canvas.css?t=' + t))};
            buildAjaxUrl = function (action, data, callback) {
              // specific for fetch head urls for preview
              if (action == "head") {
                return (
                  ajax_url +
                  (ajax_url.match(/\?/) ? "&" : "?") +
                  builderParam +
                  "=head&" +
                  builderParam +
                  "head=1&templateStyle=" +
                  data.styleid
                );
              }

              // ajax url
              var url = ajax_url;
              url +=
                (url.match(/\?/) ? "&" : "?") + builderParam + "=" + action;
              if (typeof data === "object" && data) {
                url +=
                  "&" +
                  Object.keys(data)
                    .map(
                      (k) =>
                        encodeURIComponent(k) +
                        "=" +
                        encodeURIComponent(data[k])
                    )
                    .join("&");
              }
              return url;
            },
            editorHtml = `<!DOCTYPE html>
	            <html lang="en">
	            <head>
	                <base href="${baseUrl}" />
	                <meta charset="UTF-8">
	                <meta http-equiv="X-UA-Compatible" content="IE=edge">
	                <meta name="viewport" content="width=device-width, initial-scale=1.0">
	                <title>T4 Item Preview</title>
	                <link rel="stylesheet" href="${builderUrl}css/style.css?t=${t}" />
	                <link rel="stylesheet" href="${builderUrl}css/editor.css?t=${t}" />
	                <link rel="stylesheet" href="${builderUrl}css/googlefonts.css?t=${t}" />
	                <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
	                <link rel="stylesheet" href="${builderUrl}vendors/toastr/toastr.min.css?t=${t}" />
	                <script>
	                    var siteUrl = "${siteUrl}";
	                    var builderUrl = "${builderUrl}";
	                    var baseUrl = "${baseUrl}";
	                    var itemId = ${itemId};
	                    var builderParam = "${builderParam}";
	                    var styles = ${JSON.stringify(
                        this.styles.concat(
                          "https://fonts.googleapis.com/icon?family=Material+Icons&t=" +
                            t,
                          builderUrl + "css/ja_pagebuilder.css?t=" + t
                        )
                      )};
	                    var t4template = "${t4template}";
	                    var loadgooglefont = ${JSON.stringify(
                        Joomla.t4PageBuilder.options.loadgooglefont
                      )};
	                    var xtdbuttons = ${JSON.stringify(buttons)};
	                    var customFont = '${JSON.stringify({})}';
	                    var loadIcons = ${JSON.stringify(loadIcons)};
						var ajax_url = "${ajax_url}";
						var jpb_devmode="${Joomla.t4PageBuilder.options.devmode}";
	                    var editor_type = "${
                        Joomla.t4PageBuilder.options.asset_name
                      }";
	                    var xtdbuttons = ${JSON.stringify(
                        Joomla.t4PageBuilder.options.buttonxtd
                      )};
	                    var buildAjaxUrl = ${buildAjaxUrl};
	                </script>

	                <script src="${builderUrl}vendors/jquery/jquery.min.js?t=${t}"></script>
	                <script src="${builderUrl}vendors/toastr/toastr.min.js?t=${t}"></script>
	                <script src="${builderUrl}js/googlefonts.js?t=${t}"></script>
	                <script src="${builderUrl}vendors/ckeditor/ckeditor.js?v=${t}"></script>
	                <script src="${builderUrl}vendors/grapesjs/dist/grapes.js?v=${gjs}"></script>
	                <script src="${builderUrl}js/plugin-t4.min.js?t=${t}"></script>

	            </head>

	            <body class="jub-editor jpb-loading">

	                <div id="editor-container">
	                </div>

	                <script src="${builderUrl}js/editor-loader.js?t=${t}" type="module"></script>

	            </body>
	            </html>`;
	        var doc = iframe.contentWindow.document;
	        doc.open();
	        doc.clear();
	        doc.write(editorHtml);
	        doc.close();

		    // open media box
		    let extendMedia = false;

	        // register for open media
	        $(doc).on('t4:media-open', function(e) {

	            var $layoutmediainput = $('#' + $editingItem.data('name') + '_media'),
	                $layoutmedia = $layoutmediainput.closest('#t4-media-joomla'),
	                $btnSelect = $layoutmedia.find('.button-select'),
	                $btnCancel = $layoutmedia.find('.button-cancel'),
	                $btnSelected = $layoutmedia.find('.button-save-selected');
	                 //init modal
                $('#imageModal_' + $editingItem.data('name') + '_media').find('.modal-body').empty();
                $('#imageModal_' + $editingItem.data('name') + '_media').addClass('in');
                if($('.modal-backdrop').length) $('.modal-backdrop').remove();
                $('<div class="modal-backdrop fade in show"></div>').appendTo('body');
	            if (!extendMedia) {
	                var onChange = function(imgUrl) {
	                    if (e.type == 't4:media-open') {
	                        const urlUpdateEvent = new CustomEvent('t4:media-selected');
	                        urlUpdateEvent.selectedUrl = imgUrl;
	                    	$layoutmedia.find('.modal').modal('hide');
	                        doc.dispatchEvent(urlUpdateEvent);
	                    }
	                }

	                var jDoit = Joomla.doIt;
	                if (jDoit) {
	                    Joomla.doIt = function(resp, input, fieldClass) {
	                        jDoit(resp, input, fieldClass);
	                        // e.originalEvent.inputUrl.value = fieldClass.basePath + '/' + editor.value;
	                        onChange(input.value);
	                        // const imgUrl = input.value;
	                        // if (e.type == 't4:media-open' && window.editor.getSelected().get('src') != imgUrl) {
	                        //  const urlUpdateEvent = new CustomEvent('t4:media-selected');
	                        //  urlUpdateEvent.selectedUrl = imgUrl;
	                        //  document.dispatchEvent(urlUpdateEvent);
	                        // }
	                    }
	                } else {
	                    // bind change for input
	                    $layoutmediainput.on('change', function() { 
	                    	onChange(this.value);
	                    });
	                }

	                extendMedia = true;
	            }
	            $btnSelect.get(0).dispatchEvent(new Event('click'));

	        }).on('t4:xtd-init', function(e) {
	            var model = e.originalEvent.detail,
	                name = model.get('name');
	            // find correct button
	            var button = $item.data('buttons').filter((i, el) => $(el).data('name') == name);
	            if (button.length) button.get(0).click();
	        })
	        $item.on('xtd-insert', (e, value) => {
	            const xtdInsertEvent = new CustomEvent('t4:xtd-insert');
	            xtdInsertEvent.insertValue = value;
	            doc.dispatchEvent(xtdInsertEvent);
	        }).on('t4:syncContent', function(e, itemId, html) {
	            $item.val(addT4InfoData(html, itemId));
	            $('#' + $item.data('name')).val(itemId);
	        })

	        $item.data('builderEditor', $editor);

		},
		syncTextContent ($item) {
	        console.log('Sync content from Text editor: ', $item);
	        const $id = $('#' + $item.data('name')),
	            $textarea = $item.data('textarea'),
	            $preview = $item.data('preview'),
	            itemId = $id.val(),
	            html = cleanHtml($item.data('textEditor').getValue());
	        $textarea.val(html);
	        $item.val(addT4InfoData(html, itemId));
	        this.syncTextareaToPreview(itemId, $textarea, $preview);
	    },
	    syncBuilderContent ($item) {
	        jQuery('body').trigger('joomla:syncContent', [$item]);
	    },
	    syncTextareaToPreview (itemId, $textarea, $preview, updatePreview) {
	        if (!itemId) return false;
	        let builderUrl = this.options.builderUrl,
				com_t4b_Path = this.options.jabuilderPath,
				builderParam = this.options.builderParam,
				ajax_url = this.options.ajax_url,
				baseUrl = this.options.siteUrl,
				$t4b_id = page_id;

	        var textval = cleanHtml($textarea.val()),
	            html = cleanHtml($preview.data('html'));
	        console.log('Text: ', $textarea.val(), '\n\n', textval, '\n\n', html);

	        if (textval != html) {
	            // need update working content for T4 item
	            var deferred = $.post(ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=saveHtml&id=' + itemId, {
	                html: textval
	            })
	            if (updatePreview) {
	                $preview.data('html', textval);
	                return deferred;
	            }
	        }

	        return false;
	    },
	    /**
	     * EDITOR Helper
	     * Sync content to Preview, Joomla field before save
	     */
	    beforeSave ($item) {
	        const $id = jQuery('#' + $item.data('name')),
	            $textarea = $item.data('textarea'),
	            $preview = $item.data('preview'),
	            itemId = $id.val();
	        // update $item value
	        $item.val(addT4InfoData($textarea.val(), $id.val()));
	        return this.syncTextareaToPreview(itemId, $textarea, $preview);
	    },
	     // Before Save, check and update editting content
	    updateEdittingContent () {
			let newContent = jQuery('#jform_articletext').val();
	    	if(Joomla.t4PageBuilder.editorExist){
	    		if(newContent){
					window.tinyMCE.editors.jform_articletext.setContent(newContent);
	    		}else{
	    			window.tinyMCE.editors.jform_articletext.save();
	    		}
			}else{
				Joomla.editors.instances.jform_articletext.setValue(newContent);
			}
			// this.syncBuilderContent(jQuery('#jform_articletext'));
	    },
	    updatePageBuilder (data) {
	    	let ajax_url = this.options.ajax_url,
				builderParam = this.options.builderParam;
			fetch(
	            	ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=updatet4ref',{
					method: "POST",
	                body: JSON.stringify(data),
	                headers: {
	                    "Content-Type": "application/json"
	                }
	    	}).then(response => {
	            return response.json();
	        }).then(data => {

	        })
	    },
	    getT4bPageId (html) {
	    	let match = html.match(t4bPattern);
	    	if (Array.isArray(match) && match.length) {
	    		return match[1];
	    		
			}
			return null;
	    }

	}
	
	 // trim unused space
    var cleanHtml = function(html) {
        return html.replace(/\s*\>\s+\n?/g, '>').replace(/\n?\s+\</g, '<');
    }
    var clearT4bEditor = function(html){
    	let html_rep = '';
    	html_rep = html.replace(/\<p(.*)data-name="t4b" data-content="t4b:(.*):end"\>[^>]+>/gi, '').replace(/\<p(.*)data-name="t4b" data-content="t4b:(.*):start"\>[^>]+>/gi, '').trim();
    	return html_rep;
    }
	var addT4InfoData = function(html, itemId) {
        html = cleanHtml(clearT4bEditor(html));
        if (!itemId) return html;

        const start = `<p data-name="t4b" data-content="t4b:${itemId}:start">&nbsp;</p>`;
        const end = `<p data-name="t4b" data-content="t4b:${itemId}:end">&nbsp;</p>`;
        const startFull = `<p data-name="t4b" data-content="t4b:${itemId}:start-full">&nbsp;</p>`;
        const endIntro = `<p data-name="t4b" data-content="t4b:${itemId}:end-intro">&nbsp;</p>`;

        // add info for readmore
        html = html.replace(/<hr\s+id="system-readmore"[^>]*>/, `${endIntro}<hr id="system-readmore">${startFull}`);

        html = start + html + end;


        return html;
    }
    var destroyEditor = function($item) {
    	var $ = jQuery;
        $('.t4editor-editor').remove();
        $item.data('builderEditor').remove();
        $item.removeData('builderEditor');
        localStorage.setItem('editpage',false);
        if ($('.modal-backdrop').length) $('.modal-backdrop').remove();
        jQuery('body').css('overflow', jQuery('body').data('cof'));
    }
	Joomla.t4PageBuilder = t4PageBuilder;

    jQuery(document).ready(($) => {
    	var configOpt = Joomla.getOptions('xtd-t4pagebuilder', {});
		// move media box to body and hide it
		var $editingItem = $('#'+ configOpt['t4b-builder'].element_name);
        $('body').on('jpb:update', (e, itemId, data) => {
        	let t4bOptions = Joomla.t4PageBuilder.options,
				builderUrl = t4bOptions.builderUrl,
				com_t4b_Path = t4bOptions.jabuilderPath,
				builderParam = t4bOptions.builderParam,
				ajax_url = t4bOptions.ajax_url,
				baseUrl = t4bOptions.siteUrl,
				$t4b_id = Joomla.t4PageBuilder.page_id,
				data_html = addT4InfoData(data.html, itemId);
            // update content
            Joomla.t4PageBuilder.beforeSave($editingItem);
            Joomla.t4PageBuilder.t4bSetValue(data_html,Joomla.t4PageBuilder.editorExist);
            $('#' + $editingItem.data('name')).val(itemId);

            fetch(Joomla.t4PageBuilder.buildAjaxUrl('update', { itemId }), {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => {
               return response.json();
            }).then(message => {
                if(true === message.data){
                    editor.toastr.success(Joomla.JText._('T4_PAGE_BUIDLER_SAVED'));
		            // show preview then destroy the editor
		            Joomla.t4PageBuilder.showPreview($editingItem,itemId);
		    		destroyEditor($editingItem);

                }else{
                     editor.toastr.error(Joomla.JText._('T4_PAGE_BUIDLER_SAVE_ERROR'));
                }
            });
        }); 
        $('body').on('t4b:exit', (e, itemId, data) => {
        	let t4bOptions = Joomla.t4PageBuilder.options,
				builderUrl = t4bOptions.builderUrl,
				com_t4b_Path = t4bOptions.jabuilderPath,
				builderParam = t4bOptions.builderParam,
				ajax_url = t4bOptions.ajax_url,
				baseUrl = t4bOptions.siteUrl,
				$t4b_id = Joomla.t4PageBuilder.page_id;
            // update content
            $editingItem.val(addT4InfoData(data.html, itemId));
            $('#' + $editingItem.data('name')).val(itemId);

            fetch(Joomla.t4PageBuilder.buildAjaxUrl('update', { itemId }), {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => {
               return response.json();
            }).then(message => {
                if(true === message.data){
                    editor.toastr.success(Joomla.JText._('T4_PAGE_BUIDLER_SAVED'));
		            // show preview then destroy the editor
		            Joomla.t4PageBuilder.showPreview($editingItem,itemId);
		    		destroyEditor($editingItem);

                }else{
                     editor.toastr.error(Joomla.JText._('T4_PAGE_BUIDLER_SAVE_ERROR'));
                }
            });
        });
        $('body').on('jpb:close', (e, itemId, html) => {
            var t4edit_visted = localStorage.getItem('T4EDIT_VISITED');
            if(t4edit_visted == 'true'){
                // var pageEdit = localStorage.getItem('T4EDIT_PAGE');
                localStorage.setItem('T4EDIT_VISITED',false);
                // window.location.href = pageEdit;
                // return false;
            }
            // update content
            $editingItem.val(addT4InfoData(html, itemId));
            $('#' + $editingItem.data('name')).val(itemId);
            // show preview then destroy the editor
            Joomla.t4PageBuilder.showPreview($editingItem,itemId);
            destroyEditor($editingItem);
        });
    })
   
    // extend joomla submit button
    /*var _submitform = Joomla.submitform;
    Joomla.submitform = function(task, form, validate) {
        // call before submit
        Joomla.t4PageBuilder.updateEdittingContent();
        _submitform(task, form, validate);
    }*/
	// Init on DOMContentLoaded
	document.addEventListener('DOMContentLoaded', function () {
		jQuery('#item-form').find('fieldset.adminform').addClass('t4b_editor');
		if(!jQuery('.t4b_editor').length){
			jQuery('.js-editor-tinymce').addClass('t4b_editor');
			jQuery('.js-editor-none').addClass('t4b_editor');
			if(jQuery('.CodeMirror').length){
				jQuery("<div class='t4b-custom-code t4b_editor' />").append(jQuery('#editor').find('#jform_articletext'),
					jQuery('#editor').find('.label'),jQuery('#editor').find('.CodeMirror'),jQuery('#editor').find('#editor-xtd-buttons')).appendTo(jQuery('#editor'));
			}
		}
		var btnEditor = `
	                    <div class="btn-group btn-t4pagebuilder">
							<span class="btn btn-default btn-primary btn-t4b-enabled" onclick="Joomla.t4PageBuilder.toggleT4Builder('t4_page_enabled')"><span class="icon-pencil-2"></span> ${Joomla.JText._("T4_PAGE_BUIDLER_BUTTON_ENABLED")}</span>
						</div>`;
		jQuery('.t4b_editor').find(".btn-toolbar").append(btnEditor);
		Joomla.t4PageBuilder.setupEditors();
		
    	let mediaField = Joomla.t4PageBuilder.options.mediaField;
		jQuery(mediaField).appendTo(jQuery('body')).css({ position: 'relative', 'z-index': 10000000 }).find('.input-group').hide();

		if(jQuery(document).find('.field-media-wrapper').length) jQuery(document).find('.field-media-wrapper').fieldMedia();
		// Init in subform field
		if(window.jQuery) {
			jQuery(document).on('subform-row-add', function (event, row) {
				Joomla.t4PageBuilder.setupEditors(row);
			});
		}
	});
})(window,Joomla,document);
