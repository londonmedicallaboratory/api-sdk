<?php

declare(strict_types=1);

namespace LML\SDK\Service\Visitor;

use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\RequestStack;

class Visitor
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function getAffiliateCode(): ?string
    {
        $session = $this->requestStack->getSession();
        Assert::nullOrString($affiliateCode = $session->get('affiliate_code'));

        return $affiliateCode;
    }
}
