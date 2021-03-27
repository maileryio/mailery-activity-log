<?php

namespace Mailery\Activity\Log\Provider;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Activity\Log\Controller\DefaultController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create('/activity-log')
                ->routes(
                    Route::get('/default/index')
                        ->name('/activity-log/default/index')
                        ->action([DefaultController::class, 'index']),
                    Route::get('/default/view/{id:\d+}')
                        ->name('/activity-log/default/view')
                        ->action([DefaultController::class, 'view'])
                )
        );
    }
}
