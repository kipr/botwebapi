<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

// Set constants
define('ARCHIVES_ROOT_DIR', '/kovan/archives');
define('BINARIES_ROOT_DIR', '/kovan/bin');

class Projects extends resources\BotWebApiResource
{
    public function __construct($resource_uri)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        foreach (glob(ARCHIVES_ROOT_DIR.'/*') as $file)
        {
            // Link projects are compressed and stored in a single archive
            if(!is_dir($file))
            {
                $project_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($project_name),
                                array('rel' => 'projects'),
                                false);
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    protected function handlePostRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function handlePutRequest()
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function handleDeleteRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\Project';
            return new $resource_class_name($this->getResourceUri().'/'.$resource_name, $resource_name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}
?>
