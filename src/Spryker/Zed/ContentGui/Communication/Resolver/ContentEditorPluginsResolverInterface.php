<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Communication\Resolver;

interface ContentEditorPluginsResolverInterface
{
    /**
     * @param string $contentType
     *
     * @return array<\Generated\Shared\Transfer\ContentWidgetTemplateTransfer>
     */
    public function getTemplatesByType(string $contentType): array;

    /**
     * @param string $contentType
     *
     * @return string
     */
    public function getTwigFunctionTemplateByType(string $contentType): string;

    /**
     * @return array<string>
     */
    public function getContentTypes(): array;
}
