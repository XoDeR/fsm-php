<?php

namespace App\S04SM;

final class OrderContext
{
    public function __construct(
        public int $orderId,
        public int $amountCents,
        public int $authorizedCents,
        public \DateTimeImmutable $now = new \DateTimeImmutable()
    ) {
    }
}
