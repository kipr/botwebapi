<?php

namespace botwebapi\resources\api;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Api extends resources\BotWebApiResource
{
    public function __construct($resource_name, $resource_uri)
    {
        parent::__construct($resource_name, $resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        foreach (glob(__DIR__.'/*') as $file)
        {
            // the children of api are always located in a directory
            if(is_dir($file) && !is_link($file))
            {
                $child_resource_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($child_resource_name),
                                array('rel' => $child_resource_name));
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
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
    
    protected function getChild($resource_name)
    {
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\'.$resource_name.'\\'.ucfirst($resource_name);
            return new $resource_class_name($resource_name, $this->getResourceUri().'/'.$resource_name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
