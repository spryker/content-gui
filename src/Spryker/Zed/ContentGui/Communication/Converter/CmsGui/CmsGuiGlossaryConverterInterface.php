<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Communication\Converter\CmsGui;

use Generated\Shared\Transfer\CmsGlossaryTransfer;

interface CmsGuiGlossaryConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\CmsGlossaryTransfer $cmsGlossaryTransfer
     *
     * @return \Generated\Shared\Transfer\CmsGlossaryTransfer
     */
    public function convertTwigFunctionToHtml(CmsGlossaryTransfer $cmsGlossaryTransfer): CmsGlossaryTransfer;

    /**
     * @param \Generated\Shared\Transfer\CmsGlossaryTransfer $cmsGlossaryTransfer
     *
     * @return \Generated\Shared\Transfer\CmsGlossaryTransfer
     */
    public function convertHtmlToTwigFunction(CmsGlossaryTransfer $cmsGlossaryTransfer): CmsGlossaryTransfer;
}