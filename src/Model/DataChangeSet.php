<?php

namespace Mailery\Activity\Log\Model;

class DataChangeSet
{
    /**
     * @var string
     */
    private string $action;

    /**
     * @var object
     */
    private object $entity;

    /**
     * @var array
     */
    private array $oldValues = [];

    /**
     * @var array
     */
    private array $newValues = [];

    /**
     * @param objest $entity
     */
    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return object
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getOldValues(): array
    {
        return $this->oldValues;
    }

    /**
     * @return array
     */
    public function getNewValues(): array
    {
        return $this->newValues;
    }

    /**
     * @param string $action
     * @return self
     */
    public function withAction(string $action): self
    {
        $new = clone $this;
        $new->action = $action;

        return $new;
    }

    /**
     * @param array $values
     * @return self
     */
    public function withOldValues(array $values): self
    {
        $new = clone $this;
        $new->oldValues = $this->normalizeValues($values);

        return $new;
    }

    /**
     * @param array $values
     * @return self
     */
    public function withNewValues(array $values): self
    {
        $new = clone $this;
        $new->newValues = $this->normalizeValues($values);

        return $new;
    }

    /**
     * @return array
     */
    public function getChanges(): array
    {
        $oldValues = $this->getOldValues();
        $newValues = $this->getNewValues();

        $dataChanges = [];

        foreach ($newValues as $key => $value) {
            if (!isset($oldValues[$key]) || $oldValues[$key] !== $value) {
                $dataChanges[$key] = $value;
            }
        }

        foreach ($oldValues as $key => $value) {
            if (!isset($newValues[$key])) {
                $dataChanges[$key] = null;
            } else if ($newValues[$key] !== $oldValues[$key]) {
                $dataChanges[$key] = $newValues[$key];
            }
        }

        return $dataChanges;
    }

    /**
     * @param array $values
     * @return array
     */
    private function normalizeValues(array $values): array
    {
        $normalizer = function ($value): string {
            switch (gettype($value)) {
                case 'object':
                    return method_exists($value, '__toString') ? (string) $value : get_class($value);
                case 'boolean':
                    return $value ? 'true' : 'false';
                default:
                    return (string) $value;
            }
        };

        foreach ($values as $key => $value) {
            $values[$key] = $normalizer($value);
        }

        return $values;
    }
}
