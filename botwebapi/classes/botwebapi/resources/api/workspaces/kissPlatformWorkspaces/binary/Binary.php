<?php

namespace botwebapi\resources\api\workspaces\kissPlatformWorkspaces\binary;
use botwebapi\resources\api\workspaces\kissPlatformWorkspaces as kissPlatformWorkspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Binary extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    
    public function __construct($resource_uri, kissPlatformWorkspaces\Project $project_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $project_resource);
        $this->project_resource = $project_resource;
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink(array('path' => $this->project_resource->getResourceUri()),
                        array('rel' => 'project',
                              'additional' => array('name' => $this->project_resource->getProjectName())));
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
    {
        // currently, we (re-)compile all binaries at once
        $output = array();
        exec('programcompiler '.$this->project_resource->getProjectName(), $output);
        return new botwebapi\JsonHttpResponse(200, array("output" => $output));
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
