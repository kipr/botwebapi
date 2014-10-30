<?php

namespace botwebapi\resources\api;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

/**
 * Bot Web API Root Resource
 * 
 * Its purpose is to serve as a container for other resources.
 * Per convention, all child resource classes
 *  * are named: __NAMESPACE__\<name>\<Name>
 *    => this implies that the class file location will be __DIR__/<name>/<Name>
 *  * have a __construct($resource_uri) constructor where $resource_uri is the URI of the newly created resource.
 * 
 */
class Api extends resources\BotWebApiResource
{
    public function __construct($resource_uri)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'*') as $file)
        {
            if(is_dir($file))
            {
                $child_resource_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($child_resource_name),
                                array('rel' => $child_resource_name));
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
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
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function getChild($resource_name)
    {
        try
        {
            $resource_class_name = __NAMESPACE__.'\\'.$resource_name.'\\'.ucfirst($resource_name);
            return new $resource_class_name($this->getResourceUri().'/'.$resource_name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
