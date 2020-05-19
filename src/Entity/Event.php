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
use Doctrine\Common\Collections\Collection;
use Mailery\Brand\Entity\Brand;
use Mailery\User\Entity\User;
use Mailery\Common\Entity\RoutableEntityInterface;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "activity_events",
 *      repository = "Mailery\Activity\Log\Repository\EventRepository",
 *      mapper = "Yiisoft\Yii\Cycle\Mapper\TimestampedMapper"
 * )
 */
class Event implements RoutableEntityInterface
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
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Brand\Entity\Brand", nullable = true)
     * @var Brand|null
     */
    private $brand;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\User\Entity\User", nullable = true)
     * @var User|null
     */
    private $user;

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
     * @var Collection
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
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
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
     * @return Brand|null
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @return self
     */
    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getObjectId(): ?int
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
     * @return string|null
     */
    public function getObjectLabel(): ?string
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
     * @return string|null
     */
    public function getObjectClass(): ?string
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
    public function getObjectDataChanges(): Collection
    {
        return $this->objectDataChanges;
    }

    /**
     * @param Collection $objectDataChanges
     */
    public function setObjectDataChanges(Collection $objectDataChanges): Collection
    {
        $this->objectDataChanges = $objectDataChanges;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteName(): ?string
    {
        return '/activity-log/default/edit';
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteName(): ?string
    {
        return '/activity-log/default/view';
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }
}
