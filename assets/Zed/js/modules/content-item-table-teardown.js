/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

/**
 * Teardown, not creation: the content item tables were created by the Gui table orchestrator, which was told
 * about them by TABLE_INIT_EVENT. It owns no way to let go of one again (its handles are keyed by the element,
 * and the caller is about to drop the elements), so the plugin is named directly here. Destroying is what keeps
 * reopening the dialog correct: the next open appends fresh elements, and a table left behind would be reported
 * as foreign. Replace this with a handle once TableHandle can tear a table down (.claude/rules/table.md).
 *
 * @param {Element} container
 *
 * @returns {void}
 */
function destroyContentItemTables(container) {
    $(container)
        .find('table')
        .each(function () {
            if ($.fn.dataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });
}

module.exports = {
    destroyContentItemTables: destroyContentItemTables,
};
