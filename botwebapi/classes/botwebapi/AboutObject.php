<?php

namespace botwebapi;

class AboutObject
{
    public $name = '';
    public $version = '';
    public $homepage = '';
    
    public function __construct($resource)
    {
        $this->name = $resource->getName();
        $this->version = $resource->getVersion();
        $this->homepage = $resource->getHomepage();
    }
}

?>
