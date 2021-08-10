<?php

declare(strict_types=1);

namespace LML\SDK\Model;

/**
 * @template T
 */
interface ModelInterface
{
    public function getId(): string;

    /**
     * @return T
     */
    public function toArray();
}
