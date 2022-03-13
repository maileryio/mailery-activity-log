<?php

namespace Mailery\Activity\Log\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Mailery\Activity\Log\Model\EntityGroup;

class EntityGroupsProvider
{

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $groups;

    /**
     * @var EntityGroup
     */
    private EntityGroup $default;

    /**
     * @param array $groups
     * @param array $default
     */
    public function __construct(array $groups, array $default)
    {
        $this->groups = new ArrayCollection();
        $this->default = new EntityGroup('default', $default['label'], $default['entities']);

        foreach ($groups as $key => $group) {
            $this->groups->add(new EntityGroup($key, $group['label'], $group['entities']));
        }
    }

    /**
     * @param object $entity
     * @return EntityGroup
     */
    public function getGroup(object $entity): EntityGroup
    {
        return $this->groups
            ->filter(fn (EntityGroup $group) => $group->matchEntity($entity))
            ->first() ?: $this->default;
    }

    /**
     * @param string $key
     * @return EntityGroup
     */
    public function getGroupByKey(string $key): EntityGroup
    {
        return $this->groups
            ->filter(fn (EntityGroup $group) => $group->getKey() === $key)
            ->first() ?: $this->default;
    }

}
