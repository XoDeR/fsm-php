<?php

namespace App\S03Simple;

enum OrderEvent: string
{
    case Pay = 'pay';
    case Ship = 'ship';
    case Cancel = 'cancel';
}
