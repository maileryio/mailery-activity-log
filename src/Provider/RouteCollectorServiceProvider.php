<?php

namespace Mailery\Activity\Log\Provider;

use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Activity\Log\Controller\DefaultController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/activity-log',
                [
                    Route::get('/default/index', [DefaultController::class, 'index'])
                        ->name('/activity-log/default/index'),
                    Route::get('/default/view/{id:\d+}', [DefaultController::class, 'view'])
                        ->name('/activity-log/default/view'),
                ]
            )
        );
    }
}
