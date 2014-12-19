<?php

namespace botwebapi\resources\api\projects\karPcompilerProjectsOSX\binary;
use botwebapi\resources\api\projects\karPcompilerProjectsOSX as karPcompilerProjectsOSX;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class Binary extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    
    public function __construct($resource_uri, karPcompilerProjectsOSX\Project $project_resource)
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
        
        $command = 'gcc -std=c99 -Wall -I"/Applications/KISS Platform 5.1.2 (10.9)/KISS.app/cs2.app/Contents/prefix/usr/include" -include stdio.h -include kovan/kovan.h -L"/Applications/KISS Platform 5.1.2 (10.9)/KISS.app/cs2.app/Contents/prefix/usr/lib" -lkovan -o '
            .$this->project_resource->getBinaryOutputDirectory().DIRECTORY_SEPARATOR.$this->project_resource->getBinaryOutputName()
            .' '.$sourceFile.' 2>&1';

        exec($command, $output);

        return new botwebapi\JsonHttpResponse(200, array('output' => $output));
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
