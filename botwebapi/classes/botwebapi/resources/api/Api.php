<?php

namespace botwebapi\resources\api;
use botwebapi\resources as resources;

class Api implements resources\iBotWebApiResource
{
    function handleRequest()
    {
        echo 'handle /api request';
    }
}

?>
