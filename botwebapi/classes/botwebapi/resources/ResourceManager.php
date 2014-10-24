<?php

namespace botwebapi\resources;

class ResourceManager
{
    private $resources_root_path = NULL;
    
    public function __construct()
    {
        $resources_root_path = __DIR__.'/resources';
    }
    
    public function getResourceByName($urn)
    {
        $namespace = '\\'.str_replace('/', '\\', $urn);
        $name = substr(strrchr($namespace, '\\'), 1);
        
        $resource_class_name = __NAMESPACE__.$namespace.'\\'.ucfirst($name);
        
        $resource_rc = new \ReflectionClass($resource_class_name);
        if(!$resource_rc->implementsInterface('botwebapi\resources\iBotWebApiResource'))
        {
            return NULL;
        }
        
        return new $resource_class_name();
    }
}

?>
