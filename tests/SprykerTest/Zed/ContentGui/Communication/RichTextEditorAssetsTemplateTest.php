<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ContentGui\Communication;

use Codeception\Test\Unit;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ContentGui
 * @group Communication
 * @group RichTextEditorAssetsTemplateTest
 * Add your own group annotations below this line
 */
class RichTextEditorAssetsTemplateTest extends Unit
{
    protected const string MODULE_ROOT = __DIR__ . '/../../../../..';

    protected const string RICH_TEXT_FEATURE_SOURCE = self::MODULE_ROOT . '/assets/Zed/js/modules/rich-text/content-item.feature.ts';

    protected const string FEATURE_LABELS_PATTERN = '#<script type="application/json" data-spryker-rich-text-editor-labels>(.*?)</script>#s';

    /**
     * Keys of the Gui editor's own labels, which Gui translates.
     *
     * @var array<string>
     */
    protected const array GUI_LABEL_KEYS = ['dialogClose'];

    protected const string ASSETS_TEMPLATE = '@ContentGui/Assets/index.twig';

    protected const string RICH_TEXT_FEATURE_SCRIPT = 'js/spryker-zed-content-gui-rich-text.js';

    public function testFeatureLabelsHaveTheKeysAndEnglishTextsOfTheFeature(): void
    {
        // Act
        $labels = $this->decodeFeatureLabels($this->renderAssets([]));

        // Assert
        $this->assertSame($this->readFeatureDefaults(), $labels);
    }

    /**
     * @dataProvider provideTranslationFiles
     */
    public function testEveryFeatureLabelHasATranslationRow(string $translationFile): void
    {
        // Arrange
        $translations = $this->readTranslations(static::MODULE_ROOT . '/data/translation/Zed/' . $translationFile);

        // Act
        $missingTexts = array_values(array_diff($this->readFeatureDefaults(), array_keys($translations)));

        // Assert
        $this->assertSame([], $missingTexts);
    }

    /**
     * @return array<string, array<string>>
     */
    protected function provideTranslationFiles(): array
    {
        return [
            'en_US' => ['en_US.csv'],
            'de_DE' => ['de_DE.csv'],
        ];
    }

    public function testRichTextFeatureScriptIsRendered(): void
    {
        // Act
        $renderedAssets = $this->renderAssets([]);

        // Assert
        $this->assertStringContainsString(static::RICH_TEXT_FEATURE_SCRIPT, $renderedAssets);
    }

    /**
     * @param array<string, mixed> $context
     */
    protected function renderAssets(array $context): string
    {
        $loader = new FilesystemLoader();
        $loader->addPath(__DIR__ . '/../../../../../src/Spryker/Zed/ContentGui/Presentation', 'ContentGui');

        // Strict, so a condition reading an undefined variable fails instead of evaluating to false.
        $twig = new Environment($loader, ['strict_variables' => true, 'autoescape' => 'html']);
        $twig->addFunction(new TwigFunction('assetsPath', static fn (string $path): string => $path));
        $twig->addFilter(new TwigFilter('trans', static fn (string $text): string => $text));

        return $twig->render(static::ASSETS_TEMPLATE, $context + [
            'editorContentTypes' => [],
            'editorContentWidgetTemplate' => '',
            'maxWidgetNumber' => 10,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeFeatureLabels(string $renderedAssets): array
    {
        $this->assertSame(1, preg_match(static::FEATURE_LABELS_PATTERN, $renderedAssets, $matches));

        return json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * The `context.label(key, englishText)` calls of the rich-text feature, without the Gui editor's own keys.
     *
     * @return array<string, string>
     */
    protected function readFeatureDefaults(): array
    {
        $featureSource = (string)file_get_contents(static::RICH_TEXT_FEATURE_SOURCE);
        preg_match_all("/label\(\s*'(\w+)',\s*'((?:[^'\\\\]|\\\\.)*)',?\s*\)/", $featureSource, $labelMatches, PREG_SET_ORDER);
        $this->assertNotEmpty($labelMatches);

        $defaults = [];

        foreach ($labelMatches as [, $key, $text]) {
            if (in_array($key, static::GUI_LABEL_KEYS, true)) {
                continue;
            }

            $defaults[$key] = stripslashes($text);
        }

        return $defaults;
    }

    /**
     * @return array<string, string>
     */
    protected function readTranslations(string $translationFile): array
    {
        $translations = [];

        foreach ((array)file($translationFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            [$source, $translation] = str_getcsv((string)$line, ',', '"', '\\');
            $translations[(string)$source] = (string)$translation;
        }

        return $translations;
    }
}
