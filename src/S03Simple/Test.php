<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\S03Simple\Order;

$order = new Order(id: 123);

var_dump($order);
