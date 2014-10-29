<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class ProjectDescriptor
{
    public $name;
    public $location;
    public $uri;
    public $about;
    
    public function __construct($name, $uri, $location, $about)
    {
        $this->name = $name;
        $this->uri = $uri;
        $this->location = $location;
        $this->about = $about;
    }
}

class Projects extends resources\BotWebApiResource
{
    const PROJECTS_ROOT_DIR = '/kovan/archives';
    
    public function __construct($uri)
    {
        parent::__construct('projects', $uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    protected function handleGetRequest()
    {
        // returns a list of all child resources
        $project_descriptors = array();
        foreach (glob(Projects::PROJECTS_ROOT_DIR.'/*') as $file)
        {
            // Link projects are compressed and stored in a single archive
            if(!is_dir($file))
            {
                // create the resource
                $resource_name = basename($file);
                $resource = $this->getChild($resource_name);
                
                // create the resource descriptor
                $project_descriptor = new ProjectDescriptor($resource->getName(),
                                                            $resource->getUri(),
                                                            $resource->getLocation(),
                                                            array('version' => $resource->getVersion(),
                                                                  'homepage' => $resource->getHomepage()));
                array_push($project_descriptors, $project_descriptor);
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('projects' => $project_descriptors));
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
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
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
