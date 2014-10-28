<?php

namespace botwebapi\resources\api;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class ResourceDescriptor
{
    public $name;
    public $uri;
    public $about;
    
    public function __construct($name, $uri, $about)
    {
        $this->name = $name;
        $this->uri = $uri;
        $this->about = $about;
    }
}

class Api extends resources\BotWebApiResource
{
    public function __construct($uri)
    {
        parent::__construct('api', $uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    protected function handleGetRequest()
    {
        // returns a list of all child resources
        $resource_descriptors = array();
        
        foreach (glob(__DIR__.'/*') as $file)
        {
            // a resource is always located in a directory
            if(is_dir($file) && !is_link($file))
            {
                // create the resource
                $resource_name = basename($file);
                $resource = $this->getChild($resource_name);
                
                // create the resource descriptor
                $resource_descriptor = new ResourceDescriptor($resource->getName(),
                                                              $resource->getUri(),
                                                              array('version' => $resource->getVersion(),
                                                                    'homepage' => $resource->getHomepage()));
                array_push($resource_descriptors, $resource_descriptor);
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('resources' => $resource_descriptors));
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
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\'.$name.'\\'.ucfirst($name);
            return new $resource_class_name($this->getUri().'/'.$name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
