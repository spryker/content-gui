// Drawn in the 24 × 24 grid and stroke weight of the Gui editor's toolbar icons. The widget menu icons repeat the
// paths of the editor's link and image menus (Gui toolbar/menu-icons.ts), so the menus look alike; this module only
// imports types from Gui, so a redrawn Gui icon is copied here by hand.

import type { IconShape } from '@zed/gui/js/modules/libs/rich-text-editor/toolbar/icons';

export const ICON_CONTENT_ITEM: IconShape = {
    stroke: ['M12 3l8 4.5v9L12 21l-8-4.5v-9z', 'M4 7.5l8 4.5 8-4.5', 'M12 12v9'],
};

export const ICON_EDIT_WIDGET: IconShape = {
    stroke: ['M4 20h4L19 9l-4-4L4 16v4z', 'M13.5 6.5l4 4'],
};

export const ICON_OPEN_CONTENT_ITEM: IconShape = {
    stroke: ['M14 4h6v6', 'M20 4l-9 9', 'M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5'],
};

export const ICON_REMOVE_WIDGET: IconShape = {
    stroke: ['M4 7h16', 'M10 11v6', 'M14 11v6', 'M6 7l1 13h10l1-13', 'M9 7V4h6v3'],
};
