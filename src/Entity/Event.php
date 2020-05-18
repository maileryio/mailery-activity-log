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

use Cycle\ORM\Relation\Pivoted\PivotedCollection;
use Cycle\ORM\Relation\Pivoted\PivotedCollectionInterface;
use Mailery\Activity\Log\SkipLoggingInterface;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "activity_events",
 *      repository = "Mailery\Activity\Log\Repository\EventLogRepository",
 *      mapper = "Yiisoft\Yii\Cycle\Mapper\TimestampedMapper"
 * )
 */
class Event implements SkipLoggingInterface
{
    /**
     * @Cycle\Annotated\Annotation\Column(type="primary")
     * @var int|null
     */
    private $id;

    /**
     * @Cycle\Annotated\Annotation\Column(type="datetime")
     * @var \DateTime
     */
    private $date;

    /**
     * @Cycle\Annotated\Annotation\Column(type="string(255)")
     * @var string
     */
    private $action;

    /**
     * @Cycle\Annotated\Annotation\Column(type="string(255)")
     * @var string
     */
    private $module;

    /**
     * @Cycle\Annotated\Annotation\Column(type="integer", nullable=true)
     * @var int
     */
    private $objectId;

    /**
     * @Cycle\Annotated\Annotation\Column(type="string(255)", nullable=true)
     * @var string
     */
    private $objectLabel;

    /**
     * @Cycle\Annotated\Annotation\Column(type="string(255)", nullable=true)
     * @var string
     */
    private $objectClass;

    /**
     * @Cycle\Annotated\Annotation\Relation\HasMany(target="Mailery\Activity\Log\Entity\EventDataChange")
     * @var PivotedCollectionInterface
     */
    private $objectDataChanges;

    public function __construct()
    {
        $this->objectDataChanges = new PivotedCollection();
    }

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
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDate(\DateTime $dateTime)
    {
        $this->date = $dateTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule(string $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return int
     */
    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectLabel(): string
    {
        return $this->objectLabel;
    }

    /**
     * @param string $objectLabel
     */
    public function setObjectLabel(string $objectLabel)
    {
        $this->objectLabel = $objectLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    /**
     * @param string $objectClass
     */
    public function setObjectClass(string $objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectDataChanges(): PivotedCollectionInterface
    {
        return $this->objectDataChanges;
    }

    /**
     * @param PivotedCollectionInterface $objectDataChanges
     */
    public function setObjectDataChanges(PivotedCollectionInterface $objectDataChanges): PivotedCollectionInterface
    {
        $this->objectDataChanges = $objectDataChanges;

        return $this;
    }
}
