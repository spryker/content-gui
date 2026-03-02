<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Dependency\Facade;

use Generated\Shared\Transfer\ContentTransfer;

interface ContentGuiToContentFacadeInterface
{
    public function findContentById(int $idContent): ?ContentTransfer;

    public function findContentByKey(string $contentKey): ?ContentTransfer;

    public function create(ContentTransfer $contentTransfer): ContentTransfer;

    public function update(ContentTransfer $contentTransfer): ContentTransfer;
}
