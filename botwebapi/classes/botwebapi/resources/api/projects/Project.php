<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Project extends resources\BotWebApiResource
{
    private $location = '';
    
    public function __construct($name, $uri, $location)
    {
        if(!is_readable($location))
        {
            throw new \Exception('Invalid argument: $location');
        }
        
        parent::__construct($name, $uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->location = $location;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getUri(), 'self');
        
        foreach (glob(__DIR__.'/*') as $file)
        {
            // the child-resources of <project> are always located in a directory
            if(is_dir($file) && !is_link($file))
            {
                $child_resource_name = basename($file);
                $links->addLink($this->getUri().'/'.$child_resource_name, 'child-resources', false);
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
        if(unlink($this->location))
        {
            return new botwebapi\JsonHttpResponse(204, '');
        }
        else
        {
            return new botwebapi\JsonHttpResponse(404, 'Could not delete '.$this->location);
        }
    }
    
    protected function getChild($name)
    {
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\'.$name.'\\'.ucfirst($name);
            return new $resource_class_name($name, $this->getUri().'/'.$name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
