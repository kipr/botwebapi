<?php

namespace botwebapi\resources\api\projects\binaries;
use botwebapi\resources as resources;
use botwebapi as botwebapi;


class Files extends resources\BotWebApiResource
{
    public function __construct($name, $uri)
    {
        parent::__construct($name, $uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->location = $location;
    }
    
    protected function handleGetRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function handlePostRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function handlePutRequest()
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function handleDeleteRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($name)
    {
        return NULL;
    }
}

?>
