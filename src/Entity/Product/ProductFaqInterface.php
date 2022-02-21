<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Product;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      question: string,
 *      answer: string,
 *      priority: int,
 * }
 *
 * @extends ModelInterface<S>
 */
interface ProductFaqInterface extends ModelInterface
{
    public function getQuestion(): string;

    public function getAnswer(): string;

    public function getPriority(): int;
}
