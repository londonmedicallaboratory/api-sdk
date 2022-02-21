<?php

declare(strict_types=1);

namespace LML\SDK\Entity;

/**
 * @template T of array<string, mixed>
 */
interface ModelInterface
{
    public function getId(): string;

    /**
     * @return T
     */
    public function toArray();
}
