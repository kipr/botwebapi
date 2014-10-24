<?php

namespace botwebapi;

class ErrorMessage
{
    public $class = '';
    public $sub_class = '';
    public $details = '';
    
    function __construct($class, $sub_class, $details)
    {
        $this->class = $class;
        $this->sub_class = $sub_class;
        $this->details = $details;
    }
}

class JsonHttpResponse
{
    public static function sendClientError($status_code, $error_message)
    {
        if(number_format($status_code / 100) != 4)
        {
            throw new \Exception('Invalid argument: $status_code');
        }
        
        $response = new HttpResponse();
        $response->setContent(HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                              array('Error' => new ErrorMessage('Client Error',
                                                                HttpResponse::httpStatusCodeToString($status_code),
                                                                $error_message)));
        $response->setHttpStatusCode($status_code);
        HttpResponse::sendHttpResponse($response);
    }
    
    public static function sendServerError($status_code, $error_message)
    {
        if(number_format($status_code / 100) != 5)
        {
            throw new \Exception('Invalid argument: $status_code');
        }
        
        $response = new HttpResponse();
        $response->setContent(HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                              array('Error' => new ErrorMessage('Server Error',
                                                                HttpResponse::httpStatusCodeToString($status_code),
                                                                $error_message)));
        $response->setHttpStatusCode($status_code);
        HttpResponse::sendHttpResponse($response);
    }
}

?>