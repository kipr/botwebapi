<?php

namespace botwebapi\resources\api\fs\fileTypeHandlers\karArchive;
use botwebapi\resources\api\fs as fs;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

class KarArchive extends resources\BotWebApiResource
{
    private $path = NULL;
    private $temp_dir = NULL;
    
    public function __construct($resource_uri, $parent_resource, $path)
    {
        $this->path = $path;
        
        // unpack the files in a temporary directory
        $tmp_dir = sys_get_temp_dir().'/kiss_'.uniqid();
        while(file_exists($tmp_dir))
        {
	        $tmp_dir = sys_get_temp_dir().'/kiss_'.uniqid();
        }
        
        if(mkdir($tmp_dir))
        {
            $this->temp_dir = $tmp_dir;
	        $tmp_kar_file = $this->temp_dir.'/'.basename($this->path);
	        
	        if(copy($this->path, $tmp_kar_file.'.kiss'))
	        {
		        exec('kissarchive -e '.$tmp_kar_file.' '.$this->temp_dir.'/');
	        }
        }
        
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
    }
    
    public function __destruct()
    {
        if($this->temp_dir != NULL)
        {
            // repack the archive
            
            // 1. Create kam file
            $kam_file = $this->temp_dir.DIRECTORY_SEPARATOR.'kam.kam';
            $kam_content = '';
            foreach(glob($this->temp_dir.DIRECTORY_SEPARATOR.'*') as $file)
            {
                $file_name = basename($file);
                if(!preg_match('/(.kiss|.kam|package_info)$/', $file))
                {
                    $kam_content = $kam_content.$file.' => '.$file_name.PHP_EOL;
                }
            }
            
            file_put_contents($kam_file, $kam_content);
            
            // 2. package it
	        exec('cd '.$this->temp_dir.' && kissarchive -c '.basename($this->path).' 1 '.$kam_file);
	        
	        // 3. replace existing archive
	        copy($this->temp_dir.DIRECTORY_SEPARATOR.basename($this->path).'-1.kiss',
	             $this->path);
            
            // delete the temp file
            KarArchive::delTree($this->temp_dir);
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
    
    public static function canHandle($file)
    {
        $handle = fopen($file, "rb");
        if($handle)
        {
            $magic = fread($handle, 10);
            fclose($handle);
            
            if(strlen($magic) == 10)
            {
                $magic_words = unpack('c4/S3word', $magic);
                if($magic_words['word1'] == 0x616b &&
                   $magic_words['word2'] == 0x6b72 &&
                   $magic_words['word3'] == 0x7261)
                {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
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
        
        foreach(glob($this->temp_dir.DIRECTORY_SEPARATOR.'*') as $file)
        {
            $file_name = basename($file);
            if(preg_match('/(.kiss|.kam|package_info)$/', $file))
            {
                continue;
            }
            
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
                                                         'type' => 'archive',
                                                         'mime_type' => 'application/vnd.KIPR.KAR',
                                                     //  'last_modified' => date ("F d Y H:i:s.", filemtime($this->path)),
                                                         'directory_separator' => DIRECTORY_SEPARATOR,
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        $json_data = json_decode($content, true);
        if(!array_key_exists('name', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "name" required');
        }
        
        $path = $this->temp_dir.DIRECTORY_SEPARATOR.$json_data["name"];
        if(file_exists($path))
        {
            return new botwebapi\JsonHttpResponse(409, $path.' already exists');
        }
        
        if(array_key_exists('is_dir', $json_data) && $json_data["is_dir"])
        {
            if(mkdir($path))
            {
                return new botwebapi\HttpResponse(201, '', array('Location' => $this->getResourceUri().'/'.urlencode($json_data["name"])));
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
            
            return new botwebapi\HttpResponse(201, '', array('Location' => $this->getResourceUri().'/'.urlencode($json_data["name"])));
        }
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, 'Method not allowed on a KAR archive resource');
    }
    
    public function delete($content)
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
    
    public function getChild($resource_name)
    {
        $resource_name = urldecode($resource_name);
        
        foreach(glob($this->temp_dir.DIRECTORY_SEPARATOR.'*') as $file)
        {
            $file_name = basename($file);
            
            if($file_name == $resource_name)
            {
                $fs = new fs\Fs($this->getResourceUri().'/'.$resource_name, $this, $file);
                return $fs;
            }
        }
        
        return NULL;
    }
}
?>
