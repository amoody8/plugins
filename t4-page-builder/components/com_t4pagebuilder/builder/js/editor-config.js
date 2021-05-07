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

export const deviceManager = {
    devices: [{
        name: 'Desktop',
        width: ''
    }, {
        name: 'Tablet',
        width: '768px',
        widthMedia: '991px'
    }, {
        name: 'Mobile landscape',
        width: '580px',
        widthMedia: '767px'
    }, {
        name: 'Mobile portrait',
        width: '320px',
        widthMedia: '480px'
    }]
}

// From panels
var crc = 'create-comp';
var mvc = 'move-comp';
var swv = 'sw-visibility';
var expt = 'export-template';
var osm = 'open-sm';
var otm = 'open-tm';
var ola = 'open-layers';
var obl = 'open-blocks';
var ogbl = 'open-group-blocks';
var ful = 'fullscreen';
var prv = 'preview';
var none = 'none';

export const panels = {
    defaults: [{
        id: 'commands',
        buttons: [{}]
    }, {
        id: 'options',
        buttons: [{
            active: true,
            id: swv,
            className: 'fal fa-square-o',
            command: swv,
            context: swv,
            attributes: { 
                title: 'View components'
            }
        }, {
            id: prv,
            className: 'fal fa-eye',
            command: prv,
            context: prv,
            attributes: { 
                title: 'Preview'
            }
        }, {
            id: ful,
            className: 'fal fa-arrows-alt',
            command: ful,
            context: ful,
            attributes: { 
                title: 'Fullscreen'
            }
        }, {
            id: expt,
            className: 'fal fa-code',
            command: expt,
            attributes: { 
                title: '' ,
                'data-tooltip-pos': 'bottom',
                'data-tooltip': 'View code'
            }
        }, {
            id: 'undo',
            className: 'fal fa-undo',
            command: e => e.runCommand('core:undo'),
            attributes: { 
                title: '',
                'data-tooltip-pos': 'bottom',
                'data-tooltip': 'Undo (CTRL/CMD + Z)' 
            }
        }, {
            id: 'redo',
            className: 'fal fa-redo',
            command: e => e.runCommand('core:redo'),
            attributes: { title: '', 'data-tooltip-pos': 'bottom', 'data-tooltip': 'Redo (CTRL/CMD + SHIFT + Z)' }
        }, {
            id: 'clear',
            className: 'fal fa-eraser',
            command: e => {
                confirm('Are you sure?') && e.runCommand('core:canvas-clear');
                e.editor.setSelected();
                let crumbEl = e.Panels.getPanel("crumbs-view").view.$el;
                crumbEl.find('#gjs-input-holder > div').remove();
                e.store();
            },
            attributes: {
                title: '',
                'data-tooltip-pos': 'bottom',
                'data-tooltip': 'Clear All Content'
            }
        }, {
            id: 'code-editor',
            className: 'fal fa-code',
            command: 'code-editor',
            attributes: {
                title: '',
                'data-tooltip-pos': 'bottom',
                'data-tooltip': 'Code Editor'
            }
        }, {
            id: 'btn-update',
            className: 'gjs-pn-btn gjs-pn-btn-txt gjs-pn-btn-primary t4b-pagetext gjs-pn-btn-raised gjs-pn-btn-publish',
            command: e => e.runCommand('jpb-update'),
            attributes: {
                title: ''
            },
            label: 'Save',
            active: false,
        }, {
            id: 'btn-close',
            className: 'gjs-pn-btn gjs-pn-btn-raised t4b-pagetext gjs-pn-btn-close',
            command: e => e.runCommand('jpb-close'),
            attributes: {
                title: '',
                'data-tooltip-pos': 'bottom',
                'data-tooltip': 'Close Page Update',
                class: 'fal fa-times'
            },
            label: '',
            active: false,
        }/*, {
            id: 'btn-exit',
            className: 'gjs-pn-btn gjs-pn-btn-txt gjs-pn-btn-primary gjs-pn-btn-raised t4b-content gjs-pn-btn-publish',
            command: e => e.runCommand('t4b-exit'),
            attributes: {
                title: '',
                'data-tooltip-pos': 'bottom',
                'data-tooltip': 'Page Update',
            },
            label: 'Publish',
            active: false,
        }*/]
    }, {
        id: 'views',
        buttons: [{
            id: osm,
            className: 'fal fa-paint-brush',
            label: '<span>Style Manager</span>',
            command: osm,
            active: true,
            helpkey: 'style-manager',
            attributes: { title: 'Open Style Manager' }
        }, {
            id: otm,
            className: 'fal fa-cog',
            command: otm,
            attributes: { title: 'Settings' }
        }, {
            id: ola,
            className: 'fal fa-bars',
            label: '<span>Layer Manager</span>',
            command: ola,
            helpkey: 'layer-manager',
            attributes: { title: 'Open Layer Manager' }
        }, {
            id: 'e-sep',
            className: 'gjs-separator',
            label: '<span>Library</span>',
            command: none
        }, {
            id: 'open-Element',
            label: '<span>Elements</span>',
            className: 'fal fa-th-large',
            command: ogbl,
            group: 'Element',
            helpkey: 'Element',
            attributes: { title: 'Open Elements Library', 'data-tooltip': 'Elements' }
        }, {
            id: 'open-Block',
            label: '<span>Blocks</span>',
            className: 'fal fa-indent',
            command: ogbl,
            group: 'Block',
            attributes: { title: 'Open Blocks Library','data-tooltip': 'Blocks' }
        }, {
            id: 'open-Pages',
            label: '<span>Pages</span>',
            className: 'fal fa-file-alt t4b-page-libs',
            command: 'open-pagelibs',
            helpkey: 'Page',
            attributes: { title: 'Open Pages Library', 'data-tooltip': 'Pages' }
        }, {
            id: 'open-UserBlock',
            label: '<span>Users Blocks</span>',
            className: 'fal fa-user-circle',
            command: ogbl,
            group: 'UserBlock',
            helpkey: 'UserBlock',
            attributes: { title: 'Open User Blocks', 'data-tooltip': 'Users Blocks' }
        }, {
            id: 'e-sep-2',
            className: 'gjs-separator',
            label: '<span>Advanced</span>',
            command: none
        }]
    }]
}

