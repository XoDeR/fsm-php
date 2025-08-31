<?php

namespace App\S04SM;

use PHPUnit\Event\InvalidArgumentException;

final class Transition
{
    /**
     * @param null|callable(OrderContext):bool               $guard
     * @param null|callable(OrderContext):list<DomainEvent>  $action Produces events, no I/O here
     */
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
