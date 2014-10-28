<?php

namespace botwebapi\resources;
use botwebapi as botwebapi;

abstract class BotWebApiResource
{
    private $name = '';
    private $uri = '';
    private $version = '';
    private $homepage = '';
    
    public function __construct($name, $uri, $version, $homepage)
    {
        $this->name = $name;
        $this->uri = $uri;
        $this->version = $version;
        $this->homepage = $homepage;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getUri()
    {
        return $this->uri;
    }
    
    public function getVersion()
    {
        return $this->version;
    }
    
    public function getHomepage()
    {
        return $this->homepage;
    }
    
    public function handleRequest()
    {
        // check if a child addressed
        preg_match('`^'.$this->getUri().'/*([^/\?]*).*$`', $_SERVER['REQUEST_URI'], $matches);
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
    
    protected abstract function getChild($name);
}

?>
