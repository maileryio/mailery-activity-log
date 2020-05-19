<?php

use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Spiral\Core\Container as SpiralContainer;
use Spiral\Database\DatabaseManager;
use Psr\Container\ContainerInterface;

return [
    FactoryInterface::class => function (ContainerInterface $container) {
        $spiralContainer = new SpiralContainer();
        $spiralContainer->bind(ContainerInterface::class, $container);
        return new Factory($container->get(DatabaseManager::class), null, $spiralContainer, $container);
    },
];
