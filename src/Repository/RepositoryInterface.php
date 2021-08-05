<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

/**
 * @template T
 */
interface RepositoryInterface
{
    /**
     * @return ?T
     */
    public function find(string $id);
}
