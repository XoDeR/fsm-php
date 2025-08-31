<?php

namespace App\S04SM;

final class ReceiptToSend implements DomainEvent
{
    public function __construct(public int $orderId)
    {
    }
}
