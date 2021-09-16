<?php

declare(strict_types=1);

namespace LML\functions;

use Closure;

/**
 * @template TKey
 * @template TValue
 *
 * @param iterable<TKey, TValue> $input
 * @param Closure(TValue, TKey): void $callback
 */
function each(iterable $input, Closure $callback): void {
    foreach ($input as $key => $datum) {
        $callback($datum, $key);
    }
}
