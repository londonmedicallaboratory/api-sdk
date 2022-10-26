<?php
declare(strict_types=1);

namespace LML\SDK\Entity\FAQ;

use LML\SDK\Enum\Model\NameableInterface;

enum FAQCategoryTypeEnum: string implements NameableInterface
{
    case CUSTOMER = 'customer';
    case HEALTH_CHECK = 'health_check';
    case CLINICAL_HUB = 'clinical_hub';

    public function getName(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer FAQs',
            self::HEALTH_CHECK => 'Health Check FAQs',
            self::CLINICAL_HUB => 'Clinical hubs FAQs',
        };
    }
}
