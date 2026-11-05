import { readContentItemConfig } from './modules/rich-text/content-item-config';
import { createContentItemFeature } from './modules/rich-text/content-item.feature';

// Rendered by Presentation/Assets/index.twig right after the configuration it reads.
const config = readContentItemConfig(window);

if (config === null) {
    console.error(
        'window.editorConfiguration.contentGuiConfigData is missing or incomplete, so the rich-text editor cannot ' +
            'offer content items. Check Presentation/Assets/index.twig.',
    );
}

// Without the registry (a Gui version before RichTextEditorType) there are no rich-text fields to extend; a field
// that needs a missing registry is reported by the Gui field initialization.
if (config !== null) {
    window.SprykerRichTextEditor?.registerFeature('cms', createContentItemFeature(config));
}
