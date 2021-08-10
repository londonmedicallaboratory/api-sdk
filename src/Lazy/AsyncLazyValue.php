<?php

declare(strict_types=1);

namespace LML\SDK\Lazy;

use Closure;
use LML\View\Lazy\LazyValue;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @template T
 * @template R
 */
class AsyncLazyValue extends LazyValue
{
    /**
     * @param Closure(T): R $onComplete
     */
    public function __construct(private ResponseInterface $response, private Closure $onComplete)
    {
    }

    public function getValue()
    {
        $response = $this->response;
        $content = $response->toArray();

        $callback = $this->onComplete;

        return $callback($content);
    }
}
