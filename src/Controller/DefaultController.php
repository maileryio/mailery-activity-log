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
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;

class DefaultController
{
    private const PAGINATION_INDEX = 20;

    /**
     * @var ViewRenderer
     */
    private ViewRenderer $viewRenderer;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var EventRepository
     */
    private EventRepository $eventRepo;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param EventRepository $eventRepo
     */
    public function __construct(ViewRenderer $viewRenderer, ResponseFactory $responseFactory, EventRepository $eventRepo)
    {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->eventRepo = $eventRepo;
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
            'module' => $queryParams['module'] ?? null,
            'objectId' => $queryParams['objectId'] ?? null,
            'objectClass' => $queryParams['objectClass'] ?? null,
        ]);

        $searchForm = $eventService->getSearchForm()
            ->withSearchBy($searchBy)
            ->withSearchPhrase($searchPhrase);

        $paginator = $eventService->getFullPaginator($searchForm->getSearchBy(), $scope)
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->viewRenderer->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        $eventId = $request->getAttribute('id');
        if (empty($eventId) || ($event = $this->eventRepo->findByPK($eventId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('view', compact('event'));
    }
}
