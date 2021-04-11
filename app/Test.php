<?php


namespace App;


class Test
{

    public function test()
    {
        file_put_contents('r.txt', date('H:i:s'));
    }}