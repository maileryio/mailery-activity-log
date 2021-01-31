<?php

namespace Mailery\Activity\Log\Service;

use Mailery\Activity\Log\Model\DataChangeSet;
use Mailery\Activity\Log\Entity\Event;
use Mailery\Activity\Log\Entity\EventDataChange;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\User\Service\CurrentUserService;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Cycle\ORM\Command\Branch\ContextSequence;
use Cycle\ORM\Command\Branch\Sequence;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Command\Database\Insert;
use Cycle\ORM\Command\Database\Delete;
use Cycle\ORM\Command\CommandInterface;

class ObjectLoggerService
{
    /**
     * @var CurrentUserService
     */
    private CurrentUserService $currentUser;

    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param CurrentUserService $currentUser
     * @param ORMInterface $orm
     */
    public function __construct(CurrentUserService $currentUser, ORMInterface $orm)
    {
        $this->currentUser = $currentUser;
        $this->orm = $orm;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param Insert $insert
     * @return ContextCarrierInterface
     */
    public function queueCreate(DataChangeSet $dataChangeSet, Insert $insert): ContextCarrierInterface
    {
        if (($eventCommand = $this->getEventCommand($dataChangeSet, $insert)) === null) {
            return $insert;
        }

        $eventCommand->waitContext('object_id');
        $insert->forward(Insert::INSERT_ID, $eventCommand, 'object_id');

        foreach ($eventCommand->getCommands() as $sequence) {
            if (!$sequence instanceof Sequence) {
                continue;
            }

            foreach ($sequence->getCommands() as $command) {
                $data = $command->getData();
                if (isset($data['field']) && $data['field'] === 'id') {
                    $command->waitContext('value_new');
                    $insert->forward(Insert::INSERT_ID, $command, 'value_new');
                }
            }
        }

        $entity = clone $dataChangeSet->getEntity();
        $eventUpdateCommand = new UpdateEventCommand($this->orm, $entity, $eventCommand->getPrimary());

        $sequence = new ContextSequence();
        $sequence->addPrimary($insert);
        $sequence->addCommand($eventCommand);
        $sequence->addCommand($eventUpdateCommand);

        return $sequence;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param Update $update
     * @return ContextCarrierInterface
     */
    public function queueUpdate(DataChangeSet $dataChangeSet, Update $update): ContextCarrierInterface
    {
        if (($eventCommand = $this->getEventCommand($dataChangeSet, $update)) === null) {
            return $update;
        }

        $sequence = new ContextSequence();
        $sequence->addPrimary($update);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param CommandInterface $delete
     * @return CommandInterface
     */
    public function queueDelete(DataChangeSet $dataChangeSet, CommandInterface $delete): CommandInterface
    {
        if (($eventCommand = $this->getEventCommand($dataChangeSet, $delete)) === null) {
            return $delete;
        }

        $sequence = new Sequence();
        $sequence->addCommand($delete);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param CommandInterface $delete
     * @return ContextCarrierInterface|null
     */
    private function getEventCommand(DataChangeSet $dataChangeSet, CommandInterface $delete): ?ContextCarrierInterface
    {
        $entity = $dataChangeSet->getEntity();

        if (!$entity instanceof LoggableEntityInterface) {
            return null;
        }

        $oldValues = $dataChangeSet->getOldValues();
        $dataChanges = $dataChangeSet->getChanges();

        if (empty($dataChanges)) {
            return null;
        }

        $event = new Event();
        $event->setAction($dataChangeSet->getAction());
        $event->setDate(new \DateTime('now'));
        $event->setModule($dataChangeSet->getModule());

        if (!$delete instanceof Delete) {
            $event->setObjectId($entity->getObjectId());
            $event->setObjectLabel($entity->getObjectLabel());
            $event->setObjectClass($entity->getObjectClass());
        }

        if (($user = $this->currentUser->getUser()) !== null && $user !== $entity) {
            $event->setUser($user);
        }
        if (method_exists($entity, 'getBrand') && ($brand = $entity->getBrand()) !== null) {
            $event->setBrand($brand);
        }

        foreach ($dataChanges as $field => $value) {
            $eventDataChange = new EventDataChange();
            $eventDataChange->setField($field);
            $eventDataChange->setValueOld($oldValues[$field] ?? null);
            $eventDataChange->setValueNew($value);

            $event->getObjectDataChanges()->add($eventDataChange);
        }

        return $this->orm->queueStore($event, Transaction::MODE_CASCADE);
    }
}
