<?php

namespace Mailery\Activity\Log\Entity;

interface LoggableEntityInterface
{
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
}