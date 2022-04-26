<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ContentGui\Business\ContentGuiFacadeInterface getFacade()
 * @method \Spryker\Zed\ContentGui\Communication\ContentGuiCommunicationFactory getFactory()
 */
class ListContentByTypeController extends AbstractController
{
    /**
     * @var string
     */
    public const PARAM_CONTENT_TYPE = 'type';

    /**
     * @var string
     */
    public const PARAM_CONTENT_KEY = 'contentKey';

    /**
     * @var string
     */
    public const PARAM_CONTENT_TEMPLATE = 'template';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request): array
    {
        /** @var string|null $contentType */
        $contentType = $request->query->get(static::PARAM_CONTENT_TYPE);
        /** @var string|null $contentKey */
        $contentKey = $request->query->get(static::PARAM_CONTENT_KEY);
        /** @var string|null $selectedTemplateIdentifier */
        $selectedTemplateIdentifier = $request->query->get(static::PARAM_CONTENT_TEMPLATE);
        $contentByTypeTable = $this->getFactory()->createContentByTypeTable($contentType, $contentKey);
        $contentTypeTemplates = $this->getFactory()->createContentEditorPluginsResolver()->getTemplatesByType($contentType);
        $twigFunctionTemplate = $this->getFactory()->createContentEditorPluginsResolver()->getTwigFunctionTemplateByType($contentType);

        $data = [
            'table' => $contentByTypeTable->render(),
            'templates' => $contentTypeTemplates,
            'twigFunctionTemplate' => $twigFunctionTemplate,
            'selectedTemplateIdentifier' => $selectedTemplateIdentifier,
            'contentType' => $contentType,
        ];

        return $this->viewResponse($data);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(Request $request): JsonResponse
    {
        /** @var string|null $contentType */
        $contentType = $request->query->get(static::PARAM_CONTENT_TYPE);
        /** @var string|null $contentKey */
        $contentKey = $request->query->get(static::PARAM_CONTENT_KEY);
        $contentByTypeTable = $this->getFactory()->createContentByTypeTable($contentType, $contentKey);

        return $this->jsonResponse($contentByTypeTable->fetchData());
    }
}
