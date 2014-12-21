<?php

namespace botwebapi\resources\api\projects\karPcompilerProjectsWindows;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Project extends resources\BotWebApiResource
{
    private $project_name = '';
    private $archive_location = '';
    private $binary_location = '';
    
    public function __construct($project_name, $resource_uri)
    {
        if(!is_readable(WIN_PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.$project_name))
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
        return WIN_PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.$this->project_name;
    }
    
    public function getBinaryOutputName()
    {
        return $this->project_name.'.exe';
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink(array('path' => $this->getParentUri()),
                                  array('rel' => 'parent'));
        }
        
        $links->addLink(array('path' => WIN_PROJECTS_ROOT_DIR_FS_RESOURCE.'/'.$this->project_name),
                              array('rel' => 'project_location'));
        
        $language = 'Unknown';
        
        $project_files_res = resources\BotWebApiResource::getResource('/'.WIN_PROJECTS_ROOT_DIR_FS_RESOURCE.'/'.$this->project_name);
        if($project_files_res)
        {
            $links->addLink(array('path' => $project_files_res->getResourceUri()),
                                  array('rel' => 'files'));
            
            $resp = $project_files_res->get();
            if($resp->getStatusCode() == 200)
            {
                $content = $resp->getContent();
                
                foreach($content->links->files as $files)
                {
                    preg_match('`^.*\.c$`', $files->name, $matches);
                    if($matches[0])
                    {
                        $language = 'C';
                        break;
                    }
                    preg_match('`^.*\.(cpp|c\+\+)$`', $files->name, $matches);
                    if($matches[0])
                    {
                        $language = 'C++';
                        break;
                    }
                    preg_match('`^.*\.java$`', $files->name, $matches);
                    if($matches[0])
                    {
                        $language = 'Java';
                        break;
                    }
                }
            }
        }
        
        $links->addLink(array('path' => $this->getResourceUri().'/binary'),
                              array('rel' => 'binary'));
        
        return new botwebapi\JsonHttpResponse(200, array('name' => $this->project_name,
                                                         'about' => new botwebapi\AboutObject($this),
                                                         'type' => 'KISS Web IDE Project',
                                                         'language' => $language,
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
