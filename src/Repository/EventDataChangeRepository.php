<?php

declare(strict_types=1);

/**
 * Activity Log module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-activity-log
 * @package   Mailery\Activity\Log
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\Activity\Log\Repository;

use Cycle\ORM\Select\Repository;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

class EventDataChangeRepository extends Repository
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
