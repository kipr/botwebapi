<?php

namespace botwebapi\resources\api\projects\karPcompilerProjectsWindows\binary;
use botwebapi\resources\api\projects\karPcompilerProjectsWindows as karPcompilerProjectsWindows;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Binary extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    
    public function __construct($resource_uri, karPcompilerProjectsWindows\Project $project_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->project_resource = $project_resource;
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink(array('path' => $this->project_resource->getResourceUri()),
                        array('rel' => 'project',
                              'additional' => array('name' => $this->project_resource->getProjectName())));
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    protected function handlePostRequest()
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function handlePutRequest()
    {
        $json_data = json_decode(file_get_contents('php://input'), true);
        if(!array_key_exists('sourceFile', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "sourceFile" required');
        }
        
        $sourceFile = $json_data['sourceFile'];
        if(!is_readable($sourceFile))
        {
            return new botwebapi\JsonHttpResponse(415, 'Parameter "sourceFile" is not readable');
        }
        
        /*
        if(!is_writable($this->project_resource->getBinaryOutputDirectory().'/'.$this->project_resource->getBinaryOutputName()))
        {
            return new botwebapi\JsonHttpResponse(403, 'Cannot open file for writing');
        }
        */
        
        $output = array();
        
        $command = 'c:\\windows\\system32\\cmd.exe /c C:\Users\stefa_000\Documents\Projects\botwebapi\INSTALL\Windows\compile.bat '
            .$this->project_resource->getBinaryOutputDirectory().DIRECTORY_SEPARATOR.$this->project_resource->getBinaryOutputName()
            .' '.$sourceFile;
        
        exec($command, $output);
        
        return new botwebapi\JsonHttpResponse(200, array('output' => array_slice($output, 2)));
    }
    
    protected function handleDeleteRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        return NULL;
    }
}

?>
