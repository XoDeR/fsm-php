<?php

namespace App\S03Simple;

use App\S03Simple\OrderEvent;
use App\S03Simple\OrderState;
use App\S03Simple\OrderContext;
use App\S03Simple\Transition;
use PHPUnit\Event\RuntimeException;

final class Order
{
    /** @var array<string, array<string, Transition>> */
    private array $rules;

    public function __construct(
        public int $id,
        private OrderState $state = OrderState::New,
    ) {
        $this->rules = $this->buildRules();
    }

    public function state(): OrderState
    {
        return $this->state;
    }

    public function apply(OrderEvent $event, OrderContext $context): void
    {
        $transition = $this->rules[$this->state->value][$event->value] ?? null;
        if (!$transition instanceof Transition) {
            throw new RuntimeException("Invalid transition: {$this->state->value} + {$event->value}");
        }

        if ($transition->guard && !($transition->guard)($context)) {
            throw new RuntimeException('Guard failed');
        }

        if ($transition->action && is_callable($transition->action)) {
            $transition->action($context);
        }

        $this->state = $transition->to;
    }

    private function buildRules(): array
    {
        return [
            OrderState::New->value => [
                OrderEvent::Pay->value => new Transition(
                    to: OrderState::Paid,
                    guard: fn (OrderContext $context) => $context->authorizedCents >= $context->amountCents,
                    action: fn (OrderContext $context) => $this->sendReceipt($context->orderId),
                ),
                OrderEvent::Cancel->value => new Transition(
                    to: OrderState::Cancelled,
                ),
            ],
            OrderState::Paid->value => [
                OrderEvent::Ship->value => new Transition(
                    to: OrderState::Shipped,
                    action: fn (OrderContext $context) => $this->notifyShipment($context->orderId),
                ),
            ],
            OrderState::Shipped->value => [], // Final state
            OrderState::Cancelled->value => [], // Final state
        ];
    }

    private function sendReceipt(int $orderId): void
    {
        // Send receipt
    }

    private function notifyShipment(int $orderId): void
    {
        // Notify about shipment
    }
}
