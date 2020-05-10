<?php

declare(strict_types=1);

namespace Mailery\Activity\Log\Repository;

use Cycle\ORM\Select\Repository;
use Mailery\Widget\Search\Data\Reader\SelectDataReader;

class ActivityLogRepository extends Repository
{
    /**
     * @param array $scope
     * @param array $orderBy
     * @return SelectDataReader
     */
    public function getDataReader(array $scope = [], array $orderBy = []): SelectDataReader
    {
        return new SelectDataReader($this->select()->where($scope)->orderBy($orderBy));
    }
}
