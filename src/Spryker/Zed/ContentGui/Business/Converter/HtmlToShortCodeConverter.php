<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Business\Converter;

use DOMDocument;
use DOMXPath;

class HtmlToShortCodeConverter implements HtmlConverterInterface
{
    protected const ATTRIBUTE_DATA_SHORT_CODE = 'data-short-code';

    /**
     * @param string $html
     *
     * @return string
     */
    public function replaceWidget(string $html): string
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $replacements = $this->getDomReplacements($dom);

        if (!$replacements) {
            return $html;
        }

        foreach ($replacements as $replacement) {
            [$shortCode, $widget] = $replacement;
            $widget->parentNode->replaceChild($shortCode, $widget);
        }

        $html = $dom->saveHTML();

        return $html;
    }

    /**
     * @param \DOMDocument $dom
     *
     * @return array
     */
    protected function getDomReplacements(DOMDocument $dom): array
    {
        $replacements = [];
        $xpath = new DOMXPath($dom);
        $widgets = $xpath->query('//*[@' . static::ATTRIBUTE_DATA_SHORT_CODE . ']');

        foreach ($widgets as $widget) {
            $shortCode = $dom->createDocumentFragment();
            $shortCode->appendXML($widget->getAttribute(static::ATTRIBUTE_DATA_SHORT_CODE));
            $replacements[] = [$shortCode, $widget->parentNode];
        }

        return $replacements;
    }
}