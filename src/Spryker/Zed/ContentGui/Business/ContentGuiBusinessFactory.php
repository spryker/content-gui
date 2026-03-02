<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Business;

use DOMDocument;
use Spryker\Zed\ContentGui\Business\Converter\CmsBlockGui\CmsBlockGuiGlossaryConverter;
use Spryker\Zed\ContentGui\Business\Converter\CmsBlockGui\CmsBlockGuiGlossaryConverterInterface;
use Spryker\Zed\ContentGui\Business\Converter\CmsGui\CmsGuiGlossaryConverter;
use Spryker\Zed\ContentGui\Business\Converter\CmsGui\CmsGuiGlossaryConverterInterface;
use Spryker\Zed\ContentGui\Business\Converter\HtmlToTwigExpressionsConverter;
use Spryker\Zed\ContentGui\Business\Converter\HtmlToTwigExpressionsConverterInterface;
use Spryker\Zed\ContentGui\Business\Converter\TwigExpressionsToHtmlConverter;
use Spryker\Zed\ContentGui\Business\Converter\TwigExpressionsToHtmlConverterInterface;
use Spryker\Zed\ContentGui\ContentGuiDependencyProvider;
use Spryker\Zed\ContentGui\Dependency\Facade\ContentGuiToContentFacadeInterface;
use Spryker\Zed\ContentGui\Dependency\Facade\ContentGuiToTranslatorFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Spryker\Zed\ContentGui\ContentGuiConfig getConfig()
 */
class ContentGuiBusinessFactory extends AbstractBusinessFactory
{
    public function createCmsGuiGlossaryConverter(): CmsGuiGlossaryConverterInterface
    {
        return new CmsGuiGlossaryConverter(
            $this->createHtmlToTwigExpressionConverter(),
            $this->createTwigExpressionToHtmlConverter(),
        );
    }

    public function createCmsBlockGuiGlossaryConverter(): CmsBlockGuiGlossaryConverterInterface
    {
        return new CmsBlockGuiGlossaryConverter(
            $this->createHtmlToTwigExpressionConverter(),
            $this->createTwigExpressionToHtmlConverter(),
        );
    }

    public function createHtmlToTwigExpressionConverter(): HtmlToTwigExpressionsConverterInterface
    {
        return new HtmlToTwigExpressionsConverter(
            $this->createDomDocument(),
            $this->getConfig(),
        );
    }

    public function createTwigExpressionToHtmlConverter(): TwigExpressionsToHtmlConverterInterface
    {
        return new TwigExpressionsToHtmlConverter(
            $this->getContentFacade(),
            $this->getConfig(),
            $this->getTranslatorFacade(),
            $this->getContentEditorPlugins(),
        );
    }

    public function createDomDocument(): DOMDocument
    {
        return new DOMDocument();
    }

    /**
     * @return array<\Spryker\Zed\ContentGuiExtension\Dependency\Plugin\ContentGuiEditorPluginInterface>
     */
    public function getContentEditorPlugins(): array
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::PLUGINS_CONTENT_EDITOR);
    }

    public function getContentFacade(): ContentGuiToContentFacadeInterface
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::FACADE_CONTENT);
    }

    public function getTranslatorFacade(): ContentGuiToTranslatorFacadeInterface
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::FACADE_TRANSLATOR);
    }
}
