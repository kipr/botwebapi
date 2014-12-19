<?php

namespace botwebapi;

// Set constants
define('API_ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
define('INCLUDE_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'includes');
define('CLASS_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'classes');
if(defined('PHP_WINDOWS_VERSION_MAJOR'))
{
    define('PLATFORM', 'WIN');
}
else
{
    define('PLATFORM', 'LINK');
}
define('ROOT_RESOURCE_URI_PATH', '/api');

// respond to preflights
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
    // return only the headers and not the content
    // only allow CORS if we're doing a GET - i.e. no saving for now.
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
    exit;
}

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
require INCLUDE_PATH.DIRECTORY_SEPARATOR.'file_system_helper.php';

// Authenticate the user
DigestHttpAuthentication::authenticate();

// Get the resource and handle the request
$resource = resources\BotWebApiResource::getResource($_SERVER['REQUEST_URI']);
if($resource)
{
    $response = $resource->handleRequest($_SERVER['REQUEST_METHOD'], file_get_contents('php://input'));
    if(!$response)
    {
        sendHttpResponseAndExit(500, 'No response from resource "'.$resource->getName().'"');
    }
    else
    {
        HttpResponse::sendHttpResponseAndExit($response);
    }
}
else
{
    return new botwebapi\JsonHttpResponse(404, $_SERVER['REQUEST_URI'].' does not name a resource');
}

?>
