<?php

namespace botwebapi\resources\api\projects\karPcompilerProjectsOSX;
use botwebapi\resources\api\projects as projects;
use botwebapi as botwebapi;

// Set constants
define('OSX_PROJECTS_ROOT_DIR','/Users/stefan/Documents/KISSProjects');

class KarPcompilerProjectsOSX implements projects\iProjectProvider
{
    public function getProjectNames()
    {
        $project_names = array();

        foreach (glob(OSX_PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.'*') as $file)
        {
            // OSX projects are stored in a directory
            if(is_dir($file))
            {
                $project_name = basename($file);
                array_push($project_names, $project_name);
            }
        }
        
        return $project_names;
    }
    
    public function containsProject($project_name)
    {
        return is_readable(OSX_PROJECTS_ROOT_DIR.DIRECTORY_SEPARATOR.$project_name);
    }
    
    public function getProjectResource($project_name, $resource_uri)
    {
        try
        {
            $resource_class_name = __NAMESPACE__.'\\Project';
            return new $resource_class_name($project_name, $resource_uri);
        }
        catch(\Exception $e)
        {
            return NULL;
        }
    }
}

?>