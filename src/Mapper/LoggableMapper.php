<?php

namespace Mailery\Activity\Log\Mapper;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Command\CommandInterface;
use Mailery\Activity\Log\Service\ObjectLoggerService;
use Mailery\Activity\Log\Model\DataChangeSet;
use Cycle\ORM\ORMInterface;
use Mailery\Common\Mapper\BaseMapper;
use Psr\Container\ContainerInterface;
use Cycle\ORM\Mapper\Proxy\ProxyEntityFactory;

class LoggableMapper extends BaseMapper
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var array
     */
    private array $pendingLogEntry = [];

    /**
     * @param ContainerInterface $container
     * @param ORMInterface $orm
     * @param string $role
     */
    public function __construct(ContainerInterface $container, ORMInterface $orm, ProxyEntityFactory $entityFactory, string $role)
    {
        $this->container = $container;
        parent::__construct($orm, $entityFactory, $role);
    }

    /**
     * @inheritdoc
     */
    public function hydrate($entity, array $data): object
    {
        $hydrate = parent::hydrate($entity, $data);
        $this->setPendingLogEntry($entity);

        return $hydrate;
    }

    /**
     * @inheritdoc
     */
    public function queueCreate($entity, Node $node, State $state): CommandInterface
    {
        $dataChangeSet = (new DataChangeSet($entity))
            ->withAction('Object created')
            ->withModule($this->getModule())
            ->withNewValues($this->extract($entity));

        return $this->getObjectLoggerService()->queueCreate(
            $dataChangeSet,
            parent::queueCreate($entity, $node, $state)
        );
    }

    /**
     * @inheritdoc
     */
    public function queueDelete($entity, Node $node, State $state): CommandInterface
    {
        $dataChangeSet = (new DataChangeSet($entity))
            ->withAction('Object deleted')
            ->withModule($this->getModule())
            ->withOldValues($this->getPendingLogEntry($entity));

        return $this->getObjectLoggerService()->queueDelete(
            $dataChangeSet,
            parent::queueDelete($entity, $node, $state)
        );
    }

    /**
     * @inheritdoc
     */
    public function queueUpdate($entity, Node $node, State $state): CommandInterface
    {
        $dataChangeSet = (new DataChangeSet($entity))
            ->withAction('Object updated')
            ->withModule($this->getModule())
            ->withOldValues($this->getPendingLogEntry($entity))
            ->withNewValues($this->extract($entity));

        return $this->getObjectLoggerService()->queueUpdate(
            $dataChangeSet,
            parent::queueUpdate($entity, $node, $state)
        );
    }

    /**
     * @return string
     */
    protected function getModule(): string
    {
        return 'Default';
    }

    /**
     * @param object $entity
     * @return array
     */
    private function getPendingLogEntry(object $entity): array
    {
        $objectId = spl_object_hash($entity);
        return $this->pendingLogEntry[$objectId] ?? [];
    }

    /**
     * @param object $entity
     */
    private function setPendingLogEntry(object $entity)
    {
        $objectId = spl_object_hash($entity);
        $this->pendingLogEntry[$objectId] = $this->extract($entity);
    }

    /**
     * @return ObjectLoggerService
     */
    private function getObjectLoggerService(): ObjectLoggerService
    {
        return $this->container->get(ObjectLoggerService::class);
    }
}
