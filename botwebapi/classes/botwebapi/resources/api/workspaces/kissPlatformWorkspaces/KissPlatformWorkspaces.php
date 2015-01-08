<?php

namespace botwebapi\resources\api\workspaces\kissPlatformWorkspaces;
use botwebapi\resources\api\workspaces as workspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class KissPlatformWorkspaces extends resources\BotWebApiResource
{

    public function __construct($resource_uri, $parent_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink($this->getResourceUri().'/workspace', array('rel' => 'workspace'));
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
        // we need a DB for this...
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function getChild($resource_name)
    {
        if($resource_name != 'workspace')
        {
            return NULL;
        }
        
        try
        {
            $resource_class_name = __NAMESPACE__.'\\Workspace';
            return new $resource_class_name($this->getResourceUri().'/workspace', $this);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
