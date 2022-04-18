<?php

namespace Mailery\Activity\Log\Model;

class EntityGroup
{

    public function __construct(
        private string $key,
        private string $label,
        private array $entities
    ) {}

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function matchEntity(object $entity): bool
    {
        foreach ($this->entities as $entityClass) {
            if ($entity instanceof $entityClass) {
                return true;
            }
        }
        return false;
    }

}
