<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Communication;

use Generated\Shared\Transfer\ContentTransfer;
use Orm\Zed\Content\Persistence\SpyContentQuery;
use Spryker\Zed\ContentGui\Communication\Form\ContentForm;
use Spryker\Zed\ContentGui\Communication\Form\DataProvider\ContentFormDataProvider;
use Spryker\Zed\ContentGui\Communication\Form\DataProvider\ContentFormDataProviderInterface;
use Spryker\Zed\ContentGui\Communication\Resolver\ContentEditorPluginsResolver;
use Spryker\Zed\ContentGui\Communication\Resolver\ContentEditorPluginsResolverInterface;
use Spryker\Zed\ContentGui\Communication\Resolver\ContentResolver;
use Spryker\Zed\ContentGui\Communication\Resolver\ContentResolverInterface;
use Spryker\Zed\ContentGui\Communication\Table\ContentByTypeTable;
use Spryker\Zed\ContentGui\Communication\Table\ContentTable;
use Spryker\Zed\ContentGui\Communication\Tabs\ContentTabs;
use Spryker\Zed\ContentGui\ContentGuiDependencyProvider;
use Spryker\Zed\ContentGui\Dependency\Facade\ContentGuiToContentFacadeInterface;
use Spryker\Zed\ContentGui\Dependency\Facade\ContentGuiToLocaleFacadeInterface;
use Spryker\Zed\ContentGui\Dependency\Service\ContentGuiToUtilEncodingInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Spryker\Zed\ContentGui\Business\ContentGuiFacadeInterface getFacade()
 * @method \Spryker\Zed\ContentGui\ContentGuiConfig getConfig()
 */
class ContentGuiCommunicationFactory extends AbstractCommunicationFactory
{
    public function createContentTable(): ContentTable
    {
        return new ContentTable(
            $this->getPropelContentQuery(),
            $this->getContentPlugins(),
        );
    }

    public function createContentTabs(): ContentTabs
    {
        return new ContentTabs($this->getLocaleFacade());
    }

    public function createContentFormDataProvider(): ContentFormDataProviderInterface
    {
        return new ContentFormDataProvider(
            $this->createContentResolver(),
            $this->getContentFacade(),
            $this->getLocaleFacade(),
        );
    }

    public function createContentResolver(): ContentResolverInterface
    {
        return new ContentResolver($this->getContentPlugins());
    }

    public function createContentByTypeTable(string $contentType, ?string $contentKey = null): ContentByTypeTable
    {
        return new ContentByTypeTable(
            $contentType,
            $this->getPropelContentQuery(),
            $contentKey,
        );
    }

    public function createContentEditorPluginsResolver(): ContentEditorPluginsResolverInterface
    {
        return new ContentEditorPluginsResolver($this->getContentEditorPlugins());
    }

    /**
     * @param \Generated\Shared\Transfer\ContentTransfer|null $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getContentForm(?ContentTransfer $data = null, array $options = []): FormInterface
    {
        return $this->getFormFactory()->create(ContentForm::class, $data, $options);
    }

    public function getPropelContentQuery(): SpyContentQuery
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::PROPEL_QUERY_CONTENT);
    }

    public function getLocaleFacade(): ContentGuiToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::FACADE_LOCALE);
    }

    public function getContentFacade(): ContentGuiToContentFacadeInterface
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::FACADE_CONTENT);
    }

    /**
     * @return array<\Spryker\Zed\ContentGuiExtension\Dependency\Plugin\ContentPluginInterface>
     */
    public function getContentPlugins(): array
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::PLUGINS_CONTENT_ITEM);
    }

    public function getUtilEncoding(): ContentGuiToUtilEncodingInterface
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return array<\Spryker\Zed\ContentGuiExtension\Dependency\Plugin\ContentGuiEditorPluginInterface>
     */
    public function getContentEditorPlugins(): array
    {
        return $this->getProvidedDependency(ContentGuiDependencyProvider::PLUGINS_CONTENT_EDITOR);
    }
}
