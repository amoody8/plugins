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

import {deviceManager, panels, customStyleManager} from './editor-config.js';

var getUrl = function(type) {
    switch (type) {
        case 'store':
            return ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=save&id=' + itemId;
        case 'load':
            return ajax_url + (ajax_url.match(/\?/) ? '&' : '?') + builderParam + '=loadItem&id=' + itemId;
    }
    return '';
}

var editor = grapesjs.init({
  container: "#editor-container",
  components: "",
  protectedCss: "",
  avoidDefaults: 1,
  allowScripts: 0,
  wrapperIsBody: 0,
  showOffsets: 1,
  fromElement: 1,
  height: "100%",
  avoidInlineStyle: jpb_devmode ? 0 : 1,
  showDevices: 1,
  forceClass: jpb_devmode ? 1 : 0,
  canvasCss: "",
  cssIcons: "",
  /* some joomla specific params */
  siteUrl,
  baseUrl,
  builderUrl,
  loadIcons,
  builderParam,

  canvas: {
    styles,
    scripts: [builderUrl + "vendors/jquery/jquery.min.js"],
  },
  clearStyles: 1,
  colorPicker: {
    showAlpha: true,
    showPalette: true,
    palette: [["black", "white", "blanchedalmond"]],
  },
  blockManager: {
    blocks: [],
  },
  settingsManager: {
    loadgooglefont: loadgooglefont,
    customfont: customFont,
  },
  storageManager: {
    id: "",
    type: "remote",
    autoload: true,
    stepsBeforeSave: 1,
    urlStore: buildAjaxUrl("save", { id: itemId }),
    urlLoad: buildAjaxUrl("loadItem", { id: itemId }),
    contentTypeJson: true,
    // For custom parameters/headers on requests
    params: {},
  },
  plugins: ["grapesjs-plugin-t4"],
  pluginsOpts: {
    "grapesjs-plugin-t4": {
      blockUrl: buildAjaxUrl("load", { id: itemId }),
      presetWebpage: {
        blocksBasicOpts: { flexGrid: 1, blocks: ["image", "video"] },
      },
      baseUrl,
      siteUrl,
      xtdbuttons,
      customStyleManager,
    },
  },
  //init item id used by some module
  itemId: itemId,

  deviceManager,
  templateManager: {
    templatestyle: t4template,
    emailTemplate: {
      html: `New contact form submission.
            <p>From: {name}</p>
            <p>Email:{email}</p>
            <p>Subject:{subject}</p>
            <p>Message:{message}</p>`,
    },
  },
  revisionManager: {
    revDel: 1,
  },
  pagelibs: {
    pagelibs: JSON.parse(localStorage.getItem("t4bpage")) || {},
  },
  helpManager: {
    helpurl: buildAjaxUrl("loadtips"),
  },

  // overwrite panel buttons
  panels,
  selectorManager: {
    componentFirst: true,
  },
  // disable computed status
  styleManager: {
    highlightComputed: false,
    clearProperties: true,
    showComputed: true,
  },

  ajaxUrl: {
    buildAjaxUrl: buildAjaxUrl,
  },
});

parent.window.editor = editor;


