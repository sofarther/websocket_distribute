<?php
declare(strict_types=1);

if(!function_exists('generate_server_key')){
    function generate_server_key($host,$port)
    {
        return $host .':' .$port;
    }
}
