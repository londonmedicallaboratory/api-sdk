<?php

declare(strict_types=1);

namespace LML\SDK\Factory;


/**
 * @template T of object
 * @template R or array
 */
interface ModelMapperInterface
{
    /**
     * @psalm-param T $model
     *
     * @return R
     */
    public function toArray($model);

    /**
     * @param R $input
     *
     * @return T
     */
    public function fromArray($input);
}
