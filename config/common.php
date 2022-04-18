<?php

use Psr\Container\ContainerInterface;
use Mailery\Activity\Log\Repository\EventRepository;
use Mailery\Activity\Log\Entity\Event;
use Cycle\ORM\ORMInterface;
use Mailery\Activity\Log\Provider\EntityGroupsProvider;

/** @var array $params */

return [
    EventRepository::class => static function (ContainerInterface $container) {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(Event::class);
    },

    EntityGroupsProvider::class => [
        '__construct()' => [
            'groups' => $params['maileryio/mailery-activity-log']['entity-groups'],
            'default' => $params['maileryio/mailery-activity-log']['entity-groups']['default'],
        ],
    ],
];
