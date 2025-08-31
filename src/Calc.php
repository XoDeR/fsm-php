<?php

namespace App;

class Calc
{
    public function add(int $a, int $b): int
    {
        fwrite(STDOUT, "Debug info here\n");
        return $a + $b;
    }

    public function writeToConsole(): void
    {
        fwrite(STDOUT, "Debug info here\n");
        echo "test";
        print("test2");
        //print_r("test3");
        $a = "test4";
        //var_dump($a);
    }
}

//$calc = new Calc();
//$calc->writeToConsole();
