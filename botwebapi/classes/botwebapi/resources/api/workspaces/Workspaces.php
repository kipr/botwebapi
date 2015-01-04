<?php

namespace botwebapi\resources\api\workspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

/**
 * Projects Resource
 * 
 * Its purpose is to serve as a container for project resources.
 * As project handling is platform-dependent, this resource redirects a request of the form
 * .../<this>/<workspace-provider> to a class located at a subdirectory:
 * Per convention, all workspace provider resource classes
 *  *  are named: __NAMESPACE__\<workspace-provider>\<Workspace-provider>
 *     => this implies that the class file location will be __DIR__/<workspace-provider>\<Workspace-provider>
 */
class Workspaces extends resources\BotWebApiResource
{
    public function __construct($resource_uri)
    {
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
        
        foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'*') as $file)
        {
            if(is_dir($file))
            {
                try
                {
                    $workspace_provider_name = basename($file);
                    $links->addLink($this->getResourceUri().'/'.urlencode($workspace_provider_name),
                                    array('rel' => 'workspace_provider',
                                          'additional' => array('name' => $workspace_provider_name)),
                                    false);
                }
                catch(\Exception $e) { }
            }
        }
        
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
        try
        {
            $workspace_provider_name = urldecode($resource_name);
            $workspace_provider_class_name
                = __NAMESPACE__.'\\'.$workspace_provider_name.'\\'.ucfirst($workspace_provider_name);
            return new $workspace_provider_class_name($this->getResourceUri().'/'.$resource_name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}
?>
