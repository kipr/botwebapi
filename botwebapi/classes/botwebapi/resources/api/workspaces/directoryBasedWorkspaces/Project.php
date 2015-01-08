<?php

namespace botwebapi\resources\api\workspaces\directoryBasedWorkspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Project extends resources\BotWebApiResource
{
    private $project_name = '';
    private $project_path = '';
    
    public function __construct($project_name, $project_path, $resource_uri, $parent_resource)
    {
        if(!is_readable($project_path))
        {
            throw new \Exception('Invalid argument: $project_path');
        }
        
        $this->project_name = $project_name;
        $this->project_path = $project_path;
        
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
    }
    
    public function getProjectName()
    {
        return $this->project_name;
    }
    
    public function getBinaryOutputDirectory()
    {
        return $this->project_path;
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
        
        $language = 'Unknown';
        
        $root_resource = resources\BotWebApiResource::getRootResource();
        if($root_resource)
        {
            $fs_root_resource = $root_resource->getChildResource('/fs');
            if($fs_root_resource)
            {
                $project_fs_resource = $fs_root_resource->getChildResourceFromFsPath($this->project_path);
                if($project_fs_resource)
                {
                    $links->addLink(array('path' => $project_fs_resource->getResourceUri()),
                                          array('rel' => 'project_location'));
                    
                    $links->addLink(array('path' => $project_fs_resource->getResourceUri()),
                                          array('rel' => 'files'));
                    
                    $resp = $project_fs_resource->get();
                    if($resp->getStatusCode() == 200)
                    {
                        $content = $resp->getContent();
                        
                        if(property_exists($content->links, 'files'))
                        {
                            foreach($content->links->files as $files)
                            {
                                preg_match('`^.*\.c$`', $files->name, $matches);
                                if(array_key_exists(0, $matches))
                                {
                                    $language = 'C';
                                    break;
                                }
                                preg_match('`^.*\.(cpp|c\+\+)$`', $files->name, $matches);
                                if(array_key_exists(0, $matches))
                                {
                                    $language = 'C++';
                                    break;
                                }
                                preg_match('`^.*\.java$`', $files->name, $matches);
                                if(array_key_exists(0, $matches))
                                {
                                    $language = 'Java';
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $links->addLink(array('path' => $this->getResourceUri().'/binary'),
                              array('rel' => 'binary'));
        
        return new botwebapi\JsonHttpResponse(200, array('name' => $this->project_name,
                                                         'about' => new botwebapi\AboutObject($this),
                                                         'type' => 'Directory-based workspace project',
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
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
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
