<?php

namespace botwebapi\resources\api\projects\karPcompilerProjectsWindows;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

// Set constants
define('PROJECTS_ROOT_DIR','C:\Users\stefa_000\Documents\Projects\KISS-Projects');
define('PROJECTS_ROOT_DIR_FS_RESOURCE','api/fs/Users/stefa_000/Documents/Projects/KISS-Projects');

class Project extends resources\BotWebApiResource
{
    private $project_name = '';
    private $archive_location = '';
    private $binary_location = '';
    
    public function __construct($project_name, $resource_uri)
    {
        if(!is_readable(PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.$project_name))
        {
            throw new \Exception('Invalid argument: $project_name');
        }
        
        $this->project_name = $project_name;
        
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    public function getProjectName()
    {
        return $this->project_name;
    }
    
    public function getBinaryOutputDirectory()
    {
        return PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.$this->project_name;
    }
    
    public function getBinaryOutputName()
    {
        return $this->project_name.'.exe';
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink(array('path' => PROJECTS_ROOT_DIR_FS_RESOURCE.'/'.$this->project_name),
                             array('rel' => 'project_location'));
        $links->addLink(array('path' => PROJECTS_ROOT_DIR_FS_RESOURCE.'/'.$this->project_name),
                             array('rel' => 'files'));
        $links->addLink(array('path' => $this->getResourceUri().'/binary'),
                             array('rel' => 'binary'));
        
        return new botwebapi\JsonHttpResponse(200, array('name' => $this->project_name,
                                                         'about' => new botwebapi\AboutObject($this),
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
        if(unlink($this->archive_location))
        {
            return new botwebapi\JsonHttpResponse(204, '');
        }
        else
        {
            return new botwebapi\JsonHttpResponse(500, 'Could not delete '.$this->location);
        }
    }
    
    protected function getChild($resource_name)
    {
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\'.$resource_name.'\\'.ucfirst($resource_name);
            return new $resource_class_name($this->getResourceUri().'/'.$resource_name, $this);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
