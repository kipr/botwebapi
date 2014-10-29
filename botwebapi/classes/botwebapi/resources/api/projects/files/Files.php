<?php

namespace botwebapi\resources\api\projects\files;
use botwebapi\resources\api\projects as projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;


class Files extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    
    public function __construct($resource_name, $resource_uri, projects\Project $project_resource)
    {
        parent::__construct($resource_name, $resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->project_resource = $project_resource;
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
    
    protected function getChild($resource_name)
    {
        return NULL;
    }
}

?>
