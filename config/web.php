<?php

declare(strict_types=1);

/**
 * Activity Log module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-activity-log
 * @package   Mailery\Activity\Log
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

$navbarSystem = $params['menu']['navbar']['items']['system'];
$navbarSystemChilds = $navbarSystem->getChildItems();
$navbarSystemChilds['activity-log'] = $params['activityLogNavbarMenuItem'];
$navbarSystem->setChildItems($navbarSystemChilds);

return [];
