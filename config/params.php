<?php

declare(strict_types=1);

/**
 * Activity Log module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-activity-log
 * @package   Mailery\Activity\Log
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Mailery\Menu\MenuItem;
use Opis\Closure\SerializableClosure;
use Yiisoft\Router\UrlGeneratorInterface;

return [
    'activityLogNavbarMenuItem' => (new MenuItem())
        ->withLabel('Activity log')
        ->withUrl(new SerializableClosure(function (UrlGeneratorInterface $urlGenerator) {
            return $urlGenerator->generate('/activity-log/default/index');
        }))
        ->withOrder(300),

    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-activity-log/src/Entity',
        ],
    ],
];
