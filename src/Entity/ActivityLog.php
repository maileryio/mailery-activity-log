<?php

declare(strict_types=1);

namespace Mailery\Activity\Log\Entity;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "groups",
 *      repository = "Mailery\Activity\Log\Repository\ActivityLogRepository",
 *      mapper = "Yiisoft\Yii\Cycle\Mapper\TimestampedMapper"
 * )
 * @Cycle\Annotated\Annotation\Table(
 *      indexes = {
 *          @Cycle\Annotated\Annotation\Table\Index(columns = {"name"}, unique = true)
 *      }
 * )
 */
class ActivityLog
{
    /**
     * @Cycle\Annotated\Annotation\Column(type = "primary")
     * @var int|null
     */
    private $id;

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
}
