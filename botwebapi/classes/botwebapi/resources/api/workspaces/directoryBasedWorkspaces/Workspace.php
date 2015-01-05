<?php

namespace botwebapi\resources\api\workspaces\directoryBasedWorkspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Workspace extends resources\BotWebApiResource
{
    private $path = '';
    
    public function __construct($workspace_path, $resource_uri)
    {
        if(!is_readable($workspace_path))
        {
            error_log('Invalid argument: '.$workspace_path);
            throw new \Exception('Invalid argument: $workspace_path');
        }
        
        $this->path = $workspace_path;
        
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
        foreach (glob($this->path.DIRECTORY_SEPARATOR.'*') as $file)
        {
            if(is_dir($file))
            {
                $project_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($project_name),
                                array('rel' => 'projects',
                                      'additional' => array('name' => $project_name)),
                                false);
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'path' => str_replace(':/', ':', $this->path),
                                                         'name' => basename($this->path),
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
            $project_path = $this->path.DIRECTORY_SEPARATOR.$resource_name;
            return new $resource_class_name($resource_name, $project_path, $this->getResourceUri().'/'.urlencode($resource_name));
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
