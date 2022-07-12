<?php

declare(strict_types=1);

namespace LML\SDK\Tests;

use LML\SDK\Tests\Repository\LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractTest extends KernelTestCase
{
    /**
     * @template TClass
     *
     * @param class-string<TClass> $className
     *
     * @return TClass
     */
    protected function getService(string $className)
    {
        $repo = self::$kernel->getContainer()->get($className);

        return is_a($repo, $className, true) ? $repo : throw new LogicException();
    }
}
