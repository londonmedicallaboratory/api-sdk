<?php

declare(strict_types=1);

namespace LML\SDK\Entity;

/**
 * @template T of array<string, mixed>
 */
interface ModelInterface
{
    /**
     * @todo Consider changing this to null|string but: problems **will** happen in identity-map
     */
    public function getId(): string;

    /**
     * @return T
     */
    public function toArray();
}
