<?php

declare(strict_types=1);

use Mailery\Menu\MenuItem;
use Mailery\Activity\Log\Controller\DefaultController;
use Opis\Closure\SerializableClosure;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;

return [
    'activityLogNavbarMenuItem' => (new MenuItem())
        ->withLabel('Activity log')
        ->withUrl(new SerializableClosure(function (UrlGeneratorInterface $urlGenerator) {
            return $urlGenerator->generate('/activity-log/default/index');
        })),

    'cycle.common' => [
        'entityPaths' => [
            '@vendor/maileryio/mailery-activity-log/src/Entity',
        ],
    ],

    'router' => [
        'routes' => [
            '/activity-log/default/index' => Route::get('/activity-log/default/index', [DefaultController::class, 'index'])
                ->name('/activity-log/default/index'),
            '/activity-log/default/view' => Route::get('/activity-log/default/view/{id:\d+}', [DefaultController::class, 'view'])
                ->name('/activity-log/default/view'),
        ],
    ],
];
