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
        try {
            if (method_exists($this, 'getId') && $this->getId()) {
                return $this->getId();
            }
        } catch (\Error $e) {}

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getObjectLabel(): string
    {
        if (method_exists($this, '__toString')) {
            return $this->__toString();
        }

        $path = explode('\\', $this->getObjectClass());
        return array_pop($path);
    }

    /**
     * @inheritdoc
     */
    public function getObjectClass(): string
    {
        return self::class;
    }

    /**
     * @inheritdoc
     */
    public function getMaskedFields(): array
    {
        return [];
    }
}