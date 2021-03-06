<?php

namespace botwebapi;

// Set constants
define('API_ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
define('INCLUDE_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'includes');
define('CLASS_PATH', API_ROOT_PATH.DIRECTORY_SEPARATOR.'classes');
if(defined('PHP_WINDOWS_VERSION_MAJOR'))
{
    define('PLATFORM', 'WIN');
    
    define('CMD_PATH', 'C:\windows\system32\cmd.exe');
    define('COMPILE_HELPER_PATH', 'C:\Users\stefa_000\Documents\Projects\botwebapi\INSTALL\Windows\compile.bat');
}
else
{
    define('PLATFORM', 'LINK');
}
define('ROOT_RESOURCE_URI_PATH', '/api');

// allow requests from any origin
if(isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
}

// respond to preflights
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
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
    sendHttpResponseAndExit(404, $_SERVER['REQUEST_URI'].' does not name a resource');
}

?>
