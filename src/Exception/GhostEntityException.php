<?php

declare(strict_types=1);

namespace LML\SDK\Exception;

class GhostEntityException extends SDKException
{
    public function __construct()
    {
        parent::__construct(message: 'You used ghost instance which prevents using methods other than \'getId\'. This is done for performance reasons, check the stacktrace.');
    }
}