/* Custom style manager */
export const customStyleManager = [{
    name: 'General',
    buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
    properties: [{
            name: 'Alignment',
            property: 'float',
            type: 'radio',
            defaults: 'none',
            list: [
                { value: 'none', className: 'fa fa-times' },
                { value: 'left', className: 'fa fa-align-left' },
                { value: 'right', className: 'fa fa-align-right' }
            ],
        },
        { property: 'position', type: 'select' }
    ],
    }, {
    name: 'Dimension',
    open: false,
    buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
    properties: [{
        id: 'flex-width',
        type: 'integer',
        name: 'Width',
        units: ['px', '%'],
        property: 'flex-basis',
        toRequire: 1,
    }, {
        property: 'margin',
        properties: [
            { name: 'Top', property: 'margin-top' },
            { name: 'Right', property: 'margin-right' },
            { name: 'Bottom', property: 'margin-bottom' },
            { name: 'Left', property: 'margin-left' }
        ],
    }, {
        property: 'padding',
        properties: [
            { name: 'Top', property: 'padding-top' },
            { name: 'Right', property: 'padding-right' },
            { name: 'Bottom', property: 'padding-bottom' },
            { name: 'Left', property: 'padding-left' }
        ],
    }],
    }, {
    name: 'Typography',
    open: false,
    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
    properties: [
        { name: 'Font', property: 'font-family' },
        { name: 'Weight', property: 'font-weight' },
        { name: 'Font color', property: 'color' }, {
            property: 'text-align',
            type: 'radio',
            defaults: 'left',
            list: [
                { value: 'left', name: 'Left', className: 'fa fa-align-left' },
                { value: 'center', name: 'Center', className: 'fa fa-align-center' },
                { value: 'right', name: 'Right', className: 'fa fa-align-right' },
                { value: 'justify', name: 'Justify', className: 'fa fa-align-justify' }
            ],
        }, {
            property: 'text-decoration',
            type: 'radio',
            defaults: 'none',
            list: [
                { value: 'none', name: 'None', className: 'fa fa-times' },
                { value: 'underline', name: 'underline', className: 'fa fa-underline' },
                { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough' }
            ],
        }, {
            property: 'text-shadow',
            properties: [
                { name: 'X position', property: 'text-shadow-h' },
                { name: 'Y position', property: 'text-shadow-v' },
                { name: 'Blur', property: 'text-shadow-blur' },
                { name: 'Color', property: 'text-shadow-color' }
            ],
        }
    ],
    }, {
    name: 'Decorations',
    open: false,
    buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
    properties: [{
        type: 'slider',
        property: 'opacity',
        defaults: 1,
        step: 0.01,
        max: 1,
        min: 0,
    }, {
        property: 'border-radius',
        properties: [
            { name: 'Top', property: 'border-top-left-radius' },
            { name: 'Right', property: 'border-top-right-radius' },
            { name: 'Bottom', property: 'border-bottom-left-radius' },
            { name: 'Left', property: 'border-bottom-right-radius' }
        ],
    }, {
        property: 'box-shadow',
        properties: [
            { name: 'X position', property: 'box-shadow-h' },
            { name: 'Y position', property: 'box-shadow-v' },
            { name: 'Blur', property: 'box-shadow-blur' },
            { name: 'Spread', property: 'box-shadow-spread' },
            { name: 'Color', property: 'box-shadow-color' },
            { name: 'Shadow type', property: 'box-shadow-type' }
        ],
    }, {
        property: 'background',
        properties: [
            { name: 'Image', property: 'background-image' },
            { name: 'Repeat', property: 'background-repeat' },
            { name: 'Position', property: 'background-position' },
            { name: 'Attachment', property: 'background-attachment' },
            { name: 'Size', property: 'background-size' }
        ],
    },{
        property: 'background-image', 
        type: 'gradient',
        name:"Gradient",
        inputDirection: 1,
        inputType: 1,
        colorPicker: 'default',
        grapickOpts: {min: 1,max: 99},
    }, ],
    }, {
    name: 'Extra',
    open: false,
    buildProps: ['transition', 'perspective', 'transform'],
    properties: [{
        property: 'transition',
        properties: [
            { name: 'Property', property: 'transition-property' },
            { name: 'Duration', property: 'transition-duration' },
            { name: 'Easing', property: 'transition-timing-function' }
        ],
    }, {
        property: 'transform',
        properties: [
            { name: 'Rotate X', property: 'transform-rotate-x' },
            { name: 'Rotate Y', property: 'transform-rotate-y' },
            { name: 'Rotate Z', property: 'transform-rotate-z' },
            { name: 'Scale X', property: 'transform-scale-x' },
            { name: 'Scale Y', property: 'transform-scale-y' },
            { name: 'Scale Z', property: 'transform-scale-z' }
        ],
    }]
    }, {
    name: 'Flex',
    open: false,
    properties: [{
        name: 'Flex Container',
        property: 'display',
        type: 'select',
        defaults: 'block',
        list: [
            { value: 'block', name: 'Disable' },
            { value: 'flex', name: 'Enable' }
        ],
    }, {
        name: 'Flex Parent',
        property: 'label-parent-flex',
        type: 'integer',
    }, {
        name: 'Direction',
        property: 'flex-direction',
        type: 'radio',
        defaults: 'row',
        list: [{
            value: 'row',
            name: 'Row',
            className: 'icons-flex icon-dir-row',
            title: 'Row',
        }, {
            value: 'row-reverse',
            name: 'Row reverse',
            className: 'icons-flex icon-dir-row-rev',
            title: 'Row reverse',
        }, {
            value: 'column',
            name: 'Column',
            title: 'Column',
            className: 'icons-flex icon-dir-col',
        }, {
            value: 'column-reverse',
            name: 'Column reverse',
            title: 'Column reverse',
            className: 'icons-flex icon-dir-col-rev',
        }],
    }, {
        name: 'Justify',
        property: 'justify-content',
        type: 'radio',
        defaults: 'flex-start',
        list: [{
            value: 'flex-start',
            className: 'icons-flex icon-just-start',
            title: 'Start',
        }, {
            value: 'flex-end',
            title: 'End',
            className: 'icons-flex icon-just-end',
        }, {
            value: 'space-between',
            title: 'Space between',
            className: 'icons-flex icon-just-sp-bet',
        }, {
            value: 'space-around',
            title: 'Space around',
            className: 'icons-flex icon-just-sp-ar',
        }, {
            value: 'center',
            title: 'Center',
            className: 'icons-flex icon-just-sp-cent',
        }],
    }, {
        name: 'Align',
        property: 'align-items',
        type: 'radio',
        defaults: 'center',
        list: [{
            value: 'flex-start',
            title: 'Start',
            className: 'icons-flex icon-al-start',
        }, {
            value: 'flex-end',
            title: 'End',
            className: 'icons-flex icon-al-end',
        }, {
            value: 'stretch',
            title: 'Stretch',
            className: 'icons-flex icon-al-str',
        }, {
            value: 'center',
            title: 'Center',
            className: 'icons-flex icon-al-center',
        }],
    }, {
        name: 'Flex Children',
        property: 'label-parent-flex',
        type: 'integer',
    }, {
        name: 'Order',
        property: 'order',
        type: 'integer',
        defaults: 0,
        min: 0
    }, {
        name: 'Flex',
        property: 'flex',
        type: 'composite',
        properties: [{
            name: 'Grow',
            property: 'flex-grow',
            type: 'integer',
            defaults: 0,
            min: 0
        }, {
            name: 'Shrink',
            property: 'flex-shrink',
            type: 'integer',
            defaults: 0,
            min: 0
        }, {
            name: 'Basis',
            property: 'flex-basis',
            type: 'integer',
            units: ['px', '%', ''],
            unit: '',
            defaults: 'auto',
        }],
    }, {
        name: 'Align',
        property: 'align-self',
        type: 'radio',
        defaults: 'auto',
        list: [{
            value: 'auto',
            name: 'Auto',
        }, {
            value: 'flex-start',
            title: 'Start',
            className: 'icons-flex icon-al-start',
        }, {
            value: 'flex-end',
            title: 'End',
            className: 'icons-flex icon-al-end',
        }, {
            value: 'stretch',
            title: 'Stretch',
            className: 'icons-flex icon-al-str',
        }, {
            value: 'center',
            title: 'Center',
            className: 'icons-flex icon-al-center',
        }],
    }]
}]