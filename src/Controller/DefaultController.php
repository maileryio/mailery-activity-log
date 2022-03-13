<?php

declare(strict_types=1);

/**
 * Activity Log module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-activity-log
 * @package   Mailery\Activity\Log
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\Activity\Log\Controller;

use Mailery\Activity\Log\Repository\EventRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mailery\Activity\Log\Service\EventService;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Http\Status;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Yiisoft\Router\CurrentRoute;
use Mailery\Activity\Log\Provider\EntityGroupsProvider;

class DefaultController
{
    private const PAGINATION_INDEX = 20;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param EventRepository $eventRepo
     */
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ResponseFactory $responseFactory,
        private EventRepository $eventRepo,
        private EntityGroupsProvider $entityGroups
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views');
    }

    /**
     * @param Request $request
     * @param EventService $eventService
     * @return Response
     */
    public function index(Request $request, EventService $eventService): Response
    {
        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);
        $searchBy = $queryParams['searchBy'] ?? null;
        $searchPhrase = $queryParams['search'] ?? null;
        $scope = array_filter([
            'group' => $queryParams['group'] ?? null,
            'objectId' => $queryParams['objectId'] ?? null,
            'objectClass' => $queryParams['objectClass'] ?? null,
        ]);

        $searchForm = $eventService->getSearchForm()
            ->withSearchBy($searchBy)
            ->withSearchPhrase($searchPhrase);

        $paginator = $eventService->getFullPaginator($searchForm->getSearchBy(), $scope)
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        $entityGroups = $this->entityGroups;

        return $this->viewRenderer->render('index', compact('searchForm', 'paginator', 'entityGroups'));
    }

    /**
     * @param CurrentRoute $currentRoute
     * @return Response
     */
    public function view(CurrentRoute $currentRoute): Response
    {
        $eventId = (int) $currentRoute->getArgument('id');
        if (empty($eventId) || ($event = $this->eventRepo->findByPK($eventId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $entityGroups = $this->entityGroups;

        return $this->viewRenderer->render('view', compact('event', 'entityGroups'));
    }
}
