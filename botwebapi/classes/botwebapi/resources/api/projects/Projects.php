<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Projects extends resources\BotWebApiResource
{
    const PROJECTS_ROOT_DIR = '/kovan/archives';
    
    public function __construct($name, $uri)
    {
        parent::__construct($name, $uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getUri(), 'self');
        
        foreach (glob(Projects::PROJECTS_ROOT_DIR.'/*') as $file)
        {
            // Link projects are compressed and stored in a single archive
            if(!is_dir($file))
            {
                $project_name = basename($file);
                $links->addLink($this->getUri().'/'.$project_name, 'projects', false);
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
    
    protected function getChild($name)
    {
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\Project';
            return new $resource_class_name($name,
                                            $this->getUri().'/'.$name,
                                            Projects::PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.$name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}
?>
