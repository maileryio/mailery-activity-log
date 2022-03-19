<?php

declare(strict_types=1);

/**
 * Activity Log module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-activity-log
 * @package   Mailery\Activity\Log
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\Activity\Log\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Mailery\Activity\Log\Repository\EventDataChangeRepository;
use Cycle\ORM\Entity\Behavior;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(
    table: 'activity_event_data_changes',
    repository: EventDataChangeRepository::class
)]
#[Behavior\CreatedAt(
    field: 'createdAt',
    column: 'created_at'
)]
#[Behavior\UpdatedAt(
    field: 'updatedAt',
    column: 'updated_at'
)]
class EventDataChange
{
    #[Column(type: 'primary')]
    private int $id;

    #[BelongsTo(target: Event::class, innerKey: 'activity_event_id')]
    private Event $event;

    #[Column(type: 'string(255)')]
    private string $field;

    #[Column(type: 'text', nullable: true)]
    private ?string $valueOld = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $valueNew;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return self
     */
    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return self
     */
    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValueOld(): ?string
    {
        return $this->valueOld;
    }

    /**
     * @param string|null $valueOld
     * @return self
     */
    public function setValueOld(?string $valueOld): self
    {
        $this->valueOld = $valueOld;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValueNew(): ?string
    {
        return $this->valueNew;
    }

    /**
     * @param string|null $valueNew
     * @return self
     */
    public function setValueNew(?string $valueNew): self
    {
        $this->valueNew = $valueNew;

        return $this;
    }
}
