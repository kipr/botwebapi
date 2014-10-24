<?php

namespace botwebapi\resources\api\sessions;
use botwebapi\resources as resources;

class Sessions implements resources\iBotWebApiResource
{
    function handleRequest()
    {
        echo 'handle /api/session request';
    }
}

?>
