<?php

use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Spiral\Database\DatabaseManager;
use Mailery\Activity\Log\Factory\ObjectLoggerFactory;
use Psr\Container\ContainerInterface;
use Mailery\Activity\Log\Service\ObjectLoggerService;

return [
    FactoryInterface::class => function (ContainerInterface $container) {
        $factory = new Factory($container->get(DatabaseManager::class), null, null, $container);
        $objectLogger = $container->get(ObjectLoggerService::class);

        return new ObjectLoggerFactory($factory, $objectLogger);
    },
];
