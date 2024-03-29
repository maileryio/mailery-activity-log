<?php

declare(strict_types=1);

/**
 * Activity Log module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-activity-log
 * @package   Mailery\Activity\Log
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Definitions\DynamicReference;

return [
    'maileryio/mailery-menu-navbar' => [
        'items' => [
            'system' => [
                'items' => [
                    'activity-log' => [
                        'label' => static function () {
                            return 'Activity log';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return strtok($urlGenerator->generate('/activity-log/default/index'), '?');
                        },
                    ],
                ],
            ]
        ],
    ],

    'maileryio/mailery-activity-log' => [
        'entity-groups' => [
            'default' => [
                'label' => DynamicReference::to(static fn () => 'Default'),
                'entities' => [],
            ],
        ],
    ],

    'yiisoft/yii-cycle' => [
        'entity-paths' => [
            '@vendor/maileryio/mailery-activity-log/src/Entity',
        ],
    ],
];
