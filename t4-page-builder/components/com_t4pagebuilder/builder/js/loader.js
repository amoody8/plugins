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

(function($) {
    var siteUrl, builderUrl, styles, scripts, $editingItem;

    /**
     * Build ajax request / response url
     */
    var buildAjaxUrl = function(action, data, callback) {

        // ajax url
        var url = ajax_url;
        // specific for fetch head urls for preview
        if (action == 'head') {
            url += (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=head&' + builderParam + 'head=1&templateStyle=' + data.styleid;
            if (data.pageid) url += "&pageid=" + data.pageid;
        } else {
            url += (url.match(/\?/) ? '&' : '?') + builderParam + '=' + action;
            if (typeof(data) === 'object' && data) {
                url += '&' + Object.keys(data).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k])).join('&');
            }
        }

        return url;
    }

    var showPreview = function($item, working) {
        var id = $item.attr('name').replace(/[\[\]]/g, '');
        $item.data('editorid', id);
        var $preview = $item.data('preview') || $(`<div class="jpb-preview">
                    <textarea id="${id}-text" class="jpb-text"></textarea>
                    <iframe allowfullscreen="allowfullscreen"></iframe>
                </div>`),
            $iframe = $preview.find('iframe'),
            iframe = $iframe.get(0),
            html = $item.val().replace(/<jdoc:include([^\/]*)\/>/gi, '<jdoc:include$1></jdoc:include>'),
            $textarea = $preview.find('#' + id + '-text');
        // default, get item value to text and hide
        $textarea.val(cleanData($item.val())).hide();
        baseUrl = getBaseUrl();

        // load preview content
        var $id = $('#' + $item.data('name')),
            itemid = $id.val(),
            template = $item.data('template'),
            previewUrl = ajax_url + '&' + builderParam + '=preview&id=' + itemid + (working ? '&working=1' : '') + (template ? '&template=' + template : '');
        if (localStorage.getItem('editinline') == 'true' || localStorage.getItem('editpage') == 'true') {
            $('body').addClass('jpb-loading');
        } else {
            $('body').removeClass('jpb-loading');
        }
        // insert after item
        if (!$item.data('preview')) {
            $preview.insertAfter($item);
            $item.data('preview', $preview);
            $item.data('textarea', $textarea);

            $(document).on('click', '.jpb-btn-edit', function(e) {
                localStorage.setItem("editpage", true);
                localStorage.setItem("pageid", itemid);
                $('body').addClass('jpb-loading');
                loadEditor($item);
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
        $.getJSON(previewUrl, function(data) {
            styles = data.styles;
            scripts = data.scripts;
            page_css = data.data.page_css;
            css = data.css.join('\n');
            blockscss = data.data.blockscss;
            blocksjs = data.data.blocksjs;
            // add param to style url to prevent cache
            var t = new Date().getTime();
            styles = styles.map(url => url.match(/t=/) ? url : url + (url.match(/\?/) ? '&' : '?') + 't=' + t);
            var icons = JSON.parse(loadIcons);
            var materialIcon = icons.material_icons.material_icons ? ((icons.material_icons.url_type == 'cdn') ? "cdn" : icons.material_icons.custom_url) : "";
            var awsIcon = icons.awesome_icons.awesome_icons ? ((icons.awesome_icons.url_type == 'cdn') ? "cdn" : icons.awesome_icons.custom_url) : "";
            var head = '';
            styles.forEach(url => { head += `<link rel="stylesheet" href="${url}" />\n` });
            // for preview only
            head += `<link rel="stylesheet" href="${builderUrl}css/editor-preview.css" />\n`;
            head += `<link rel="stylesheet" href="${builderUrl}css/ja_pagebuilder.css" />\n`;
            head += `<link rel="stylesheet" href="${builderUrl}vendors/animate/animate.css" />\n`;
            if (materialIcon && materialIcon == 'cdn') {
                head += `<link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons" />\n`;

            } else if (materialIcon && materialIcon !== 'cdn') {
                head += `<link rel="stylesheet" href="${materialIcon}" />\n`;
            }
            head += `<link rel="stylesheet" href="${builderUrl}css/googlefonts.css" />\n`;
            if (awsIcon && awsIcon == 'cdn') {
                if(!isT4){
                    head += `<link rel="stylesheet" href="${builderUrl}css/awesome_5.11.2.min.css" />\n`;
                }

            } else if (awsIcon && awsIcon !== 'cdn') {
                head += `<link rel="stylesheet" href="${awsIcon}" />\n`;
            }

            //init google fonts
            let googlfonts = loadgooglefont;
            let fontArr = Object.values(googlfonts),
                fontname = [];
            fontArr.forEach(function(font) {
                let fontval = font.name;
                if (font.weight.length) fontval += ":" + font.weight.join(',');
                fontname.push(fontval);
            });
            if (fontname) {
                head += '<link id="linkloadfonts" href="https://fonts.googleapis.com/css?family=' + escape(fontname.join('|')) + '" rel="stylesheet" type="text/css" />';
            }
            scripts.forEach(url => { head += `<script type="text/script" src="${url}"></script>\n` });

            // using working html if exist
            if (data.html) {
                html = data.html;
            }

            $('.jpb-add-block').show();
            $('.jpb-edit').hide();

            // set value for $preview
            var htmlcontent = data.data.html ? data.data.html : cleanData($item.val());
            if (htmlcontent) {
                $('.jpb-edit').show();
                $('.jpb-add-block').hide();
            }

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

            fetch(buildAjaxUrl('head', { styleid, pageid: itemid }))
            .then(response => {
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
                cssStyle.setAttribute('type', "text/css");
                if (css || blockscss || page_css) {
                    let cssEl = "";
                    if (page_css) cssEl += page_css;
                    if (blockscss) cssEl += blockscss;
                    if (css) cssEl += css;
                    cssStyle.innerHTML = cssEl;
                    head.appendChild(cssStyle);
                }
                let jsInline = doc.createElement('script');
                jsInline.setAttribute("type", "text/css");
                jsInline.setAttribute("class", "t4b-blockjs");
                if(blocksjs){
                    jsInline.innerHTML = blocksjs;
                    head.appendChild(jsInline);
                }

            });
            if (localStorage.getItem('editinline') == 'true' || localStorage.getItem('editpage') == 'true') {
                if(localStorage.getItem('editinline')) localStorage.setItem('T4EDIT_VISITED', false);
                loadEditor($item);
            }

        });
    }


    /**
     * BUILDER EDITOR
     */
    var loadEditor = function($item) {
        var cof = $('body').css('overflow');
        $('body').css('overflow', 'hidden').data('cof', cof);

        $editingItem = $item;
        // create editor iframe
        var id = $item.data('editorid');
        var $editor = $(`<div class="jpb-editor" id="${id}-editor">
                    <iframe allowfullscreen="allowfullscreen"></iframe>
                </div>`),
            iframe = $editor.find('iframe').get(0),
            baseUrl = getBaseUrl();

        var $id = $('#' + $item.data('name')),
            itemId = parseInt($id.val()) || 0,
            itemHtml = $item.data('textarea').val();
        $editor.insertAfter($item);
        // list buttons
        var buttons = [];
        if ($item.data('buttons') != undefined) {
            $item.data('buttons').each((i, button) => {
                var $button = $(button),
                    label = $button.attr('title'),
                    name = label.toLowerCase().replace(' ', '-');
                buttons.push({ name, label });
                $button.data('name', name);
            })
        }
        var t = new Date().getTime(),
            gjs = (jpb_devmode) ? '0.16.34' : t,
            t4template = $item.data('template') ? $item.data('template') : '',
            //var styles = ${JSON.stringify(styles.concat(baseUrl + 'media/'+template+'/'+tempId()+'.css?t=' + t,baseUrl + 'templates/'+template+'/css/off-canvas.css?t=' + t))};

            editorHtml =
            `<!DOCTYPE html>
            <html lang="en">
            <head>
                <base href="${baseUrl}" />
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>T4 Item Editor</title>
                <link rel="stylesheet" href="${builderUrl}css/style.css?t=${t}" />
                <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
                <link rel="stylesheet" href="${builderUrl}css/googlefonts.css?t=${t}" />
                <link rel="stylesheet" href="${builderUrl}vendors/toastr/toastr.min.css?t=${t}" />
                <link rel="stylesheet" href="${builderUrl}css/editor.css?t=${t}" />
                <script>
                    var siteUrl = "${siteUrl}";
                    var builderUrl = "${builderUrl}";
                    var baseUrl = "${baseUrl}";
                    var itemId = ${itemId};
                    var builderParam = "${builderParam}";
                    var styles = ${JSON.stringify(styles.concat(builderUrl+"vendors/animate/animate.css",builderUrl+"/css/ja_pagebuilder.css"))};
                    var t4template = "${t4template}";
                    var loadgooglefont = '${JSON.stringify(loadgooglefont)}';
                    var customFont = '${JSON.stringify(customFont)}';
                    var xtdbuttons = ${JSON.stringify(buttons)};
                    var ajax_url = "${ajax_url}";
                    var editor_type = "${editor_type}";
                    var loadIcons = ${loadIcons};
                    var buildAjaxUrl = ${buildAjaxUrl};
                    var jpb_devmode = "${jpb_devmode}";
                </script>

                <script src="${builderUrl}vendors/jquery/jquery.min.js?t=${t}"></script>
                <script src="${builderUrl}vendors/toastr/toastr.min.js?t=${t}"></script>
                <script src="${builderUrl}js/googlefonts.js?t=${t}"></script>
                <script src="${builderUrl}vendors/ckeditor/ckeditor.js?v=${t}"></script>
                <script src="${builderUrl}vendors/grapesjs/dist/grapes.min.js?v=${gjs}"></script>
                <script src="${builderUrl}js/plugin-t4.min.js?t=${t}"></script>

            </head>

            <body class="jub-editor">

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
            if ($('.modal-backdrop').length) $('.modal-backdrop').remove();
            $('<div class="modal-backdrop fade in show"></div>').appendTo('body');
            if (!extendMedia) {
                var onChange = function(imgUrl) {
                    if (e.type == 't4:media-open') {
                        const urlUpdateEvent = new CustomEvent('t4:media-selected');
                        urlUpdateEvent.selectedUrl = imgUrl;
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
                    $layoutmediainput.on('change', function() { onChange(this.value) });
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
            $item.val(addT4InfoData(cleanHtml(html), itemId));
            $('#' + $item.data('name')).val(itemId);
        })

        $item.data('builderEditor', $editor);
    }

    var destroyEditor = function($item) {
        // $('.t4editor-editor').remove();
        $item.data('builderEditor').remove();
        $item.removeData('builderEditor');
        localStorage.setItem('editinline', false);
        localStorage.setItem('editpage', false);
        if ($('.modal-backdrop').length) $('.modal-backdrop').remove();
        $('body').css('overflow', $('body').data('cof'));
        $('body').removeClass('jpb-loading');
    }

    var syncBuilderContent = function($item) {
        $('body').trigger('joomla:syncContent', [$item]);
    }

    var syncTextContent = function($item) {
        console.log('Sync content from Text editor: ', $item);
        const $id = $('#' + $item.data('name')),
            $textarea = $item.data('textarea'),
            $preview = $item.data('preview'),
            itemId = $id.val(),
            html = cleanHtml($item.data('textEditor').getValue());
        $textarea.val(html);
        $item.val(addT4InfoData(html, itemId));
        syncTextareaToPreview(itemId, $textarea, $preview);
    }

    /**
     * EDITOR Helper
     * Sync content to Preview, Joomla field before save
     */
    var beforeSave = function($item) {
        const $id = $('#' + $item.data('name')),
            $textarea = $item.data('textarea'),
            $preview = $item.data('preview'),
            itemId = $id.val();
        // update $item value
        $item.val(addT4InfoData($textarea.val(), $id.val()));
        return syncTextareaToPreview(itemId, $textarea, $preview);
    }

    var ajaxUrl = function(action) {
        return (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=' + action + '&id=' + itemId
    }

    var syncTextareaToPreview = function(itemId, $textarea, $preview, updatePreview) {
        if (!itemId) return false;

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
    }

    var cleanData = function(html) {
        // backword compatible
        html = html.replace(/<jpb:Item(Start|End)[^>]+>/gi, '').trim();
        html = html.replace(/<!--\/?jpb:item[^>]+>/gi, '').trim();
        html = html.replace(/<\/?jpb(?=\s|>)[^>]*>/gi, '').trim();

        // clean t4 meta
        html = html.replace(/<meta name="jpb"[^>]*>/gi, '').trim();

        return html;
    }

    // trim unused space
    var cleanHtml = function(html) {
        return html.replace(/\s*\>\s+\n?/g, '>').replace(/\n?\s+\</g, '<');
    }

    var addT4InfoData = function(html, itemId) {
        html = cleanHtml(cleanData(html));
        if (!itemId) return html;
        //const start = `<t4 iid="${itemId}">`;
        //const end = `</t4>`;
        const start = `<meta name="jpb" content="${itemId}:start">`;
        const startFull = `<meta name="jpb" content="${itemId}:start-full">`;
        const end = `<meta name="jpb" content="${itemId}:end">`;
        const endIntro = `<meta name="jpb" content="${itemId}:end-intro">`;
        // add info for readmore
        html = html.replace(/<hr\s+id="system-readmore"[^>]*>/, `${endIntro}<hr id="system-readmore">${startFull}`);

        html = start + html + end;

        return html;
    }

    var getBaseUrl = function() {
        var baseUrl = window.site_root_url;
        if (!baseUrl.match(/^http/)) baseUrl = location.protocol + '//' + location.host + baseUrl;
        return baseUrl;
    }
    var tempId = function() {
        var currLocation = window.location.search,
            parArr = currLocation.split("?")[1].split("&"),
            returnBool = '';
        for (var i = 0; i < parArr.length; i++) {
            parr = parArr[i].split("=");
            if (parr[0] == 'id') {
                returnBool = parr[1];
            }
        }
        return returnBool;
    }
    var loadStyle = function(url) {
        var head = document.head || document.getElementsByTagName('head')[0],
            link = document.createElement('link');
        link.type = 'text/css';
        link.rel = 'stylesheet';
        link.href = url;
        head.appendChild(link);
    }



    /**
     * SYSTEM Event to communicate between Editor & Core
     */
    $(document).ready(function() {
        siteUrl = window.site_root_url;
        builderUrl = window.builder_url;
        const $items = $('.jpb-item');
        // detect all t4editor items, then create preview, add edit action
        $items.each(function() {
            const $this = $(this);
            showPreview($this);
        })

        if ($items.length) {
            loadStyle(builderUrl + 'css/style.css');
        }


        $('body').on('jpb:exit', (e, itemId, html) => {
            // update content
            $editingItem.val(addT4InfoData(cleanHtml(html), itemId));
            $('#' + $editingItem.data('name')).val(itemId);
            // show preview then destroy the editor
            showPreview($editingItem, true);
            destroyEditor($editingItem);
            Joomla.submitbutton('page.apply');
        });
        $('body').on('jpb:update', (e, itemId, data) => {
            // update content
            $editingItem.val(addT4InfoData(cleanHtml(data.html), itemId));
            $('#' + $editingItem.data('name')).val(itemId);
            fetch(buildAjaxUrl('update', { itemId }), {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                return response.json();
            }).then(message => {
                if (true === message.data) {
                    editor.toastr.success('Page item saved successfully!');
                } else {
                    editor.toastr.error('Page item saving error!');
                }

            });
        });
        $('body').on('jpb:close', (e, itemId, html) => {
            if (e.target.className.indexOf('admin') < 0) {
                var pageEdit = localStorage.getItem('T4EDIT_PAGE');
                localStorage.setItem('T4EDIT_VISITED', false);
                window.location.href = pageEdit;
                return false;
            }

            // update content
            $editingItem.val(addT4InfoData(cleanHtml(html), itemId));
            $('#' + $editingItem.data('name')).val(itemId);
            if (localStorage.getItem('editinline') == "true") {
                localStorage.setItem('editinline', false);
                localStorage.setItem('editpage', false);
                window.location.href = baseUrl + 'administrator/index.php?option=com_t4pagebuilder&view=pages';
                return false;
            }
            // show preview then destroy the editor
            showPreview($editingItem, true);

            destroyEditor($editingItem);
        });
    });


    // Before Save, check and update editting content
    var updateEdittingContent = function() {
        $('.jpb-item').each(function() {
            var $item = $(this);
            if ($item.data('textEditor')) {
                // editting in Text mode
                syncTextContent($item);
            } else if ($item.data('builderEditor')) {
                syncBuilderContent($item);
            }
        })
    }

    // open media box
    let extendMedia = false;
    let extendfonts = false;
    // $(document).on('t4:media-open', function(e) {})
    // $(document).on('t4:fonts-open', function(e) {})

    // move media box to body and hide it
    $(document).ready(() => {
        $('#t4-media-joomla').appendTo('body').css({ position: 'relative', 'z-index': 10000000 }).find('.input-group').hide();
        // extend joomla submit button
        var _submitform = Joomla.submitform;
        Joomla.submitform = function(task, form, validate) {
            // call before submit
            updateEdittingContent();
            _submitform(task, form, validate);
        }
    })
})(jQuery)