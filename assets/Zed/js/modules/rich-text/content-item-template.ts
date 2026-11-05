export interface ContentItemAttributes {
    readonly type: string;
    readonly key: string;
    readonly displayType: string;
    readonly id: string;
    readonly template: string;
    readonly twigExpression: string;
    readonly name: string;
    readonly templateDisplayName: string;
}

export const CONTENT_ITEM_ATTRIBUTE_NAMES: readonly (keyof ContentItemAttributes)[] = [
    'type',
    'key',
    'displayType',
    'id',
    'template',
    'twigExpression',
    'name',
    'templateDisplayName',
];

/**
 * The CSS form of what HtmlToTwigExpressionsConverter saves as Twig (ContentGuiConfig::DOM_PATH_WIDGET_QUERY), so a
 * project override of the widget template is still read as a content item as long as the server can convert it.
 */
export const CONTENT_ITEM_SELECTOR =
    'span[contenteditable="false"][data-key][data-twig-expression][data-template][data-type]';

// ContentGuiConfig::PARAMETER_* → attribute; the tokens are replaced whole, so %TEMPLATE% never eats into
// %TEMPLATE_DISPLAY_NAME%.
const PLACEHOLDERS: Readonly<Record<string, keyof ContentItemAttributes>> = {
    '%TYPE%': 'type',
    '%KEY%': 'key',
    '%DISPLAY_TYPE%': 'displayType',
    '%ID%': 'id',
    '%TEMPLATE%': 'template',
    '%TWIG_EXPRESSION%': 'twigExpression',
    '%NAME%': 'name',
    '%TEMPLATE_DISPLAY_NAME%': 'templateDisplayName',
};
const PLACEHOLDER_PATTERN = /%[A-Z_]+%/g;

// Label positions in the default template: type, key, name, template (ContentGuiConfig::getEditorContentWidgetTemplate()).
const NAME_LABEL_INDEX = 2;
const TEMPLATE_LABEL_INDEX = 3;

const escapeHtml = (value: string): string => value.replace(/[&<>"']/g, (character) => `&#${character.charCodeAt(0)};`);

/**
 * The widget exactly as the server renders it for stored content: the PHP template in a <p>
 * (TwigExpressionsToHtmlConverter), which is also the shape the save-side converter expects. Values are
 * HTML-escaped before the template is parsed.
 */
export const renderContentItem = (template: string, attributes: ContentItemAttributes): HTMLParagraphElement => {
    const templateElement = document.createElement('template');
    const paragraph = document.createElement('p');

    templateElement.innerHTML = template.replace(PLACEHOLDER_PATTERN, (placeholder) => {
        const attribute = PLACEHOLDERS[placeholder];

        return attribute === undefined ? placeholder : escapeHtml(attributes[attribute]);
    });
    paragraph.append(templateElement.content);

    return paragraph;
};

export const readContentItemAttributes = (widget: Element): ContentItemAttributes => {
    const data = (widget as HTMLElement).dataset;
    const labels = widget.querySelectorAll(':scope > span b');

    return {
        type: data.type ?? '',
        key: data.key ?? '',
        displayType: data.displayType ?? '',
        id: data.id ?? '',
        template: data.template ?? '',
        twigExpression: data.twigExpression ?? '',
        // Display only: the save-side converter replaces the whole widget with its Twig expression.
        name: labels[NAME_LABEL_INDEX]?.textContent ?? '',
        templateDisplayName: labels[TEMPLATE_LABEL_INDEX]?.textContent ?? '',
    };
};

/** The Twig call for a content item, from the plugin's function template, e.g. `{{ content_banner('%KEY%', '%TEMPLATE%') }}`. */
export const buildTwigExpression = (twigFunctionTemplate: string, key: string, templateIdentifier: string): string => {
    const values: Readonly<Record<string, string>> = { '%KEY%': key, '%TEMPLATE%': templateIdentifier };

    return twigFunctionTemplate.replace(/%\w+%/g, (placeholder) => values[placeholder] ?? placeholder);
};
