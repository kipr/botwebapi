<?php

namespace botwebapi\resources\api\workspaces\kissPlatformWorkspaces;
use botwebapi\resources\api\workspaces as workspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

// Set constants
define('ARCHIVES_ROOT_DIR', '/kovan/archives');
define('BINARIES_ROOT_DIR', '/kovan/bin');

class Workspace extends resources\BotWebApiResource
{

    public function __construct($resource_uri, $parent_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
        foreach (glob(ARCHIVES_ROOT_DIR.DIRECTORY_SEPARATOR.'*') as $file)
        {
            // Link projects are compressed and stored in a single archive
            if(!is_dir($file))
            {
                $project_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($project_name),
                                array('rel' => 'projects',
                                      'additional' => array('name' => $project_name)),
                                false);
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'name' => 'Kiss Platform Workspace',
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        try
        {
            $resource_class_name = __NAMESPACE__.'\\Project';
            return new $resource_class_name($resource_name, $this->getResourceUri().'/'.$resource_name, $this);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
