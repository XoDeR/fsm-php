<?php

declare(strict_types=1);

use App\S04SM\Order;
use App\S04SM\OrderContext;
use App\S04SM\OrderEvent;
use App\S04SM\OrderState;
use App\S04SM\ReceiptToSend;
use App\S04SM\ShipmentToNotify;
use PHPUnit\Framework\TestCase;

class OrderStateMachineTest extends TestCase
{
    public function test_happy_path_emits_events(): void
    {
        $order = new Order(id: 1);
        $context = new OrderContext(
            orderId: 1,
            amountCents: 1000,
            authorizedCents: 1000
        );

        $order->apply(OrderEvent::Pay, $context);
        $events = $order->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ReceiptToSend::class, $events[0]);
        $this->assertSame(OrderState::Paid, $order->state());

        $order->apply(OrderEvent::Ship, $context);
        $events = $order->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ShipmentToNotify::class, $events[0]);
        $this->assertSame(OrderState::Shipped, $order->state());
    }

    public function test_guard_blocks_payment_without_authorization(): void
    {
        $order = new Order(id: 2);
        $context = new OrderContext(
            orderId: 2,
            amountCents: 1000,
            authorizedCents: 500
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard failed');
        $order->apply(OrderEvent::Pay, $context);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $order = new Order(id: 3);
        $context = new OrderContext(
            orderId: 3,
            amountCents: 1000,
            authorizedCents: 1000
        );

        $this->expectException(RuntimeException::class);
        $order->apply(OrderEvent::Ship, $context);
    }
}