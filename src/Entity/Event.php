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
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Mailery\Brand\Entity\Brand;
use Mailery\User\Entity\User;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Activity\Log\Repository\EventRepository;
use Cycle\ORM\Entity\Behavior;
use Mailery\Activity\Log\Entity\EventDataChange;
use Cycle\ORM\Collection\DoctrineCollectionFactory;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity(
    table: 'activity_events',
    repository: EventRepository::class
)]
#[Behavior\CreatedAt(
    field: 'createdAt',
    column: 'created_at'
)]
#[Behavior\UpdatedAt(
    field: 'updatedAt',
    column: 'updated_at'
)]
class Event implements RoutableEntityInterface
{
    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $date;

    #[Column(type: 'string(255)')]
    private string $action;

    #[Column(type: 'string(255)')]
    private string $module;

    #[BelongsTo(target: Brand::class, nullable: true, fkAction: 'SET NULL')]
    private ?Brand $brand = null;

    #[BelongsTo(target: User::class, nullable: true, fkAction: 'SET NULL')]
    private ?User $user = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $objectId = null;

    #[Column(type: 'string(255)', nullable: true)]
    private ?string $objectLabel = null;

    #[Column(type: 'string(255)', nullable: true)]
    private ?string $objectClass = null;

    #[HasMany(target: EventDataChange::class, collection: DoctrineCollectionFactory::class)]
    private ArrayCollection $objectDataChanges;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->objectDataChanges = new ArrayCollection();
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
     * @param \DateTimeImmutable $dateTime
     */
    public function setDate(\DateTimeImmutable $dateTime)
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
     * @param int|null $objectId
     */
    public function setObjectId(?int $objectId)
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
     * @return ArrayCollection
     */
    public function getObjectDataChanges(): ArrayCollection
    {
        return $this->objectDataChanges;
    }

    /**
     * @param ArrayCollection $objectDataChanges
     */
    public function setObjectDataChanges(ArrayCollection $objectDataChanges): ArrayCollection
    {
        $this->objectDataChanges = $objectDataChanges;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteName(): ?string
    {
        return '/activity-log/default/index';
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteParams(): array
    {
        return [];
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
    public function getDeleteRouteName(): ?string
    {
        return '/activity-log/default/delete';
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @return LoggableEntityInterface|null
     */
    public function getEntity(): ?LoggableEntityInterface
    {
        if (($className = $this->getObjectClass()) !== null && class_exists($className)) {
            $entity = new $className;

            if ($this->getObjectId() && method_exists($entity, 'setId')) {
                $entity->setId($this->getObjectId());
            }
            return $entity;
        }
        return null;
    }
}
