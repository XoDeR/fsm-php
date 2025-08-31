<?php

declare(strict_types=1);

namespace App\S04SM;

use Psr\Log\LoggerInterface;
use Throwable;

final class OrderTransition
{
    public function __construct(private LoggerInterface $log)
    {
    }

    public function apply(Order $order, OrderEvent $event, OrderContext $context): void
    {
        $from = $order->state();
        $start = hrtime(true);

        try {
            $order->apply($event, $context);
            $to = $order->state();

            $this->log->info('order.transition.ok', [
                'order_id' => $order->id,
                'event' => $event->value,
                'from' => $from->value,
                'to' => $to->value,
                'ms' => (hrtime(true) - $start) / 1_000_000,
            ]);
        } catch (Throwable $exception) {
            $this->log->warning('order.transition.fail', [
                'order_id' => $order->id,
                'event' => $event->value,
                'from' => $from->value,
                'error' => $exception->getMessage(),
                'ms' => (hrtime(true) - $start) / 1_000_000,
            ]);

            throw $exception;
        }
    }
}
