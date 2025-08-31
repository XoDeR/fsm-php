<?php

namespace App\S03Simple;

use PHPUnit\Event\InvalidArgumentException;

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
