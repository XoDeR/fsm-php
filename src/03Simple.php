<?php

namespace App;

use PHPUnit\Event\InvalidArgumentException;

enum OrderState: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}

enum OrderEvent: string
{
    case Pay = 'pay';
    case Ship = 'ship';
    case Cancel = 'cancel';
}

final class Transition
{
    public function __construct(
        public OrderState $to,
        public $guard = null,
        public $action = null
    ) {
        if ($guard !== null && !is_callable($guard)) {
            throw new InvalidArgumentException("Guard must be a callable or null");
        }

        if ($action !== null && !is_callable($action)) {
            throw new InvalidArgumentException("Action must be a callable or null");
        }
    }
}

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

$to = OrderState::New;
$guard1 = function () {

};
$action1 = function () {

};

$transition = new Transition($to, $guard1, $action1);
var_dump($transition);
