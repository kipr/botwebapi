<?php

namespace botwebapi\resources\api\workspaces\kissPlatformWorkspaces\binaries;
use botwebapi\resources\api\workspaces\kissPlatformWorkspaces as kissPlatformWorkspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Binary extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    
    public function __construct($resource_uri, kissPlatformWorkspaces\Project $project_resource, $parent_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
        $this->project_resource = $project_resource;
    }
    
    public function get()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        return NULL;
    }
}

?>
