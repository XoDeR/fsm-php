<?php

namespace App\S04SM;

final class ShipmentToNotify implements DomainEvent
{
    public function __construct(public int $orderId)
    {
    }
}
