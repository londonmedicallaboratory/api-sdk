<?php

declare(strict_types=1);

namespace LML\SDK\Exception;

use Throwable;

class EntityNotPersistedException extends SDKException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('You must persist this entity first', previous: $previous);
    }
}
