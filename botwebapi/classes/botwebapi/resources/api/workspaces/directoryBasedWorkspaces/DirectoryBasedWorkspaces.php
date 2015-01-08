<?php

namespace botwebapi\resources\api\workspaces\directoryBasedWorkspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class DirectoryBasedWorkspaces extends resources\BotWebApiResource
{
    public function __construct($resource_uri, $parent_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
        // we need a DB for this...
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        $json_data = json_decode($content, true);
        if(!$json_data)
        {
            return new botwebapi\JsonHttpResponse(400, 'Could not parse the content ('.json_last_error().')');
        }
        if(!array_key_exists('path', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "path" required');
        }
        
        $path = $json_data["path"];
        if(!file_exists($path))
        {
            return new botwebapi\JsonHttpResponse(404, $path.' does not exist');
        }
        
        $enc_path = base64_encode($path);
        $location = botwebapi\LinksObject::buildUri($this->getResourceUri().'/'.urlencode($enc_path));
        return new botwebapi\HttpResponse(201, '', array('Location' => $location));
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    protected function getChild($resource_name)
    {
        try
        {
            $workspace_path = base64_decode(urldecode($resource_name));
            $resource_class_name = __NAMESPACE__.'\\Workspace';
            return new $resource_class_name($workspace_path, $this->getResourceUri().'/'.$resource_name, $this);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
