<?php

namespace App\S04SM;

enum OrderEvent: string
{
    case Pay = 'pay';
    case Ship = 'ship';
    case Cancel = 'cancel';
}
