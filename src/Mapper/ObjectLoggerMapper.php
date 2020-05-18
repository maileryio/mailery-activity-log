<?php

namespace Mailery\Activity\Log\Mapper;

use Cycle\ORM\MapperInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\CommandInterface;
use Mailery\Activity\Log\Service\ObjectLoggerService;
use Mailery\Activity\Log\Model\DataChangeSet;
use Cycle\ORM\ORMInterface;

class ObjectLoggerMapper implements MapperInterface
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var MapperInterface
     */
    private MapperInterface $mapper;

    /**
     * @var ObjectLoggerService
     */
    private ObjectLoggerService $loggerService;

    /**
     * @var array
     */
    private array $pendingLogEntry = [];

    /**
     * @param MapperInterface $mapper
     * @param ObjectLoggerService $loggerService
     */
    public function __construct(ORMInterface $orm, MapperInterface $mapper, ObjectLoggerService $loggerService)
    {
        $this->orm = $orm;
        $this->mapper = $mapper;
        $this->loggerService = $loggerService;
    }

    /**
     * @inheritdoc
     */
    public function extract($entity): array
    {
        return $this->mapper->extract($entity);
    }

    /**
     * @inheritdoc
     */
    public function getRole(): string
    {
        return $this->mapper->getRole();
    }

    /**
     * @inheritdoc
     */
    public function hydrate($entity, array $data): object
    {
        $hydrate = $this->mapper->hydrate($entity, $data);
        $this->setPendingLogEntry($entity);

        return $hydrate;
    }

    /**
     * @inheritdoc
     */
    public function init(array $data): array
    {
        return $this->mapper->init($data);
    }

    /**
     * @inheritdoc
     */
    public function queueCreate($entity, Node $node, State $state): ContextCarrierInterface
    {
        $dataChangeSet = (new DataChangeSet($entity))
            ->withAction('Object created')
            ->withNewValues($this->extract($entity));

        return $this->loggerService->queueCreate(
            $dataChangeSet,
            $this->mapper->queueCreate($entity, $node, $state)
        );
    }

    /**
     * @inheritdoc
     */
    public function queueDelete($entity, Node $node, State $state): CommandInterface
    {
        $dataChangeSet = (new DataChangeSet($entity))
            ->withAction('Object deleted')
            ->withOldValues($this->getPendingLogEntry($entity));

        return $this->loggerService->queueDelete(
            $dataChangeSet,
            $this->mapper->queueDelete($entity, $node, $state)
        );
    }

    /**
     * @inheritdoc
     */
    public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface
    {
        $dataChangeSet = (new DataChangeSet($entity))
            ->withAction('Object updated')
            ->withOldValues($this->getPendingLogEntry($entity))
            ->withNewValues($this->extract($entity));

        return $this->loggerService->queueUpdate(
            $dataChangeSet,
            $this->mapper->queueUpdate($entity, $node, $state)
        );
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
}
