<?php

namespace botwebapi\resources\api\projects\karPcompilerProjects\files;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class File extends resources\BotWebApiResource
{
    private $file = NULL;
    
    public function __construct($resource_uri, $file)
    {
        if(!is_readable($file))
        {
            throw new \Exception('Invalid argument $file');
        }
        
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->file = $file;
    }
    
    public function get()
    {
		$content = file_get_contents($this->file);
		if($content)
		{
		    $content_b64 = base64_encode($content);
            return new botwebapi\JsonHttpResponse(200, array('content' => $content_b64,
                                                             'encoding' => 'base64',
                                                             'about' => new botwebapi\AboutObject($this)));
        }
    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
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
