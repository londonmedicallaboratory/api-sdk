<?php

namespace LML\SDK\Entity\TestRegistration;

use LML\SDK\Enum\Model\NameableInterface;

enum TestRegistrationStatusEnum: string implements NameableInterface
{
    case PENDING = 'pending';
    case PENDING_AUTHENTICATION = 'pending_authentication';
    case SUCCESS = 'success';
    case PARTIAL_RESULT = 'partial_result';
    case FAILED = 'fail';
    case CANCELLED = 'cancelled';
    case PROCESSING_ERROR = 'processing_error';
    case CLOSED = 'closed';

    public function getName(): string
    {
        return match ($this) {
            self::SUCCESS => 'Success',
            self::PENDING => 'Pending',
            self::PENDING_AUTHENTICATION => 'Pending authentication',
            self::PARTIAL_RESULT => 'Partial',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::PROCESSING_ERROR => 'Processing error',
            self::CLOSED => 'Closed',
        };
    }
}