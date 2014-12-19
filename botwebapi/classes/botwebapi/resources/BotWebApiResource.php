<?php

namespace botwebapi\resources;
use botwebapi as botwebapi;

abstract class BotWebApiResource
{
    
    public static function getRootResource()
    {
        return new api\Api(ROOT_RESOURCE_URI_PATH);
    }
    
    public static function getResource($uri)
    {
        $root_resource = BotWebApiResource::getRootResource();
        preg_match('`^/*'.$root_resource->getResourceUri().'/*([^\?]*).*$`', $uri, $matches);
        return $root_resource->getChildResource($matches[1]);
    }
    
    private $resource_uri = '';
    private $resource_version = '';
    private $resource_homepage = '';
    private $resource_name = '';
    private $parent_uri = '';
    
    public function __construct($resource_uri, $resource_version, $resource_homepage)
    {
        $this->resource_uri = $resource_uri;
        $this->resource_version = $resource_version;
        $this->resource_homepage = $resource_homepage;
        
        $name_start = strrpos($this->resource_uri, '/') + 1;
        $name_end = strrpos($this->resource_uri, '?', $name_start);
        if($name_end === false)
        {
            $this->resource_name = substr($this->resource_uri, $name_start);
        }
        else
        {
            $this->resource_name = substr($this->resource_uri, $name_start, $name_end - $name_start);
        }
        $parent_uri = substr($this->resource_uri, 0, $name_start-1);
        $this->parent_uri = $parent_uri ? $parent_uri : '';
    }
    
    public function getName()
    {
        return $this->resource_name;
    }
    
    public function getParentUri()
    {
        return $this->parent_uri;
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
    
    public function getChildResource($rel_uri)
    {
        preg_match('`^/*([^/\?]*)/*([^\?]*).*$`', $rel_uri, $matches);
        if($matches[1])
        {
            // look up the child
            $child_resource = $this->getChild($matches[1]);
            if($child_resource)
            {
                return $child_resource->getChildResource($matches[2]);
            }
            else
            {
                return NULL;
            }
        }
        return $this;
    }
    
    public function handleRequest($method, $content)
    {
        switch($method)
        {
        case 'GET':
            return $this->get();
        case 'POST':
            return $this->post($content);
        case 'PUT':
            return $this->put($content);
        case 'DELETE':
            return $this->delete($content);
        default:
            return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
        }
    }
    
    public abstract function get();
    public abstract function post($content);
    public abstract function put($content);
    public abstract function delete($content);
    
    protected abstract function getChild($resource_name);
}

?>
