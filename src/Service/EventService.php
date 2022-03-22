<?php

namespace Mailery\Activity\Log\Service;

use Mailery\Widget\Search\Form\SearchForm;
use Mailery\Widget\Search\Model\SearchByList;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Mailery\Activity\Log\Repository\EventRepository;
use Mailery\Activity\Log\Search\DefaultSearchBy;
use Yiisoft\Data\Reader\Filter\FilterInterface;

class EventService
{
    /**
     * @param EventRepository $eventRepo
     */
    public function __construct(
        private EventRepository $eventRepo
    ) {}

    /**
     * @return SearchForm
     */
    public function getSearchForm(): SearchForm
    {
        return (new SearchForm())
            ->withSearchByList(new SearchByList([
                new DefaultSearchBy(),
            ]));
    }

    /**
     * @param FilterInterface|null $filter
     * @param array $scope
     * @return PaginatorInterface
     */
    public function getFullPaginator(FilterInterface $filter = null, array $scope = []): PaginatorInterface
    {
        $dataReader = $this->eventRepo
            ->getDataReader($scope);

        if ($filter !== null) {
            $dataReader = $dataReader->withFilter($filter);
        }

        return new OffsetPaginator(
            $dataReader->withSort(
                Sort::only(['id'])->withOrder(['id' => 'desc'])
            )
        );
    }
}
