<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Dependency\Facade;

use Generated\Shared\Transfer\ContentTransfer;

interface ContentGuiToContentFacadeInterface
{
    /**
     * @param int $id
     *
     * @return \Generated\Shared\Transfer\ContentTransfer
     */
    public function findContentById(int $id): ContentTransfer;

    /**
     * @param \Generated\Shared\Transfer\ContentTransfer $contentTransfer
     *
     * @return \Generated\Shared\Transfer\ContentTransfer
     */
    public function create(ContentTransfer $contentTransfer): ContentTransfer;

    /**
     * @param \Generated\Shared\Transfer\ContentTransfer $contentTransfer
     *
     * @return \Generated\Shared\Transfer\ContentTransfer
     */
    public function update(ContentTransfer $contentTransfer): ContentTransfer;
}