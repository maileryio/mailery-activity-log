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
use Mailery\Activity\Log\Controller;
use Mailery\Activity\Log\Entity\ActivityLog;
use Mailery\Activity\Log\Repository\ActivityLogRepository;
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
    private const PAGINATION_INDEX = 10;

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @param SearchForm $searchForm
     * @return Response
     */
    public function index(Request $request, ORMInterface $orm, SearchForm $searchForm): Response
    {
        $searchForm = $searchForm->withSearchByList(new SearchByList([
            new DefaultSearchBy(),
        ]));

        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);

        $dataReader = $this->getActivityLogRepository($orm)
            ->getDataReader()
            ->withSearch((new Search())->withSearchPhrase($searchForm->getSearchPhrase())->withSearchBy($searchForm->getSearchBy()))
            ->withSort((new Sort([]))->withOrderString('username'));

        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @return Response
     */
    public function view(Request $request, ORMInterface $orm): Response
    {
        $activityLogId = $request->getAttribute('id');
        if (empty($activityLogId) || ($activityLog = $this->getActivityLogRepository($orm)->findByPK($activityLogId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        return $this->render('view', compact('activityLog'));
    }

    /**
     * @param ORMInterface $orm
     * @return ActivityLogRepository
     */
    private function getActivityLogRepository(ORMInterface $orm): ActivityLogRepository
    {
        return $orm->getRepository(ActivityLog::class);
    }
}
