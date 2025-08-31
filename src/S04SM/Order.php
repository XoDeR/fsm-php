<?php

namespace App\S04SM;

use App\S04SM\OrderEvent;
use App\S04SM\OrderState;
use App\S04SM\OrderContext;
use App\S04SM\Transition;
use App\S04SM\ReceiptToSend;
use App\S04SM\ShipmentToNotify;
use PHPUnit\Event\RuntimeException;

final class Order
{
    /** @var list<DomainEvent> */
    private array $outbox = [];

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

    /** @return array<string, array<string, Transition>> */
    public function transitions(): array
    {
        return $this->rules;
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

        $this->state = $transition->to;
        if ($transition->action && is_callable($transition->action)) {
            $events = ($transition->action)($context);
            foreach ($events as $event) {
                $this->outbox[] = $event;
            }
        }
    }

    public function pullDomainEvents(): array
    {
        $events = $this->outbox;
        $this->outbox = [];

        return $events;
    }

    private function buildRules(): array
    {
        return [
            OrderState::New->value => [
                OrderEvent::Pay->value => new Transition(
                    to: OrderState::Paid,
                    guard: fn (OrderContext $context) => $context->authorizedCents >= $context->amountCents,
                    action: fn (OrderContext $context) => [new ReceiptToSend($context->orderId)],
                ),
                OrderEvent::Cancel->value => new Transition(
                    to: OrderState::Cancelled,
                    action: fn (OrderContext $context) => [],
                ),
            ],
            OrderState::Paid->value => [
                OrderEvent::Ship->value => new Transition(
                    to: OrderState::Shipped,
                    action: fn (OrderContext $context) => [new ShipmentToNotify($context->orderId)],
                ),
            ],
            OrderState::Shipped->value => [], // Final state
            OrderState::Cancelled->value => [], // Final state
        ];
    }
}
