import type { CommandProps } from '@tiptap/core';
import type { TiptapApi } from '@zed/gui/js/modules/libs/rich-text-editor/types';
import {
    CONTENT_ITEM_ATTRIBUTE_NAMES,
    CONTENT_ITEM_SELECTOR,
    readContentItemAttributes,
    renderContentItem,
} from './content-item-template';
import type { ContentItemAttributes } from './content-item-template';

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        contentItem: {
            /** Replaces an empty paragraph with the item, or inserts it after the current top-level block. */
            insertContentItem: (attributes: ContentItemAttributes) => ReturnType;
            /** Changes the selected content item. */
            updateContentItem: (attributes: ContentItemAttributes) => ReturnType;
            /** Removes the selected content item. */
            removeContentItem: () => ReturnType;
        };
    }
}

export const CONTENT_ITEM_NODE_NAME = 'contentItem';

// Above the paragraph rule (default 50), so a widget-only paragraph is read as the item, not as a paragraph.
const PRIORITY = 60;

/** The paragraph TwigExpressionsToHtmlConverter wraps a widget in: the widget alone (a trailing <br> allowed). */
const readWrappedWidget = (paragraph: HTMLElement): ContentItemAttributes | false => {
    const children = [...paragraph.children].filter((child) => child.tagName !== 'BR');
    const [widget] = children;

    if (children.length !== 1 || widget === undefined || !widget.matches(CONTENT_ITEM_SELECTOR)) {
        return false;
    }

    return paragraph.textContent?.trim() === widget.textContent?.trim() ? readContentItemAttributes(widget) : false;
};

/**
 * A ContentGui content item as one block per <p>: the save-side converter replaces the widget's whole
 * paragraph with the Twig expression, so any text sharing that paragraph would be lost.
 */
export const createContentItemNode = ({ Node, NodeSelection }: TiptapApi, widgetTemplate: string) => {
    const findSelectedItem = (state: CommandProps['state']) =>
        state.selection instanceof NodeSelection && state.selection.node.type.name === CONTENT_ITEM_NODE_NAME
            ? state.selection
            : null;

    return Node.create({
        name: CONTENT_ITEM_NODE_NAME,
        group: 'block',
        atom: true,
        selectable: true,
        draggable: false,

        addAttributes() {
            // Parsed as a whole by the rules below and rendered by the widget template.
            return Object.fromEntries(
                CONTENT_ITEM_ATTRIBUTE_NAMES.map((name) => [
                    name,
                    { default: '', parseHTML: () => null, rendered: false },
                ]),
            );
        },

        parseHTML() {
            return [
                { tag: 'p', priority: PRIORITY, getAttrs: (element: HTMLElement) => readWrappedWidget(element) },
                // A widget sharing a paragraph with text is lifted into its own block.
                {
                    tag: CONTENT_ITEM_SELECTOR,
                    priority: PRIORITY,
                    getAttrs: (element: HTMLElement) => readContentItemAttributes(element),
                },
            ];
        },

        renderHTML({ node }) {
            return renderContentItem(widgetTemplate, node.attrs as ContentItemAttributes);
        },

        addCommands() {
            return {
                insertContentItem:
                    (attributes: ContentItemAttributes) =>
                    ({ state, commands }: CommandProps): boolean => {
                        const { $from } = state.selection;
                        const content = { type: CONTENT_ITEM_NODE_NAME, attrs: attributes };
                        const isInEmptyParagraph =
                            $from.parent.type.name === 'paragraph' && $from.parent.content.size === 0;

                        if (isInEmptyParagraph || $from.depth === 0) {
                            return commands.insertContent(content);
                        }

                        return commands.insertContentAt($from.after(1), content);
                    },
                updateContentItem:
                    (attributes: ContentItemAttributes) =>
                    ({ state, tr }: CommandProps): boolean => {
                        const selection = findSelectedItem(state);

                        if (selection === null) {
                            return false;
                        }

                        tr.setNodeMarkup(selection.from, undefined, { ...selection.node.attrs, ...attributes });

                        return true;
                    },
                removeContentItem:
                    () =>
                    ({ state, commands }: CommandProps): boolean =>
                        findSelectedItem(state) !== null && commands.deleteSelection(),
            };
        },
    });
};
