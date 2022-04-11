<?php

namespace Mailery\Activity\Log\Model;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Mailery\Web\Widget\DateTimeFormat;

class DataChangeSet
{
    /**
     * @var string
     */
    private string $action;

    /**
     * @var string
     */
    private string $group;

    /**
     * @var array
     */
    private array $oldValues = [];

    /**
     * @var array
     */
    private array $newValues = [];

    /**
     * @param object $entity
     * @param Node $node
     * @param State $state
     */
    public function __construct(
        private object $entity,
        private Node $node,
        private State $state
    ) {}

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return object
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return Node
     */
    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
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
     * @param string $group
     * @return self
     */
    public function withGroup(string $group): self
    {
        $new = clone $this;
        $new->group = $group;

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
                    if (method_exists($value, '__toString')) {
                        return (string) $value;
                    }

                    switch (get_class($value)) {
                        case \DateTime::class:
                        case \DateTimeImmutable::class:
                            return DateTimeFormat::widget()
                                ->dateTime($value)
                                ->run();
                        default:
                            return get_class($value);
                    }
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
