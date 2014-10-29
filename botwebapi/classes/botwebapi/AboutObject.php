<?php

namespace botwebapi;

class AboutObject
{
    public $resource_name = '';
    public $resource_version = '';
    public $resource_homepage = '';
    
    public function __construct($resource)
    {
        $this->resource_name = $resource->getResourceName();
        $this->resource_version = $resource->getResourceVersion();
        $this->resource_homepage = $resource->getResourceHomepage();
    }
}

?>
