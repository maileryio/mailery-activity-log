<?php

namespace Mailery\Activity\Log\Entity;

interface LoggableEntityInterface
{
    /**
     * @param string|int $id
     * @return self
     */
    public function setObjectId($id): self;

    /**
     * @return string|null
     */
    public function getObjectId(): ?string;

    /**
     * @return string|null
     */
    public function getObjectLabel(): ?string;

    /**
     * @return string|null
     */
    public function getObjectClass(): ?string;

    /**
     * @return array
     */
    public function getMaskedFields(): array;
}