editor.Commands.add('jpb-update', {
    run: function(editor, sender) {
        // update item value
        var html = editor.getHtml();
        var data = {};
        data.css = editor.getCss();
        data.js = editor.getJs();
        data.blockscss = editor.StateManager.getBlocksCss();
        data.blocksjs = editor.StateManager.getBlocksJs();
        // replace jdoc:include to void tag
        html = html.replace(/<jdoc:include([^>]*)>\s*<\/jdoc:include>/gi, '<jdoc:include$1/>');
        data.html = html;
        // remove current start/end signal and update
        //html = html.replace(/<T4:Item(Start|End)[^>]+>/gi, '').trim();
        //html = '<T4:ItemStart for="' + itemId + '" />\n' + html + '<T4:ItemEnd for="' + itemId + '" />\n';
        // add signal
        //html += '\n<style>' + editor.getCss() + '</style>';

        parent.jQuery('body').trigger('jpb:update', [itemId, data]);
    }
});
editor.Commands.add('t4b-exit', {
    run: function(editor, sender) {
        // update item value
        var html = editor.getHtml();
        var data = {};
        data.css = editor.getCss();
        data.blockscss = editor.StateManager.getBlocksCss();
        // replace jdoc:include to void tag
        html = html.replace(/<jdoc:include([^>]*)>\s*<\/jdoc:include>/gi, '<jdoc:include$1/>');
        data.html = html;
        parent.jQuery('body').trigger('t4b:exit', [itemId, data]);
    }
});
editor.Commands.add('jpb-close', {
    run: function(editor, sender) {
        // update item value
        var html = editor.getHtml();
        // replace jdoc:include to void tag
        html = html.replace(/<jdoc:include([^>]*)>\s*<\/jdoc:include>/gi, '<jdoc:include$1/>');
        // remove current start/end signal and update
        //html = html.replace(/<T4:Item(Start|End)[^>]+>/gi, '').trim();
        //html = '<T4:ItemStart for="' + itemId + '" />\n' + html + '<T4:ItemEnd for="' + itemId + '" />\n';
        // add signal
        //html += '\n<style>' + editor.getCss() + '</style>';

        parent.jQuery('body').trigger('jpb:close', [itemId, html]);
    }
});

parent.jQuery('body').on('joomla:syncContent', function (e, $item) {
    var html = editor.getHtml();
    // replace jdoc:include to void tag
    html = html.replace(/<jdoc:include([^>]*)>\s*<\/jdoc:include>/gi, '<jdoc:include$1/>');
    $item.trigger('t4:syncContent', [itemId, html]);
})

// editor.Commands.add('t4-preview', {
//     run: function(editor, sender) {
//         if (itemId) {
//             var strWindowFeatures = "scrollbars=yes,status=no,menubar=no,toolbar=no,personalbar=no,location=no";
//             window.open(siteUrl + (siteUrl.match(/\?/) ? '&' : '?') + 't4doc=preview&t4id=' + itemId, 't4-preview', strWindowFeatures);
//         } else {
//             editor.Modal.open({
//                 title: 'Item not found',
//                 content: '<div>Please save the item first before can using live preview'
//             })
//         }
//     }
// })

editor.Commands.add('none', {
    run: function(editor, sender) {}
});

//show message warning 
parent.window.editor.toastr = toastr;
var origWarn = console.warn; 
 toastr.options = {
    closeButton: true,
    preventDuplicates: true,
    showDuration: 250,
    positionClass: 'toast-top-center',
    hideDuration: 150
  };
  console.warn = function (msg) {
    if (msg.indexOf('[undefined]') == -1) {
      toastr.warning(msg);
    }
    origWarn(msg);
  };
// store item, new id
editor.on('storage:end:store', (resultObject) => {
    if (resultObject.newId) {
        // update id
        itemId = resultObject.newId;

        // update url for storage
        const storage = editor.StorageManager.getCurrentStorage();
        storage.set('urlStore', getUrl('store'));
        storage.set('urlLoad', getUrl('load'));
    }
});

// add t4 wrap to editor content
editor.on('load', () => {
    // editor.getWrapper().view.$el.addClass('jpb');
    setTimeout(() => { 
        editor.$('body').removeClass('jpb-loading');
        window.parent.jQuery('body').removeClass('jpb-loading');
    }, 400);
});

editor.on('run:tlb-delete', () => {
    editor.editor.setSelected();
    let crumbEl = editor.Panels.getPanel("crumbs-view").view.$el;
    crumbEl.find('#gjs-input-holder > div').remove();
});

// Reload css
editor.Commands.add('t4-reload-css', {
    run: function(editor, sender) {
        var links = document.getElementsByTagName("link");
        for (var x in links) {
            var link = links[x];
            if (link.getAttribute && link.getAttribute("rel") == 'stylesheet') {
                link.href = link.href + "?id=" + new Date().getMilliseconds();
            }
        }
    }
});
if(parent.window.jpb_devmode){
    editor.Panels.addButton('options', {
        id: 'reloadcss',
        className: 'gjs-pn-btn gjs-pn-btn-txt gjs-pn-btn-reload',
        command: e => e.runCommand('t4-reload-css'),
        attributes: {
            title: ''
        },
        label: 'Reload CSS',
        active: false,
    })
}

