<?php

namespace Mailery\Activity\Log\Mapper;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Command\CommandInterface;
use Mailery\Activity\Log\Model\DataChangeSet;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Mapper\Proxy\ProxyEntityFactory;
use Mailery\User\Service\CurrentUserService;
use Cycle\ORM\Command\Special\Sequence;
use Mailery\Activity\Log\Entity\Event;
use Mailery\Activity\Log\Entity\EventDataChange;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Cycle\ORM\Command\Special\WrappedStoreCommand;
use Mailery\Activity\Log\Provider\EntityGroupsProvider;

class LoggableMapper extends Mapper
{
    /**
     * @var array
     */
    private array $pendingLogEntry = [];

    /**
     * @param CurrentUserService $currentUser
     * @param EntityGroupsProvider $entityGroups
     * @param ORMInterface $orm
     * @param ProxyEntityFactory $entityFactory
     * @param string $role
     */
    public function __construct(
        private CurrentUserService $currentUser,
        private EntityGroupsProvider $entityGroups,
        private ORMInterface $orm,
        ProxyEntityFactory $entityFactory,
        string $role
    ) {
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
        $command = parent::queueCreate($entity, $node, $state);

        $dataChangeSet = (new DataChangeSet($entity, $node, $state))
            ->withAction('Object created')
            ->withGroup($this->entityGroups->getGroup($entity)->getKey())
            ->withNewValues($this->extract($entity));

        if (($eventCommand = $this->getEventCommand($dataChangeSet)) === null) {
            return $command;
        }

        $sequence = new Sequence($command);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @inheritdoc
     */
    public function queueDelete($entity, Node $node, State $state): CommandInterface
    {
        $command = parent::queueDelete($entity, $node, $state);

        $dataChangeSet = (new DataChangeSet($entity, $node, $state))
            ->withAction('Object deleted')
            ->withGroup($this->entityGroups->getGroup($entity)->getKey())
            ->withOldValues($this->getPendingLogEntry($entity));

        if (($eventCommand = $this->getEventCommand($dataChangeSet)) === null) {
            return $command;
        }

        $sequence = new Sequence($command);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @inheritdoc
     */
    public function queueUpdate($entity, Node $node, State $state): CommandInterface
    {
        $command = parent::queueUpdate($entity, $node, $state);

        $dataChangeSet = (new DataChangeSet($entity, $node, $state))
            ->withAction('Object updated')
            ->withGroup($this->entityGroups->getGroup($entity)->getKey())
            ->withOldValues($this->getPendingLogEntry($entity))
            ->withNewValues($this->extract($entity));

        if (($eventCommand = $this->getEventCommand($dataChangeSet)) === null) {
            return $command;
        }

        $sequence = new Sequence($command);
        $sequence->addCommand($eventCommand);

        return $sequence;
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
     * @param DataChangeSet $dataChangeSet
     * @return CommandInterface|null
     */
    private function getEventCommand(DataChangeSet $dataChangeSet): ?CommandInterface
    {
        if (!$dataChangeSet->getEntity() instanceof LoggableEntityInterface) {
            return null;
        }

        $entity = $dataChangeSet->getEntity();
        $origin = $dataChangeSet->getState();
        $oldValues = $dataChangeSet->getOldValues();
        $changes = $dataChangeSet->getChanges();

        $user = $this->currentUser->getUser();
        $brand = method_exists($entity, 'getBrand') ? $entity->getBrand() : null;

        $state = new State(
            Node::SCHEDULED_INSERT,
            array_filter([
                'action' => $dataChangeSet->getAction(),
                'date' => new \DateTimeImmutable(),
                'group' => $dataChangeSet->getGroup(),
                'object_id' => $entity->getObjectId(),
                'object_label' => $entity->getObjectLabel(),
                'object_class' => $entity->getObjectClass(),
                'user_id' => $user !== $entity ? $user?->getId() : null,
                'brand_id' => $brand?->getId(),
            ])
        );

        $source = $this->orm->getSource(Event::class);

        $sequence = new Sequence(
            WrappedStoreCommand::createInsert(
                $source->getDatabase(),
                $source->getTable(),
                $state,
                null,
                ['id']
            )->withBeforeExecution(static function () use ($origin, $state): void {
                $state->register('object_id', $origin->getData()['id']);
            })
        );

        foreach ($changes as $field => $value) {
            if (empty($oldValues[$field]) && empty($value)) {
                continue;
            }

            $changeState = new State(
                Node::SCHEDULED_INSERT,
                array_filter([
                    'field' => $field,
                    'value_old' => $oldValues[$field] ?? null,
                    'value_new' => $value,
                ])
            );

            $source = $this->orm->getSource(EventDataChange::class);

            $sequence->addCommand(
                WrappedStoreCommand::createInsert(
                    $source->getDatabase(),
                    $source->getTable(),
                    $changeState,
                    null
                )->withBeforeExecution(static function () use ($changeState, $state): void {
                    $changeState->register('activity_event_id', $state->getData()['id']);
                })
            );
        }

        return $sequence;
    }
}
