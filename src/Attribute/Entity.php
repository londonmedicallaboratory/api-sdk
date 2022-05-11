<?php

declare(strict_types=1);

namespace LML\SDK\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    /**
     * @param class-string $repositoryClass
     */
    public function __construct(
        private string $repositoryClass,
        private string $baseUrl,
    )
    {
    }

    /**
     * @return class-string
     */
    public function getRepositoryClass(): string
    {
        return $this->repositoryClass;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
