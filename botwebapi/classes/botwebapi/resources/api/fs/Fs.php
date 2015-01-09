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
    
    public function __construct($resource_uri, $parent_resource, $path = NULL)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
        
        if(NULL == $path)
        {
            if(PLATFORM == 'WIN')
            {
                $this->path = 'C:';
            }
            else
            {
                $this->path = '/';
            }
        }
        else
        {
            $this->path = $path;
        }
    }
    
    public function getChildResourceFromFsPath($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        
        $resource_path = $this->path;
        if($resource_path != DIRECTORY_SEPARATOR)
        {
            $resource_path = $resource_path.DIRECTORY_SEPARATOR;
        }
        $resource_path = str_replace(DIRECTORY_SEPARATOR, '/', $resource_path);
        
        preg_match('`^'.$resource_path.'(.*)`', $path, $matches);
        return $this->getChildResource($matches[1]);
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
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
                                                         //  'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
                                                             'directory_separator' => DIRECTORY_SEPARATOR,
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
                                                             //  'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
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
                                                             //  'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
                                                                 'links' => $links));
            }
        }
    }
    
    public function post($content)
    {
        if(is_file($this->path))
        {
            return new botwebapi\JsonHttpResponse(405, 'Method not allowed on a file resource');
        }
        
        $json_data = json_decode($content, true);
        if(!array_key_exists('name', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "name" required');
        }
        
        $path = $this->path.DIRECTORY_SEPARATOR.$json_data["name"];
        if(file_exists($path))
        {
            return new botwebapi\JsonHttpResponse(409, $path.' already exists');
        }
        
        if(array_key_exists('is_dir', $json_data) && $json_data["is_dir"])
        {
            if(mkdir($path))
            {
                $location = botwebapi\LinksObject::buildUri($this->getResourceUri().'/'.urlencode($json_data["name"]));
                return new botwebapi\HttpResponse(201, '', array('Location' => $location));
            }
            else
            {
                return new botwebapi\JsonHttpResponse(500, 'Unable to create directory');
            }
        }
        else
        {
            if(array_key_exists('content', $json_data))
            {
                $file_content = base64_decode($json_data['content']);
                if($file_content === FALSE)
                {
                    return new botwebapi\JsonHttpResponse(415, 'Parameter "content" is not base64 encoded');
                }
            }
            else
            {
                $file_content = NULL;
            }
            
            if(file_put_contents($path, $file_content) === FALSE)
            {
                return new botwebapi\JsonHttpResponse(500, 'Unable to write content');
            }
            
            $location = botwebapi\LinksObject::buildUri($this->getResourceUri().'/'.urlencode($json_data["name"]));
            return new botwebapi\HttpResponse(201, '', array('Location' => $location));
        }
    }
    
    public function put($content)
    {
        if(is_dir($this->path))
        {
            return new botwebapi\JsonHttpResponse(405, 'Method not allowed on a directory resource');
        }
        
        $json_data = json_decode($content, true);
        if(!array_key_exists('content', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "content" required');
        }
        
        if(!is_writable($this->path))
        {
            return new botwebapi\JsonHttpResponse(403, 'Cannot open file for writing');
        }
        
        $file_content = base64_decode($json_data['content']);
        if($file_content === FALSE)
        {
            return new botwebapi\JsonHttpResponse(415, 'Parameter "content" is not base64 encoded');
        }
        
        if(file_put_contents($this->path, $file_content) === FALSE)
        {
            return new botwebapi\JsonHttpResponse(500, 'Unable to write content');
        }
        
	    return new botwebapi\HttpResponse(204);
    }
    
    public function delete($content)
    {
        if(is_dir($this->path))
        {
            if(Fs::delTree($this->path))
            {
                return new botwebapi\HttpResponse(204);
            }
            else
            {
                return new botwebapi\JsonHttpResponse(500, 'Unable to delete '.$this->path);
            }
        }
        else
        {
            if(unlink($this->path))
            {
                return new botwebapi\HttpResponse(204);
            }
            else
            {
                return new botwebapi\JsonHttpResponse(500, 'Unable to delete '.$this->path);
            }
        }
    }
    
    // from http://php.net/manual/de/function.rmdir.php
    private static function delTree($dir)
    { 
        $files = array_diff(scandir($dir), array('.','..')); 
        foreach ($files as $file)
        { 
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
        } 
        return rmdir($dir); 
    }
    
    public function getChild($resource_name)
    {
        $resource_name = urldecode($resource_name);
        
        foreach(glob($this->path.DIRECTORY_SEPARATOR.'*') as $file)
        {
            $file_name = basename($file);
            
            if($file_name == $resource_name)
            {
                // check if we have a file type handler for this file
                foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'fileTypeHandlers'.DIRECTORY_SEPARATOR.'*') as $handler)
                {
                    // the handler-resources are always located in a directory
                    if(is_dir($handler) && !is_link($handler))
                    {
                        $handler_name = basename($handler);
                        $handler_class_name = __NAMESPACE__.'\\fileTypeHandlers\\'.$handler_name.'\\'.ucfirst($handler_name);
                        
                        try
                        {
                            if($handler_class_name::canHandle($file))
                            {
                                return new $handler_class_name($this->getResourceUri().'/'.$resource_name, $parent_resource, $file);
                            }
                        }
                        catch(\Exception $e) { }
                    }
                }
                
                // if not, create a new generic file object
                $fs = new Fs($this->getResourceUri().'/'.$resource_name, $this, $file);
                return $fs;
            }
        }
        
        return NULL;
    }
}
?>
