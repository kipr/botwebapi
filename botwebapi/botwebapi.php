<?php

namespace botwebapi;

// Set constants
define('API_ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
if(!is_dir(API_ROOT_PATH))
{
    JsonHttpResponse::sendServerError(500, 'Internal error: $_SERVER["SCRIPT_FILENAME"]) is invalid! ('.$_SERVER['SCRIPT_FILENAME'].')');
    exit();
}

define('INCLUDE_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'includes');
if(!is_dir(INCLUDE_PATH))
{
    JsonHttpResponse::sendServerError(500, 'Internal error: INCLUDE_PATH does not name a directory! ('.INCLUDE_PATH.')');
    exit();
}

define('CLASS_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'classes');
if(!is_dir(CLASS_PATH))
{
    JsonHttpResponse::sendServerError(500, 'Internal error: CLASS_PATH does not name a directory! ('.CLASS_PATH.')');
    exit();
}

// include files
require INCLUDE_PATH.DIRECTORY_SEPARATOR.'autoload.php';

// figure out if this request is valid and who has to handle it
preg_match('@^/([^/\?]+)/?([^/\?]*).*$@', $_SERVER['REQUEST_URI'], $matches);
if(sizeof($matches) != 3 || empty($matches[0]))
{
    JsonHttpResponse::sendServerError(500, array('Internal error: Unexpected URI' => $matches));
    exit();
}

if(empty($matches[2]))
{
    $resource_uri = '/'.$matches[1];
}
else
{
    $resource_uri = '/'.$matches[1].'/'.$matches[2];
}

// create the resource manager
$resource_manager = resources\ResourceManager::getInstance();
try
{
    $resource = $resource_manager->getResourceByUri($resource_uri);
}
catch(\Exception $e)
{
    JsonHttpResponse::sendClientError(404, array('message' => $resource_uri.' does not name a resource',
                                                 'exception' => $e->getMessage()));
    exit();
}

if(!$resource)
{
    JsonHttpResponse::sendClientError(404, $resource_uri.' does not name a resource');
    exit();
}
    
$resource->handleRequest();

?>
