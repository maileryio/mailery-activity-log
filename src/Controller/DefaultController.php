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

use Cycle\ORM\ORMInterface;
use Mailery\Common\Web\Controller;
use Mailery\Activity\Log\Entity\Event;
use Mailery\Activity\Log\Repository\EventRepository;
use Mailery\Activity\Log\Search\DefaultSearchBy;
use Mailery\Widget\Dataview\Paginator\OffsetPaginator;
use Mailery\Widget\Search\Data\Reader\Search;
use Mailery\Widget\Search\Form\SearchForm;
use Mailery\Widget\Search\Model\SearchByList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Reader\Sort;

class DefaultController extends Controller
{
    private const PAGINATION_INDEX = 20;

    /**
     * @param Request $request
     * @param SearchForm $searchForm
     * @return Response
     */
    public function index(Request $request, SearchForm $searchForm): Response
    {
        $searchForm = $searchForm->withSearchByList(new SearchByList([
            new DefaultSearchBy(),
        ]));

        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);
        $scope = array_filter([
            'module' => $queryParams['module'] ?? null,
            'objectId' => $queryParams['objectId'] ?? null,
            'objectClass' => $queryParams['objectClass'] ?? null,
        ]);

        $dataReader = $this->getEventRepository()
            ->getDataReader($scope)
            ->withSearch((new Search())->withSearchPhrase($searchForm->getSearchPhrase())->withSearchBy($searchForm->getSearchBy()))
            ->withSort((new Sort([]))->withOrder(['id' => 'DESC']));

        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        $eventId = $request->getAttribute('id');
        if (empty($eventId) || ($event = $this->getEventRepository()->findByPK($eventId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        return $this->render('view', compact('event'));
    }

    /**
     * @return EventRepository
     */
    private function getEventRepository(): EventRepository
    {
        return $this->getOrm()
            ->getRepository(Event::class)
            ->withLoadBrand();
    }
}
