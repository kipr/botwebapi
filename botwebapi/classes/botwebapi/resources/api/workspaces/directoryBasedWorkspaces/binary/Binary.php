<?php

namespace botwebapi\resources\api\workspaces\directoryBasedWorkspaces\binary;
use botwebapi\resources\api\workspaces\directoryBasedWorkspaces as directoryBasedWorkspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Binary extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    
    public function __construct($resource_uri, directoryBasedWorkspaces\Project $project_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->project_resource = $project_resource;
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        $links->addLink(array('path' => $this->project_resource->getResourceUri()),
                        array('rel' => 'project',
                              'additional' => array('name' => $this->project_resource->getProjectName())));
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
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
        
        if(PLATFORM == 'WIN')
        {
            $command = CMD_PATH.' /c '.COMPILE_HELPER_PATH.' '
                .$this->project_resource->getBinaryOutputDirectory().DIRECTORY_SEPARATOR.$this->project_resource->getBinaryOutputName()
                .' '.$sourceFile;
        }
        
        exec($command, $output);
        
        return new botwebapi\JsonHttpResponse(200, array('output' => array_slice($output, 2)));
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        return NULL;
    }
}

?>
