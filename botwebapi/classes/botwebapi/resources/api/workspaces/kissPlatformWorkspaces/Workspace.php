<?php

namespace botwebapi\resources\api\workspaces\kissPlatformWorkspaces;
use botwebapi\resources\api\workspaces as workspaces;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

// Set constants
define('ARCHIVES_ROOT_DIR', '/kovan/archives');
define('BINARIES_ROOT_DIR', '/kovan/bin');

class Workspace extends resources\BotWebApiResource
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
        
        foreach (glob(ARCHIVES_ROOT_DIR.DIRECTORY_SEPARATOR.'*') as $file)
        {
            // Link projects are compressed and stored in a single archive
            if(!is_dir($file))
            {
                $project_name = basename($file);
                $links->addLink($this->getResourceUri().'/'.urlencode($project_name),
                                array('rel' => 'projects',
                                      'additional' => array('name' => $project_name)),
                                false);
            }
        }
        
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'name' => 'Kiss Platform Workspace',
                                                         'links' => $links));
    }
    
    public function post($content)
    {
        $json_data = json_decode($content, true);
        if(!array_key_exists('name', $json_data))
        {
            return new botwebapi\JsonHttpResponse(422, 'Parameter "name" required');
        }
        $new_project_name = $json_data["name"];
        
        // check for duplicates
        foreach(glob(ARCHIVES_ROOT_DIR.DIRECTORY_SEPARATOR.'*') as $file)
        {
            // Link projects are compressed and stored in a single archive
            if(!is_dir($file))
            {
                $project_name = basename($file);
                if($project_name == $new_project_name)
                {
                    return new botwebapi\JsonHttpResponse(409, $new_project_name.' already exists');
                }
            }
        }
        
        // create a temporary directory
        $temp_dir = sys_get_temp_dir().'/kiss_'.uniqid();
        while(file_exists($temp_dir))
        {
	        $temp_dir = sys_get_temp_dir().'/kiss_'.uniqid();
        }
        
        if(mkdir($temp_dir))
        {
	        $temp_kar_file = $this->temp_dir.'/'.basename($this->path);
	        
	        // create ops file
	        $ops_file = $temp_dir.DIRECTORY_SEPARATOR.$new_project_name.'.ops';
	        $ops_content = '';
	        file_put_contents($ops_file, $ops_content);
	        
	        // create kam file
	        $kam_file = $temp_dir.DIRECTORY_SEPARATOR.$new_project_name.'.kam';
	        $kam_content = $ops_file.' => '.$new_project_name.'.ops'.PHP_EOL;
	        file_put_contents($kam_file, $kam_content);
	        
            // package it
	        exec('cd '.$temp_dir.' && kissarchive -c '.$new_project_name.' 1 '.$kam_file);
	        
	        // copy it into the archives folder
	        copy($temp_dir.DIRECTORY_SEPARATOR.$new_project_name.'-1.kiss', ARCHIVES_ROOT_DIR.DIRECTORY_SEPARATOR.$new_project_name);
            
            // delete the temp file
            Workspace::delTree($temp_dir);
            
            $location = botwebapi\LinksObject::buildUri($this->getResourceUri().'/'.urlencode($new_project_name));
            return new botwebapi\HttpResponse(201, '', array('Location' => $location));
        } else {
            return new botwebapi\JsonHttpResponse(500, 'Unable to create temporary directory');
        }
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(501, 'Not implemented yet');
    }
    
    protected function getChild($resource_name)
    {
        try
        {
            $resource_class_name = __NAMESPACE__.'\\Project';
            return new $resource_class_name($resource_name, $this->getResourceUri().'/'.$resource_name, $this);
        }
        catch(\Exception $e)
        {
            return NULL;
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
}

?>
