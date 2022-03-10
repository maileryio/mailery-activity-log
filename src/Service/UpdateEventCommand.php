<?php

declare(strict_types=1);

namespace Mailery\Activity\Log\Service;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\DatabaseCommand;
use Cycle\ORM\Command\Database\Insert;
use Cycle\ORM\Command\ScopeCarrierInterface;
use Cycle\ORM\Command\Traits\ContextTrait;
use Cycle\ORM\Command\Traits\ErrorTrait;
use Cycle\ORM\Command\Traits\ScopeTrait;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Cycle\ORM\ORMInterface;
use Mailery\Activity\Log\Entity\Event;

final class UpdateEventCommand extends DatabaseCommand implements CommandInterface, ScopeCarrierInterface
{
    use ContextTrait;
    use ScopeTrait;
    use ErrorTrait;

    /**
     * @var LoggableEntityInterface
     */
    private LoggableEntityInterface $entity;

    public function __construct(ORMInterface $orm, LoggableEntityInterface $entity, Insert $insert)
    {
        $source = $orm->getSource(Event::class);
        parent::__construct($source->getDatabase(), $source->getTable());

        $this->entity = $entity;

        $this->waitContext('event_id');
        $insert->forward('object_id', $this, 'entity_id');
        $insert->forward(Insert::INSERT_ID, $this, 'event_id');
    }

    /**
     * @inheritdoc
     */
    public function isReady(): bool
    {
        return $this->waitContext === [] && $this->waitScope === [];
    }

    /**
     * Update data in associated table.
     */
    public function execute(): void
    {
        $this->scope = [
            'id' => $this->context['event_id'],
        ];

        $data = [
            'object_label' => $this->entity
                ->setObjectId($this->context['entity_id'])
                ->getObjectLabel()
        ];

        $this->db->update($this->table, $data, $this->scope)->run();

        parent::execute();
    }

    /**
     * @inheritdoc
     */
    public function register(string $key, $value, bool $fresh = false, int $stream = self::DATA): void
    {
        if ($stream == self::SCOPE) {
            if (empty($value)) {
                return;
            }

            $this->freeScope($key);
            $this->setScope($key, $value);

            return;
        }

        if ($fresh || $value !== null) {
            $this->freeContext($key);
        }

        if ($fresh) {
            // we only accept context when context has changed to avoid un-necessary
            // update commands
            $this->setContext($key, $value);
        }
    }
}
