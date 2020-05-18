<?php

namespace Mailery\Activity\Log\Service;

use Mailery\Activity\Log\Model\DataChangeSet;
use Mailery\Activity\Log\SkipLoggingInterface;
use Mailery\Activity\Log\Entity\Event;
use Mailery\Activity\Log\Entity\EventDataChange;
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
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param ORMInterface $orm
     * @return self
     */
    public function withOrm(ORMInterface $orm): self
    {
        $new = clone $this;
        $new->orm = $orm;

        return $new;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param Insert $insert
     * @return ContextCarrierInterface
     */
    public function queueCreate(DataChangeSet $dataChangeSet, Insert $insert): ContextCarrierInterface
    {
        if (($eventCommand = $this->getEventCommand($dataChangeSet)) === null) {
            return $insert;
        }

        $eventCommand->waitContext('object_id');
        $insert->forward(Insert::INSERT_ID, $eventCommand, 'object_id');

        foreach ($eventCommand->getCommands()[1]->getCommands() as $command) {
            $data = $command->getData();
            if ($data['field'] === 'id') {
                $insert->forward(Insert::INSERT_ID, $command, 'value_new');
            }
        }

        $sequence = new ContextSequence();
        $sequence->addPrimary($insert);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param Update $update
     * @return ContextCarrierInterface
     */
    public function queueUpdate(DataChangeSet $dataChangeSet, Update $update): ContextCarrierInterface
    {
        if (($eventCommand = $this->getEventCommand($dataChangeSet)) === null) {
            return $update;
        }

        $sequence = new ContextSequence();
        $sequence->addPrimary($update);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @param Delete $delete
     * @return CommandInterface
     */
    public function queueDelete(DataChangeSet $dataChangeSet, Delete $delete): CommandInterface
    {
        if (($eventCommand = $this->getEventCommand($dataChangeSet)) === null) {
            return $delete;
        }

        $sequence = new Sequence();
        $sequence->addCommand($delete);
        $sequence->addCommand($eventCommand);

        return $sequence;
    }

    /**
     * @param DataChangeSet $dataChangeSet
     * @return ContextCarrierInterface|null
     */
    private function getEventCommand(DataChangeSet $dataChangeSet): ?ContextCarrierInterface
    {
        $entity = $dataChangeSet->getEntity();
        if ($entity instanceof SkipLoggingInterface) {
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
        $event->setModule('Default');

        if (method_exists($entity, 'getId') && $entity->getId()) {
            $event->setObjectId($entity->getId());
        }
        if (method_exists($entity, '__toString')) {
            $event->setObjectLabel((string) $entity);
        }

        $event->setObjectClass(get_class($entity));

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
