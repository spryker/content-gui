<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method \Spryker\Zed\ContentGui\Business\ContentGuiFacadeInterface getFacade()
 * @method \Spryker\Zed\ContentGui\Communication\ContentGuiCommunicationFactory getFactory()
 */
class ListContentController extends AbstractController
{
    public function indexAction(): array
    {
        return $this->viewResponse($this->executeIndexAction());
    }

    public function tableAction(): JsonResponse
    {
        $contentTable = $this->getFactory()->createContentTable();

        return $this->jsonResponse($contentTable->fetchData());
    }

    protected function executeIndexAction(): array
    {
        $contentTable = $this->getFactory()->createContentTable();

        return [
            'contents' => $contentTable->render(),
            'termKeys' => $this->getFactory()->createContentResolver()->getTermKeys(),
        ];
    }
}
