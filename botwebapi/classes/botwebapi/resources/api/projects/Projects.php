<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;

class Projects implements resources\iBotWebApiResource
{
    public function getName()
    {
        return 'projects';
    }
    
    public function getVersion()
    {
        return '0.0';
    }
    
    public function getHomepage()
    {
        return 'https://github.com/kipr/botwebapi';
    }
    
    public function handleRequest()
    {
        echo 'handle /api/projects request';
    }
}

?>
