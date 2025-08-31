<?php

declare(strict_types=1);

namespace App\S04SM;

class TransitionHelper
{
    /**
     * @param array<string, array<string, Transition>>  $rules
     */
    public static function toMermaid(array $rules): string
    {
        $lines = ["stateDiagram-v2"];
        foreach ($rules as $from => $events) {
            foreach ($events as $event => $transition) {
                $to = $transition->to->value;
                $lines[] = "  {$from} --> {$to} : {$event}";
            }
        }
        return implode("\n", $lines)."\n";
    }
}
