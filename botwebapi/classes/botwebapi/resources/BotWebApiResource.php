<?php

namespace botwebapi\resources;
use botwebapi as botwebapi;

abstract class BotWebApiResource
{
    private $resource_uri = '';
    private $resource_version = '';
    private $resource_homepage = '';
    
    public function __construct($resource_uri, $resource_version, $resource_homepage)
    {
        $this->resource_uri = $resource_uri;
        $this->resource_version = $resource_version;
        $this->resource_homepage = $resource_homepage;
    }
    
    public function getResourceUri()
    {
        return $this->resource_uri;
    }
    
    public function getResourceVersion()
    {
        return $this->resource_version;
    }
    
    public function getResourceHomepage()
    {
        return $this->resource_homepage;
    }
    
    public function handleRequest()
    {
        // check if a child addressed
        preg_match('`^'.$this->getResourceUri().'/*([^/\?]*).*$`', $_SERVER['REQUEST_URI'], $matches);
        if(!empty($matches[1]))
        {
            // look up the child
            $resource = $this->getChild($matches[1]);
            if($resource)
            {
                return $resource->handleRequest();
            }
            else
            {
                return new botwebapi\JsonHttpResponse(404, $_SERVER['REQUEST_URI'].' does not name a resource');
            }
        }
        
        // if not, we have to handle the request
        switch($_SERVER['REQUEST_METHOD'])
        {
        case 'GET':
            return $this->handleGetRequest();
        case 'POST':
            return $this->handlePostRequest();
        case 'PUT':
            return $this->handlePutRequest();
        case 'DELETE':
            return $this->handleDeleteRequest();
        default:
            return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
        }
    }
    
    protected abstract function handleGetRequest();
    protected abstract function handlePostRequest();
    protected abstract function handlePutRequest();
    protected abstract function handleDeleteRequest();
    
    protected abstract function getChild($resource_name);
}

?>
