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
            $this->handleGetRequest();
            break;
        default:
            botwebapi\JsonHttpResponse::sendClientError(405, $_SERVER['REQUEST_METHOD'].' is not supported');
            return;
        }
    }
    
    private function handleGetRequest()
    {
        $resource_manager = resources\ResourceManager::getInstance();
        $resources = $resource_manager->getResources();
        
        $resource_descriptors = array();
        foreach($resources as $uri => $resource)
        {
            $resource_descriptor = new ResourceDescriptor($resource->getName(),
                                                          $uri,
                                                          array('homepage' => $resource->getHomepage()));
            array_push($resource_descriptors, $resource_descriptor);
        }
        
        botwebapi\JsonHttpResponse::sendResponse(array('resources' => $resource_descriptors));
    }
}

?>
