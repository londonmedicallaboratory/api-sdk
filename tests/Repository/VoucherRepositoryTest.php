<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Repository\VoucherRepository;

class VoucherRepositoryTest extends AbstractTest
{
    public function testInvalidCode(): void
    {
        self::bootKernel();
        $repo = $this->getVoucherRepository();
        $voucher = $repo->findOneBy(['code' => 'does not exist'], await: true);
        self::assertNull($voucher);
    }

    public function testValidCode(): void
    {
        self::bootKernel();
        $repo = $this->getVoucherRepository();
        $voucher = $repo->findOneBy(['code' => 'test-123-456'], await: true);
        self::assertInstanceOf(Voucher::class, $voucher);
    }

    private function getVoucherRepository(): VoucherRepository
    {
        $repo = $this->getService(VoucherRepository::class);
        $repo->clear();

        return $repo;
    }
}
