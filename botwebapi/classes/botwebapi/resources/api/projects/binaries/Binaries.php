<?php

namespace botwebapi\resources\api\projects\binaries;
use botwebapi\resources\api\projects as projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Binaries extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    private $binary_location = '';
    
    public function __construct($resource_name, $resource_uri, projects\Project $project_resource)
    {
        parent::__construct($resource_name, $resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->project_resource = $project_resource;
        $this->binary_location = BINARIES_ROOT_DIR.DIRECTORY_SEPARATOR.$project_resource->getProjectName();
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink($this->project_resource->getResourceUri(), array('rel' => 'project'));
        $links->addLink(array('scheme' => 'sftp', 'path' => $this->binary_location),
                        array('rel' => 'binary_output_folder', 'additional' => array('type' => 'directory')));
        
        foreach (glob($this->binary_location.'/*') as $file)
        {
            $finfo = new \finfo(FILEINFO_MIME);
            $file_name = basename($file);
            if($finfo->file($file) == 'application/x-executable; charset=binary')
            {
                $links->addLink($this->getResourceUri().'/'.urlencode($file_name),
                                array('rel' => 'binaries'),
                                false);
            }
            $links->addLink(array('scheme' => 'sftp', 'path' => $file),
                            array('rel' => 'all_files', 'additional' => array('type' => $finfo->file($file))),
                            false);
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
            $resource_class_name = __NAMESPACE__.'\\Binary';
            return new $resource_class_name($resource_name, $this->getResourceUri().'/'.$resource_name, $this->project_resource);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>