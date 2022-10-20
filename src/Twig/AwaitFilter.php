<?php

declare(strict_types=1);

namespace LML\SDK\Twig;

use Twig\TwigFilter;
use React\Promise\PromiseInterface;
use Twig\Extension\AbstractExtension;
use function Clue\React\Block\await;

class AwaitFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('await', [$this, 'await']),
        ];
    }

    /**
     * @template T
     *
     * @param PromiseInterface<T> $promise
     *
     * @return T
     */
    public function await(PromiseInterface $promise)
    {
        return await($promise);
    }
}
