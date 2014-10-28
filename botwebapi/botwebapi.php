<?php

namespace botwebapi;

// Set constants
define('API_ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
define('INCLUDE_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'includes');
define('CLASS_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'classes');

// Create and send response helpers
function sendHttpResponseAndExit($status_code, $content)
{
    $response = new JsonHttpResponse($status_code, $content);
    HttpResponse::sendHttpResponseAndExit($response);
}

// Caught all uncaught exceptions
function exception_handler($e)
{
    sendHttpResponseAndExit(500, array('message' => 'Uncaught exception', 'exception' => $e->getMessage()));
}
set_exception_handler('botwebapi\exception_handler');

// include files
require INCLUDE_PATH.DIRECTORY_SEPARATOR.'autoload.php';

// Authenticate the user
DigestHttpAuthentication::authenticate();

// figure out if this request is valid and who has to handle it
preg_match('@^/([^/\?]+)/?([^/\?]*).*$@', $_SERVER['REQUEST_URI'], $matches);
if(sizeof($matches) != 3 || empty($matches[0]))
{
    sendHttpResponseAndExit(500, 'Unexpected URI "'.$matches.'"');
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
    sendHttpResponseAndExit(404, array('message' => $resource_uri.' does not name a resource',
                                       'exception' => $e->getMessage()));
}

if(!$resource)
{
    sendHttpResponseAndExit(404, $resource_uri.' does not name a resource');
}
    
$response = $resource->handleRequest();
if(!$response)
{
    sendHttpResponseAndExit(500, 'No response from resource "'.$resource->getName().'"');
}

HttpResponse::sendHttpResponseAndExit($response);

?>
