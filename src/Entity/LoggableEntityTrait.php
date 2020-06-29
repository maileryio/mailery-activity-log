<?php

namespace Mailery\Activity\Log\Entity;

trait LoggableEntityTrait
{
    /**
     * @inheritdoc
     */
    public function setObjectId($id): self
    {
        if (method_exists($this, 'setId') && $this->setId($id)) {
            $this->setId($id);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getObjectId(): ?string
    {
        if (method_exists($this, 'getId') && $this->getId()) {
            return $this->getId();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getObjectLabel(): ?string
    {
        if (method_exists($this, '__toString')) {
            return $this->__toString();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getObjectClass(): ?string
    {
        return get_class($this);
    }
}