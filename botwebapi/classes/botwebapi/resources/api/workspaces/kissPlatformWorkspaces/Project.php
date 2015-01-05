<?php

namespace botwebapi\resources\api\projects\karPcompilerProjects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

// Set constants
define('ARCHIVES_ROOT_DIR', '/kovan/archives');
define('BINARIES_ROOT_DIR', '/kovan/bin');

class Project extends resources\BotWebApiResource
{
    private $project_name = '';
    private $archive_location = '';
    private $binary_location = '';
    
    public function __construct($project_name, $resource_uri)
    {
        if(!is_readable(ARCHIVES_ROOT_DIR.DIRECTORY_SEPARATOR.$project_name))
        {
            throw new \Exception('Invalid argument: $project_name');
        }
        
        $this->project_name = $project_name;
        $this->archive_location = ARCHIVES_ROOT_DIR.DIRECTORY_SEPARATOR.$project_name;
        $this->binary_location = BINARIES_ROOT_DIR.DIRECTORY_SEPARATOR.$project_name;
        
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    public function getProjectName()
    {
        return $this->project_name;
    }
    
    public function getBinaryLocation()
    {
        return $this->binary_location;
    }
    
    public function getArchiveLocation()
    {
        return $this->archive_location;
    }
    
    protected function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink(array('scheme' => 'sftp', 'path' => $this->archive_location),
                        array('rel' => 'project_location', 'additional' => array('type' => 'application/vnd.KIPR.kar; charset=binary')));
        
        foreach (glob(__DIR__.'/*') as $file)
        {
            // the child-resources of <project> are always located in a directory
            if(is_dir($file) && !is_link($file))
            {
                $child_resource_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($child_resource_name),
                                array('rel' => $child_resource_name));
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('name' => $this->project_name,
                                                         'about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function delete($content)
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