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

use Mailery\Activity\Log\SkipLoggingInterface;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "activity_event_data_changes",
 *      repository = "Mailery\Activity\Log\Repository\DataChangeRepository",
 *      mapper = "Yiisoft\Yii\Cycle\Mapper\TimestampedMapper"
 * )
 */
class EventDataChange implements SkipLoggingInterface
{
    /**
     * @Cycle\Annotated\Annotation\Column(type="primary")
     * @var int|null
     */
    private $id;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Activity\Log\Entity\Event")
     * @var Event
     */
    private $event;

    /**
     * @Cycle\Annotated\Annotation\Column(type="string")
     * @var string
     */
    private $field;

    /**
     * @Cycle\Annotated\Annotation\Column(type="text", nullable=true)
     * @var string
     */
    private $valueOld;

    /**
     * @Cycle\Annotated\Annotation\Column(type="text", nullable=true)
     * @var string
     */
    private $valueNew;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id ? (string) $this->id : null;
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
