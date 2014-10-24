<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;

class Projects implements resources\iBotWebApiResource
{
    function handleRequest()
    {
        echo 'handle /api/projects request';
    }
}

?>
