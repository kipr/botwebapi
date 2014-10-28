<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class ProjectDescriptor
{
    public $name;
    public $uri;
    
    public function __construct($name, $uri)
    {
        $this->name = $name;
        $this->uri = $uri;
    }
}

class Projects extends resources\BotWebApiResource
{
    public function __construct($uri)
    {
        parent::__construct('projects', $uri, '1.0', 'https://github.com/kipr/botwebapi');
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
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function getChild($name)
    {
        return NULL;
    }
}

?>
