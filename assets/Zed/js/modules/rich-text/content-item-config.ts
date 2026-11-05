export interface ContentItemType {
    /** The translated name shown in the menu. */
    readonly name: string;
    readonly type: string;
}

/** `window.editorConfiguration.contentGuiConfigData`, rendered by Presentation/Assets/index.twig. */
export interface ContentItemConfig {
    readonly types: readonly ContentItemType[];
    readonly dialogContentUrl: string;
    readonly contentItemUrl: string;
    readonly buttonTitle: string;
    readonly dialogTitle: string;
    readonly insertButtonTitle: string;
    /** ContentGuiConfig::getEditorContentWidgetTemplate(), with %TYPE%, %KEY%, … placeholders. */
    readonly widgetTemplate: string;
    readonly maxWidgetNumber: number;
    readonly editWidgetTitle: string;
    readonly editContentItemTitle: string;
}

type RawRecord = Record<string, unknown>;

const isRecord = (value: unknown): value is RawRecord => typeof value === 'object' && value !== null;
const readString = (record: RawRecord, key: string, fallback = ''): string =>
    typeof record[key] === 'string' && record[key] !== '' ? (record[key] as string) : fallback;
const readTitle = (record: RawRecord, button: string, fallback: string): string => {
    const buttons = record.popoverButtons;

    return isRecord(buttons) && isRecord(buttons[button]) ? readString(buttons[button], 'title', fallback) : fallback;
};

const readTypes = (value: unknown): ContentItemType[] =>
    Array.isArray(value)
        ? value
              .filter(isRecord)
              .filter((item) => typeof item.type === 'string' && typeof item.name === 'string')
              .map((item) => ({ name: item.name as string, type: item.type as string }))
        : [];

/** Null when the page carries no ContentGui configuration or it lacks what the feature cannot work without. */
export const readContentItemConfig = (source: Window): ContentItemConfig | null => {
    const editorConfiguration = (source as unknown as { editorConfiguration?: unknown }).editorConfiguration;
    const data = isRecord(editorConfiguration) ? editorConfiguration.contentGuiConfigData : undefined;

    if (
        !isRecord(data) ||
        readString(data, 'editorContentWidgetTemplate') === '' ||
        readString(data, 'dialogContentUrl') === ''
    ) {
        return null;
    }

    const maxWidgetNumber = Number(data.maxWidgetNumber);

    return {
        types: readTypes(data.dropDownItems),
        dialogContentUrl: readString(data, 'dialogContentUrl'),
        contentItemUrl: readString(data, 'contentItemUrl'),
        buttonTitle: readString(data, 'buttonTitle', 'Content Item'),
        dialogTitle: readString(data, 'title', 'Insert a Content Item'),
        insertButtonTitle: readString(data, 'insertButtonTitle', 'Insert'),
        widgetTemplate: readString(data, 'editorContentWidgetTemplate'),
        maxWidgetNumber: Number.isFinite(maxWidgetNumber) ? maxWidgetNumber : Number.POSITIVE_INFINITY,
        editWidgetTitle: readTitle(data, 'editWidget', 'Edit Widget'),
        editContentItemTitle: readTitle(data, 'editContentItem', 'Edit Content Item'),
    };
};
