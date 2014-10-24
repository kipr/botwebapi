<?php

namespace botwebapi\resources\api\sessions;
use botwebapi\resources as resources;

class Sessions implements resources\iBotWebApiResource
{
    public function getName()
    {
        return 'sessions';
    }
    
    public function getHomepage()
    {
        return 'https://github.com/kipr/botwebapi';
    }
    
    public function handleRequest()
    {
        echo 'handle /api/session request';
    }
}

?>
