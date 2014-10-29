<?php

namespace botwebapi;

class AboutObject
{
    public $resource_class = '';
    public $resource_version = '';
    public $resource_homepage = '';
    
    public function __construct($resource)
    {
        $this->resource_class = get_class($resource);
        $this->resource_version = $resource->getResourceVersion();
        $this->resource_homepage = $resource->getResourceHomepage();
    }
}

?>
