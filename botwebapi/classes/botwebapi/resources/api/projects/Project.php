<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Project extends resources\BotWebApiResource
{
    private $location = '';
    
    public function __construct($name, $uri, $location)
    {
        if(!is_readable($location))
        {
            throw new \Exception('Invalid argument: $location');
        }
        
        parent::__construct($name, $uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->location = $location;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
    
    protected function handleGetRequest()
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function handlePostRequest()
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function handlePutRequest()
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function handleDeleteRequest()
    {
        if(unlink($this->location))
        {
            return new botwebapi\JsonHttpResponse(204, '');
        }
        else
        {
            return new botwebapi\JsonHttpResponse(404, 'Could not delete '.$this->location);
        }
    }
    protected function getChild($name)
    {
        return NULL;
    }
}

?>
