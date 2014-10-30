<?php

namespace botwebapi\resources\api\projects\karPcompilerProjects\files;
use botwebapi\resources\api\projects\karPcompilerProjects as karPcompilerProjects;
use botwebapi\resources as resources;
use botwebapi as botwebapi;


class Files extends resources\BotWebApiResource
{
    private $project_resource = NULL;
    private $temp_dir = NULL;
    
    public function __construct($resource_uri, karPcompilerProjects\Project $project_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi');
        $this->project_resource = $project_resource;
        
        // unpack the files in a temporary directory
        $tmp_dir = sys_get_temp_dir().'/kiss_'.uniqid();
        while(file_exists($tmp_dir))
        {
	        $tmp_dir = sys_get_temp_dir().'/kiss_'.uniqid();
        }
        
        if(mkdir($tmp_dir))
        {
            $this->temp_dir = $tmp_dir;
	        $tmp_kar_file = $this->temp_dir.'/'.$this->project_resource->getProjectName();
	        
	        if(copy($this->project_resource->getArchiveLocation(), $tmp_kar_file.'.kiss'))
	        {
		        exec('kissarchive -e '.$tmp_kar_file.' '.$this->temp_dir.'/');
	        }
        }
    }
    
    public function __destruct()
    {
        if($this->temp_dir != NULL)
        {
            rrmdir($this->temp_dir);
        }
    }
    
    protected function handleGetRequest()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        $finfo = new \finfo(FILEINFO_MIME);
        foreach (glob($this->temp_dir.'/*') as $file)
        {
            $file_name = basename($file);
            if($file_name !== $this->project_resource->getProjectName().'.kiss')
            {
                $links->addLink($this->getResourceUri().'/'.urlencode($file_name),
                                array('rel' => files,
                                      'additional' => array('type' => $finfo->file($file))),
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
        try
        {
            // Load the resource. The class name is <this namespace>\<name>\<Name>
            $resource_class_name = __NAMESPACE__.'\\File';
            return new $resource_class_name($this->getResourceUri().'/'.$resource_name,
                                            $this->temp_dir.DIRECTORY_SEPARATOR.$resource_name);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>
