/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

const ContentItemDropdownButton = function (
    buttonTitle,
    dropdownList,
    dropdownCallback
) {
    return function (context) {
        let ui = $.summernote.ui;

        let button = ui.buttonGroup([
            ui.button({
                contents: buttonTitle + ' <i class="fa fa-caret-down" aria-hidden="true"></i>',
                data: {
                    toggle: 'dropdown'
                }
            }),
            ui.dropdown({
                contents: dropdownList,
                click: dropdownCallback(context)
            })
        ]);

        return button.render();
    }
};

module.exports = {
    ContentItemDropdownButton: ContentItemDropdownButton
};