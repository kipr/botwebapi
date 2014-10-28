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

// Create a new api root resource and let it handle the request
$resource = new resources\api\Api('/api');
$response = $resource->handleRequest($_SERVER['REQUEST_URI']);
if(!$response)
{
    sendHttpResponseAndExit(500, 'No response from resource "'.$resource->getName().'"');
}

HttpResponse::sendHttpResponseAndExit($response);

?>
