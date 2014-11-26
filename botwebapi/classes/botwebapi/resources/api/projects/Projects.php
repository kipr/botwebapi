<?php

namespace botwebapi\resources\api\projects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

/**
 * Projects Resource
 * 
 * Its purpose is to serve as a container for project resources.
 * As project handling is platform-dependent, Projects searches all subdirectoies for project providers
 * Per convention, all project provider classes
 *  *  are named: __NAMESPACE__\<name>\<Name>
 *    => this implies that the class file location will be __DIR__/<name>/<Name>
 *  * have a default constructor.
 *  * implement botwebapi\resources\api\projects\iProjectProvider
 */
class Projects extends resources\BotWebApiResource
{
    private $project_providers = array();
    
    public function __construct($resource_uri)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        
        // register project providers
        foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'*') as $file)
        {
            if(is_dir($file))
            {
                try
                {
                    $project_provider_name = basename($file);
                    $project_provider_class_name
                        = __NAMESPACE__.'\\'.$project_provider_name.'\\'.ucfirst($project_provider_name);
                    $project_provider = new $project_provider_class_name();
                    
                    array_push($this->project_providers, $project_provider);
                }
                catch(\Exception $e) { }
            }
        }
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        foreach($this->project_providers as $project_provider)
        {
            $project_names = $project_provider->getProjectNames();
            foreach($project_names as $project_name)
            {
                $links->addLink($this->getResourceUri().'/'.urlencode($project_name),
                                array('rel' => 'projects',
                                         'additional' => array('name' => $project_name)),
                                false);
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
    
    protected function getChild($resource_name)
    {
        foreach($this->project_providers as $project_provider)
        {
            if($project_provider->containsProject($resource_name))
            {
                return $project_provider->getProjectResource($resource_name, $this->getResourceUri().'/'.$resource_name);
            }
        }
        
        return NULL;
    }
}
?>
