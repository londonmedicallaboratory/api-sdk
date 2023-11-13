<?php

declare(strict_types=1);

namespace LML\SDK\Service\Visitor;

use DateTime;
use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest')]
#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onKernelResponse')]
class Visitor implements ResetInterface
{
    private const BASKET_ID_KEY = 'basket_id';
    private const AFFILIATE_CODE_KEY = 'affiliate_code';

    private ?string $affiliateCode = null;
    private ?string $basketId = null;

    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function reset(): void
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        Assert::nullOrString($affiliateCode = $event->getRequest()->query->get('affiliate'));
        if ($affiliateCode) {
            $this->affiliateCode = $affiliateCode;
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($affiliateCode = $this->affiliateCode) {
            $cookie = Cookie::create(self::AFFILIATE_CODE_KEY, $affiliateCode, expire: new DateTime('+1 day'));
            $response = $event->getResponse();
            $response->headers->setCookie($cookie);
        }
        if ($basketId = $this->basketId) {
            $cookie = Cookie::create(self::BASKET_ID_KEY, $basketId, expire: new DateTime('+1 day'));
            $response = $event->getResponse();
            $response->headers->setCookie($cookie);
        }
    }

    public function getBasketId(): ?string
    {
        Assert::nullOrString($cookie = $this->requestStack->getMainRequest()?->cookies->get(self::BASKET_ID_KEY));

        return $cookie;
    }

    public function setBasketId(string $id): void
    {
        $this->basketId = $id;
    }

    public function getAffiliateCode(): ?string
    {
        Assert::nullOrString($cookie = $this->requestStack->getMainRequest()?->cookies->get(self::AFFILIATE_CODE_KEY));

        return $this->affiliateCode ?? $cookie;
    }

}
