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

class Api implements resources\iBotWebApiResource
{
    public function getName()
    {
        return 'api';
    }
    
    public function getVersion()
    {
        return '1.0';
    }
    
    public function getHomepage()
    {
        return 'https://github.com/kipr/botwebapi';
    }
    
    public function handleRequest()
    {
        switch($_SERVER['REQUEST_METHOD'])
        {
        case 'GET':
        case 'HEAD':
            // returns a list of top-level resources
            return $this->handleGetRequest();
        default:
            return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
        }
    }
    
    private function handleGetRequest()
    {
        // returns a list of top-level resources
        $resource_manager = resources\ResourceManager::getInstance();
        $resources = $resource_manager->getResources();
        
        $resource_descriptors = array();
        foreach($resources as $uri => $resource)
        {
            $resource_descriptor = new ResourceDescriptor($resource->getName(),
                                                          $uri,
                                                          array('version' => $resource->getVersion(),
                                                                'homepage' => $resource->getHomepage()));
            array_push($resource_descriptors, $resource_descriptor);
        }
        
        return new botwebapi\JsonHttpResponse(200, array('resources' => $resource_descriptors));
    }
}

?>
