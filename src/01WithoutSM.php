<?php

namespace App;

// Without
class Order
{
    public bool $paid = false;
    public bool $shipped = false;
    public bool $cancelled = false;
    public bool $refunded = false;
}

$order = new Order();
var_dump($order);

if ($order->paid && !$order->shipped && !$order->cancelled) {
    $b = 3;
    // some actions
}

if ($order->shipped && $order->refunded) {
    // impossible
}

// With a State Machine

// State -- named phase
enum OrderState
{
    case New;
    case Paid;
    case Shipped;
    case Cancelled;
    case Refunded;
}

// Event -- pay, ship, cancel

$transitions = [
    OrderState::New => [
        'pay' => OrderState::Paid,
        'cancel' => OrderState::Cancelled,
    ],
    OrderState::Paid => [
        'ship' => OrderState::Shipped,
        'refund' => OrderState::Refunded,
    ],
    OrderState::Shipped => [
        // refund not allowed by design
    ],
];
