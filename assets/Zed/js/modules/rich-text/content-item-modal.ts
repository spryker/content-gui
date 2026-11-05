import { Modal } from 'bootstrap';
import { destroyContentItemTables } from '../content-item-table-teardown';
import type { ContentItemConfig } from './content-item-config';
import { buildTwigExpression } from './content-item-template';
import type { ContentItemAttributes } from './content-item-template';

// Gui's table orchestrator initializes the tables of the loaded list on this DOM event (libs/table/table.js,
// TABLE_INIT_EVENT). It is named here, not imported: an import would bundle a second orchestrator copy
// (.claude/rules/table.md), and this bundle must also build against Gui versions without the export.
const TABLE_INIT_EVENT = 'TABLE-INIT-EVENT';

const LOAD_ERROR = 'The content items could not be loaded. Reload the page and try again.';
const contentCache = new Map<string, Promise<string>>();

export interface ContentItemModalOptions {
    readonly config: ContentItemConfig;
    readonly type: string;
    /** Preselects an item and template when an existing widget is edited. */
    readonly key?: string;
    readonly template?: string;
    readonly closeLabel: string;
    onSelect(attributes: ContentItemAttributes): void;
    onClose(): void;
}

type Selection = { readonly attributes: ContentItemAttributes } | { readonly errors: readonly string[] };

const loadContent = (url: string): Promise<string> => {
    let content = contentCache.get(url);

    if (content === undefined) {
        content = fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(
            async (response) => {
                if (!response.ok) {
                    throw new Error(`GET ${url} answered ${response.status}`);
                }

                return response.text();
            },
        );
        // A failed request is not cached, so the next opening tries again.
        content.catch(() => contentCache.delete(url));
        contentCache.set(url, content);
    }

    return content;
};

const readValue = (container: Element, name: string): string =>
    container.querySelector<HTMLInputElement>(`input[name="${name}"]`)?.value ?? '';

// The same fields the Summernote dialog read (content-item-editor-dialog.js, addContent), from the list the
// server renders (ListContentByType/index.twig, ContentByTypeTable).
export const readContentItemSelection = (container: Element): Selection => {
    const item = container.querySelector<HTMLInputElement>('table input[name="content-item"]:checked');
    const templateList = container.querySelector('.template-list');
    const template = templateList?.querySelector<HTMLInputElement>('input:checked') ?? null;
    const errors = [
        ...(item === null
            ? [container.querySelector('.content-errors .item')?.textContent?.trim() || 'Please select a content item']
            : []),
        ...(templateList !== null && template === null
            ? [container.querySelector('.content-errors .template')?.textContent?.trim() || 'Please select a template']
            : []),
    ];

    if (item === null || errors.length > 0) {
        return { errors };
    }

    const templateIdentifier = template?.value ?? '';

    return {
        attributes: {
            type: readValue(container, 'type'),
            key: item.value,
            displayType: readValue(container, 'displayType'),
            id: item.dataset.id ?? '',
            template: templateIdentifier,
            twigExpression: buildTwigExpression(
                readValue(container, 'twigFunctionTemplate'),
                item.value,
                templateIdentifier,
            ),
            name: item.dataset.contentItemName ?? '',
            templateDisplayName: template?.dataset.template ?? '',
        },
    };
};

const buildUrl = ({ config, type, key, template }: ContentItemModalOptions): string => {
    const parameters = new URLSearchParams({ type });

    if (key) {
        parameters.set('contentKey', key);
    }

    if (template) {
        parameters.set('template', template);
    }

    return `${config.dialogContentUrl}?${parameters.toString()}`;
};

const MODAL_TEMPLATE_ID = 'content-item-modal-template';

// The template is rendered by ContentGui's Assets/index.twig, next to this bundle's script.
const cloneModalTemplate = (): HTMLElement => {
    const template = document.getElementById(MODAL_TEMPLATE_ID);
    const element = template instanceof HTMLTemplateElement ? template.content.firstElementChild : null;

    if (!(element instanceof HTMLElement)) {
        throw new Error(`#${MODAL_TEMPLATE_ID} is missing: render ContentGui's Assets/index.twig in the page.`);
    }

    return document.importNode(element, true);
};

const findPart = <T extends HTMLElement = HTMLElement>(modal: HTMLElement, name: string): T => {
    const part = modal.querySelector<T>(`.js-content-item-modal__${name}`);

    if (part === null) {
        throw new Error(`The content item modal template has no .js-content-item-modal__${name} element.`);
    }

    return part;
};

/** Lets the user pick a content item (and template) of one type, like the Summernote content item dialog. */
export const openContentItemModal = (options: ContentItemModalOptions): void => {
    const element = cloneModalTemplate();
    const container = findPart(element, 'content');
    const loader = findPart(element, 'loader');
    const error = findPart(element, 'error');
    const insertButton = findPart(element, 'insert');
    const showError = (message: string): void => {
        error.textContent = message;
        error.hidden = false;
    };
    // Bootstrap's Modal caches .modal-dialog in its constructor, so it is created once the markup is complete.
    const getModal = (): Modal => Modal.getOrCreateInstance(element);

    findPart(element, 'title').textContent = options.config.dialogTitle;
    findPart(element, 'close').setAttribute('aria-label', options.closeLabel);
    insertButton.textContent = options.config.insertButtonTitle;
    insertButton.addEventListener('click', () => {
        const selection = readContentItemSelection(container);

        if ('errors' in selection) {
            showError(selection.errors.join(' '));

            return;
        }

        options.onSelect(selection.attributes);
        getModal().hide();
    });
    element.addEventListener(
        'hidden.bs.modal',
        () => {
            destroyContentItemTables(container);
            getModal().dispose();
            element.remove();
            options.onClose();
        },
        { once: true },
    );

    document.body.append(element);
    getModal().show();

    loadContent(buildUrl(options))
        .then((html) => {
            // Server-rendered markup of this Back Office (ListContentByType/index.twig), as the Summernote dialog inserted it.
            container.innerHTML = html;
            container.dispatchEvent(new CustomEvent(TABLE_INIT_EVENT, { bubbles: true }));
        })
        .catch((reason: unknown) => {
            console.error('Loading the content items for the rich-text editor failed.', reason);
            showError(LOAD_ERROR);
        })
        .finally(() => {
            loader.hidden = true;
        });
};
