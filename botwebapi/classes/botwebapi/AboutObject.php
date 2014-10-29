<?php

namespace botwebapi;

class AboutObject
{
    public $resource_name = '';
    public $resource_version = '';
    public $resource_homepage = '';
    
    public function __construct($resource)
    {
        $this->resource_name = $resource->getName();
        $this->resource_version = $resource->getVersion();
        $this->resource_homepage = $resource->getHomepage();
    }
}

?>
