<?php

require __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\S04SM\Order;
use App\S04SM\OrderContext;
use App\S04SM\OrderEvent;
use App\S04SM\OrderTransition;
use App\S04SM\TransitionHelper;

$order = new Order(id: 123);

file_put_contents('order-state-machine.mmd', TransitionHelper::toMermaid($order->transitions()));

$context = new OrderContext(
    orderId: 123,
    amountCents: 10_00,
    authorizedCents: 10_00
);

$logger = new Logger('my-app');
$logger->pushHandler(new StreamHandler('app.log', Logger::DEBUG));

$transition = new OrderTransition($logger);
// Guard checks funds -> state becomes Paid
$transition->apply($order, OrderEvent::Pay, $context);
$events = $order->pullDomainEvents(); // [ReceiptToSend(123)]

//var_dump($events);

// STEP 1 -> Persist $order->state to DB
// STEP 2 -> Dispatch $events to be handled, making the handlers idempotent

//var_dump($order);
