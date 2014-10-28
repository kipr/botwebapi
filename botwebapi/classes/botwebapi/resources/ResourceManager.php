<?php

namespace botwebapi\resources;

class ResourceManager
{
    public static function getInstance()
    {
        static $instance = NULL;
        if(!$instance)
        {
            $instance = new static();
        }
        
        return $instance;
    }
    
    private $resources = array();
    
    private function registerResources($uri_prefix, $resource_path)
    {
        // a resource has the following name schema: <name>/<Name>.php
        $resource_name = basename($resource_path);
        $resource_uri = $uri_prefix.'/'.$resource_name;
        $resource_class_file_path = $resource_path.DIRECTORY_SEPARATOR.ucfirst($resource_name).'.php';
        
        if(is_readable($resource_class_file_path))
        {
            // check if we can load it. The class name is <uri_prefix>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.str_replace('/', '\\', $resource_uri).'\\'.ucfirst($resource_name);
            $resource = new $resource_class_name();
            
            // and it needs to implement iBotWebApiResource
            $resource_rc = new \ReflectionClass($resource);
            if($resource_rc->implementsInterface('botwebapi\resources\iBotWebApiResource'))
            {
                $this->resources[$resource_uri] = $resource;
            }
        }
        
        foreach (glob($resource_path.'/*') as $file)
        {
            // a resource is always located in a directory
            if(is_dir($file) && !is_link($file))
            {
                $this->registerResources($resource_uri, $file);
            }
        }
    }
    
    protected function __construct()
    {
        $resources_root_path = __DIR__;
        
        foreach (glob($resources_root_path.'/*') as $file)
        {
            // a resource is always located in a directory
            if(is_dir($file) && !is_link($file))
            {
                $this->registerResources('', $file);
            }
        }
    }
    
    public function getResourceByUri($resource_uri)
    {
        return $this->resources[$resource_uri];
    }
    
    public function getResources()
    {
        return $this->resources;
    }
}

?>
