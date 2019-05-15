/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var editorConfig = require('ZedGuiEditorConfiguration');
var editorButtons = require('./editorComponents/buttons');
var ContentItemDialog = require('./content-item-editor-dialog');
var ContentItemPopover = require('./content-item-editor-popover');

var ContentItemEditor = function(options) {
    var self = this;
    this.dropDownItems = [];
    this.buttonTitle = 'Insert Content';
    this.title = 'Content';
    this.insertButtonTitle = 'Insert';
    this.dialogContentUrl = '';
    this.popoverButtonsContent = {};
    this.editorContentWidgetTemplate = '';

    $.extend(this, options);

    this.initialization = function() {
        new ContentItemDialog(
            this.title,
            this.dialogContentUrl,
            this.editDialogContentUrl,
            this.insertButtonTitle,
            this.editorContentWidgetTemplate
        );
        new ContentItemPopover();
    };

    this.getEditorConfig = function (baseConfig = '') {
        baseConfig = editorConfig.getGlobalConfig(baseConfig);

        if (!baseConfig) {
            baseConfig = editorConfig.getConfig();
        }

        var contentGuiConfig = {
            toolbar: [
                ['insert', ['dropdownContentItem']]
            ],
            buttons: {
                dropdownContentItem: this.createDropdownButton(),
                editWidget: this.createEditWidgetButton(),
                editContentItem: this.createEditContentItemButton(),
                removeContentItem: this.createRemoveContentItemButton()
            },
            popover: {
                'editContentItem': ['editWidget', 'editContentItem', 'removeContentItem']
            },
            dialogsInBody: true
        };

        return editorConfig.mergeConfigs(baseConfig, contentGuiConfig);
    };

    this.createDropdownButton = function () {
        return editorButtons.ContentItemDropdownButton(
            this.buttonTitle,
            this.generateDropdownList(),
            this.showDialogHandler
        );
    };

    this.createEditWidgetButton = function () {
        return editorButtons.PopoverButton(
            this.popoverButtonsContent.editWidget,
            this.showDialogHandler
        );
    };

    this.createEditContentItemButton = function () {
        return editorButtons.PopoverButton(
            this.popoverButtonsContent.editContentItem,
            this.editContentItemHandler
        );
    };

    this.createRemoveContentItemButton = function () {
        return editorButtons.PopoverButton(
            this.popoverButtonsContent.removeContentItem,
            this.removeContentItemHandler
        );
    };

    this.showDialogHandler = function (context) {
        return context.createInvokeHandler('contentItemDialog.show');
    };

    this.editContentItemHandler = function () {
        return function(event) {
            var contentItemId = event.currentTarget.dataset.id;
            var originLink = window.location.origin;

            window.open(originLink + self.contentItemUrl + '?id-content=' + contentItemId, '_blank');
        }
    };

    this.removeContentItemHandler = function (context) {
        return function () {
            var $clickedNode = context.invoke('contentItemPopover.getClickedNode');
            var $clickedNodeRange = $.summernote.range.createFromNode($clickedNode);

            $clickedNodeRange.deleteContents();
            context.invoke('contentItemPopover.hidePopover');
            context.invoke('pasteHTML', ' ');
        }
    };

    this.generateDropdownList = function () {
        return this.dropDownItems.reduce(function(currentList, dropItem) {
            var dropItemTemplate = '<li role="listitem">' +
                '<a href="#" data-type="' + dropItem.type + '">' +
                dropItem.name +
                '</a>' + '</li>';

            return currentList + dropItemTemplate;
        }, '');
    };

    this.initialization();
};

module.exports = ContentItemEditor;
