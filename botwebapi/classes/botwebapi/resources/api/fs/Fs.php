<?php

namespace botwebapi\resources\api\fs;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

/**
 * Projects Resource
 * 
 * Its purpose is to provide access to the file system
 */
class Fs extends resources\BotWebApiResource
{
    private $path = NULL;
    
    public function __construct($resource_uri, $path = NULL)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        
        if(NULL == $path)
        {
            if(PLATFORM == WIN)
            {
                $this->path = $path = 'C:/';
            }
            else
            {
                $this->path = $path = '/';
            }
        }
        else
        {
            $this->path = $path;
        }
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        
        if(is_dir($this->path))
        {
            foreach(glob($this->path.DIRECTORY_SEPARATOR.'*') as $file)
            {
                $file_name = basename($file);
                
                if(is_dir($file))
                {
                    $links->addLink($this->getResourceUri().'/'.urlencode($file_name),
                                    array('rel' => 'directories',
                                             'additional' => array('name' => $file_name)),
                                    false);
                }
                else
                {
                    $links->addLink($this->getResourceUri().'/'.urlencode($file_name),
                                    array('rel' => 'files',
                                             'additional' => array('name' => $file_name)),
                                    false);
                }
            }
            
            return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                                                     'name' => basename($this->path),
                                                                                     'path' =>  str_replace(':/', ':', $this->path),
                                                                                     'type' => filetype($this->path),
                                                                                     'mime_type' => $finfo->file($this->path),
                                                                                     'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
                                                                                     'links' => $links));
        }
        else
        {
            $content = file_get_contents($this->path);
            if($content)
            {
                $content_b64 = base64_encode($content);
                return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                                                         'name' => basename($this->path),
                                                                                         'path' =>  str_replace(':/', ':', $this->path),
                                                                                         'type' => filetype($this->path),
                                                                                         'mime_type' => $finfo->file($this->path),
                                                                                         'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
                                                                                         'links' => $links,
                                                                                         'content' => $content_b64));
            }
            else
            {
                return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                                                         'name' => basename($this->path),
                                                                                         'path' =>  str_replace(':/', ':', $this->path),
                                                                                         'type' => filetype($this->path),
                                                                                         'mime_type' => $finfo->file($this->path),
                                                                                         'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
                                                                                         'links' => $links));
            }
        }
    }
    
    protected function handlePostRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function handlePutRequest()
    {
        if(is_dir($this->path))
        {
            return new botwebapi\JsonHttpResponse(405, 'Method not allowed on a directory resource');
        }
        
        $json_data = json_decode(file_get_contents('php://input'), true);
        if(!array_key_exists('content', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "content" required');
        }
        
        if(!is_writable($this->path))
        {
            return new botwebapi\JsonHttpResponse(403, 'Cannot open file for writing');
        }
        
        $content = base64_decode($json_data['content']);
        if($content === FALSE)
        {
            return new botwebapi\JsonHttpResponse(415, 'Parameter "content" is not base64 encoded');
        }
        
        if(file_put_contents($this->path, $content) === FALSE)
        {
            return new botwebapi\JsonHttpResponse(500, 'Unable to write content');
        }
        
        return new botwebapi\HttpResponse(204);
    }
    
    protected function handleDeleteRequest()
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        $resource_name = urldecode($resource_name);
        
        foreach(glob($this->path.DIRECTORY_SEPARATOR.'*') as $file)
        {
            $file_name = basename($file);
            
            if($file_name == $resource_name)
            {
                $fs = new Fs($this->getResourceUri().'/'.$resource_name, $file);
                return $fs;
            }
        }
        
        return NULL;
    }
}
?>
