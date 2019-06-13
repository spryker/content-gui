<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Business\Converter\CmsBlockGui;

use Generated\Shared\Transfer\CmsBlockGlossaryTransfer;
use Spryker\Zed\ContentGui\Business\Converter\HtmlConverterInterface;
use Spryker\Zed\ContentGui\Business\Converter\TwigExpressionConverterInterface;

class CmsBlockGuiGlossaryConverter implements CmsBlockGuiGlossaryConverterInterface
{
    /**
     * @var \Spryker\Zed\ContentGui\Business\Converter\HtmlConverterInterface
     */
    protected $htmlToTwigExpressionConverter;

    /**
     * @var \Spryker\Zed\ContentGui\Business\Converter\TwigExpressionConverterInterface
     */
    protected $twigExpressionToHtmlConverter;

    /**
     * @param \Spryker\Zed\ContentGui\Business\Converter\HtmlConverterInterface $htmlToTwigExpressionConverter
     * @param \Spryker\Zed\ContentGui\Business\Converter\TwigExpressionConverterInterface $twigExpressionToHtmlConverter
     */
    public function __construct(
        HtmlConverterInterface $htmlToTwigExpressionConverter,
        TwigExpressionConverterInterface $twigExpressionToHtmlConverter
    ) {
        $this->htmlToTwigExpressionConverter = $htmlToTwigExpressionConverter;
        $this->twigExpressionToHtmlConverter = $twigExpressionToHtmlConverter;
    }

    /**
     * @param \Generated\Shared\Transfer\CmsBlockGlossaryTransfer $cmsBlockGlossaryTransfer
     *
     * @return \Generated\Shared\Transfer\CmsBlockGlossaryTransfer
     */
    public function convertTwigExpressionToHtml(CmsBlockGlossaryTransfer $cmsBlockGlossaryTransfer): CmsBlockGlossaryTransfer
    {
        $cmsBlockGlossaryTransfer->requireGlossaryPlaceholders();
        $cmsBlockGlossaryPlaceholderTransfers = $cmsBlockGlossaryTransfer->getGlossaryPlaceholders();

        foreach ($cmsBlockGlossaryPlaceholderTransfers as $cmsBlockGlossaryPlaceholderTransfer) {
            $cmsBlockGlossaryPlaceholderTransfer->requireTranslations();
            $cmsBlockGlossaryPlaceholderTranslationTransfers = $cmsBlockGlossaryPlaceholderTransfer->getTranslations();

            foreach ($cmsBlockGlossaryPlaceholderTranslationTransfers as $cmsBlockGlossaryPlaceholderTranslationTransfer) {
                $cmsBlockGlossaryPlaceholderTranslation = $cmsBlockGlossaryPlaceholderTranslationTransfer->getTranslation();

                if (!$cmsBlockGlossaryPlaceholderTranslation) {
                    continue;
                }

                $cmsBlockGlossaryPlaceholderTranslation = $this->twigExpressionToHtmlConverter->convertTwigExpressionToHtml($cmsBlockGlossaryPlaceholderTranslation);
                $cmsBlockGlossaryPlaceholderTranslationTransfer->setTranslation($cmsBlockGlossaryPlaceholderTranslation);
            }

            $cmsBlockGlossaryPlaceholderTransfer->setTranslations($cmsBlockGlossaryPlaceholderTranslationTransfers);
        }

        return $cmsBlockGlossaryTransfer->setGlossaryPlaceholders($cmsBlockGlossaryPlaceholderTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\CmsBlockGlossaryTransfer $cmsBlockGlossaryTransfer
     *
     * @return \Generated\Shared\Transfer\CmsBlockGlossaryTransfer
     */
    public function convertHtmlToTwigExpression(CmsBlockGlossaryTransfer $cmsBlockGlossaryTransfer): CmsBlockGlossaryTransfer
    {
        $cmsBlockGlossaryTransfer->requireGlossaryPlaceholders();
        $cmsBlockGlossaryPlaceholderTransfers = $cmsBlockGlossaryTransfer->getGlossaryPlaceholders();

        foreach ($cmsBlockGlossaryPlaceholderTransfers as $cmsBlockGlossaryPlaceholderTransfer) {
            $cmsBlockGlossaryPlaceholderTransfer->requireTranslations();
            $cmsBlockGlossaryPlaceholderTranslationTransfers = $cmsBlockGlossaryPlaceholderTransfer->getTranslations();

            foreach ($cmsBlockGlossaryPlaceholderTranslationTransfers as $cmsBlockGlossaryPlaceholderTranslationTransfer) {
                $cmsBlockGlossaryPlaceholderTranslation = $cmsBlockGlossaryPlaceholderTranslationTransfer->getTranslation();

                if (!$cmsBlockGlossaryPlaceholderTranslation) {
                    continue;
                }

                $cmsBlockGlossaryPlaceholderTranslation = $this->htmlToTwigExpressionConverter->convertHtmlToTwigExpression($cmsBlockGlossaryPlaceholderTranslation);
                $cmsBlockGlossaryPlaceholderTranslationTransfer->setTranslation($cmsBlockGlossaryPlaceholderTranslation);
            }

            $cmsBlockGlossaryPlaceholderTransfer->setTranslations($cmsBlockGlossaryPlaceholderTranslationTransfers);
        }

        return $cmsBlockGlossaryTransfer->setGlossaryPlaceholders($cmsBlockGlossaryPlaceholderTransfers);
    }
}
