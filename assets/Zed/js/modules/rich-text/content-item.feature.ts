import type { Editor } from '@tiptap/core';
import type { FeatureContext, RichTextEditorFeature } from '@zed/gui/js/modules/libs/rich-text-editor/types';
import type { ContentItemConfig } from './content-item-config';
import { ICON_CONTENT_ITEM, ICON_EDIT_WIDGET, ICON_OPEN_CONTENT_ITEM, ICON_REMOVE_WIDGET } from './content-item-icons';
import { openContentItemModal } from './content-item-modal';
import type { ContentItemAttributes } from './content-item-template';
import { CONTENT_ITEM_NODE_NAME, createContentItemNode } from './content-item.node';

// The page-wide count the Summernote dialog checked before inserting (content-item-editor-dialog.js).
const PAGE_WIDGET_COUNT_SELECTOR = 'span[data-twig-expression*="{{ content_"]';
const MAX_WIDGET_NUMBER_PLACEHOLDER = '%maxWidgetNumber%';

const isWidgetLimitReached = (maxWidgetNumber: number): boolean =>
    document.querySelectorAll(PAGE_WIDGET_COUNT_SELECTOR).length > maxWidgetNumber;

const openModal = (
    editor: Editor,
    config: ContentItemConfig,
    context: FeatureContext,
    selection: { type: string; key?: string; template?: string },
    apply: (attributes: ContentItemAttributes) => void,
): void => {
    openContentItemModal({
        config,
        ...selection,
        closeLabel: context.label('dialogClose', 'Close'),
        onSelect: apply,
        onClose: () => editor.commands.focus(),
    });
};

const createNodeMenuItems = (config: ContentItemConfig, context: FeatureContext) => [
    {
        id: 'content-item-edit-widget',
        label: config.editWidgetTitle,
        iconShape: ICON_EDIT_WIDGET,
        kind: 'button' as const,
        run: (editor: Editor) => {
            const current = editor.getAttributes(CONTENT_ITEM_NODE_NAME) as ContentItemAttributes;

            openModal(
                editor,
                config,
                context,
                { type: current.type, key: current.key, template: current.template },
                (attributes) => editor.chain().focus().updateContentItem(attributes).run(),
            );
        },
    },
    {
        id: 'content-item-edit-content-item',
        label: config.editContentItemTitle,
        iconShape: ICON_OPEN_CONTENT_ITEM,
        kind: 'button' as const,
        run: (editor: Editor) => {
            const { id } = editor.getAttributes(CONTENT_ITEM_NODE_NAME) as ContentItemAttributes;
            const url = new URL(config.contentItemUrl, window.location.origin);

            if (url.origin !== window.location.origin) {
                console.error(`The content item URL "${config.contentItemUrl}" is not on this Back Office.`);
                context.showNotice(context.label('contentItemOpenError', 'The content item could not be opened.'));

                return;
            }

            url.searchParams.set('id-content', id);
            window.open(url.toString(), '_blank', 'noopener');
        },
    },
    {
        id: 'content-item-remove',
        label: context.label('contentItemRemove', 'Remove'),
        iconShape: ICON_REMOVE_WIDGET,
        kind: 'button' as const,
        run: (editor: Editor) => editor.chain().focus().removeContentItem().run(),
    },
];

/** The ContentGui "Content Item" button, widget node and widget menu of the CMS glossary editors. */
export const createContentItemFeature = (config: ContentItemConfig): RichTextEditorFeature => ({
    name: 'content-item',
    order: 10,
    extensions: (tiptap) => [createContentItemNode(tiptap, config.widgetTemplate)],
    toolbarItems: (context) =>
        config.types.length === 0
            ? []
            : [
                  {
                      id: 'content-item',
                      label: config.buttonTitle,
                      iconShape: ICON_CONTENT_ITEM,
                      kind: 'menu',
                      menuItems: config.types.map(({ name, type }) => ({ value: type, label: name })),
                      run: (editor, type) => {
                          if (!type) {
                              return;
                          }

                          if (isWidgetLimitReached(config.maxWidgetNumber)) {
                              context.showNotice(
                                  context
                                      .label(
                                          'contentItemLimit',
                                          'Limit exceeded, maximum number of widgets %maxWidgetNumber%',
                                      )
                                      .replace(MAX_WIDGET_NUMBER_PLACEHOLDER, String(config.maxWidgetNumber)),
                              );

                              return;
                          }

                          openModal(editor, config, context, { type }, (attributes) =>
                              editor.chain().focus().insertContentItem(attributes).run(),
                          );
                      },
                  },
              ],
    nodeMenus: (context) => [
        { nodeName: CONTENT_ITEM_NODE_NAME, label: config.dialogTitle, items: createNodeMenuItems(config, context) },
    ],
});
