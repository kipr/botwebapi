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

class JsonHttpResponse extends HttpResponse
{
    public function __construct($status_code, $content, $additional_response_fields = array())
    {
        $status_class = number_format($status_code / 100);
        switch($status_class)
        {
        case 1: // 1xx (Informational)
        case 2: // 2xx (Successful)
        case 3: // 3xx (Redirection)
            parent::__construct($status_code,
                                $content,
                                $additional_response_fields,
                                HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                                HttpResponse::CHARACTER_SET_UTF_8);
            break;
        
        case 4: // 4xx (Client Error)
            parent::__construct($status_code,
                                array('Error' => new ErrorMessage('Client Error',
                                                                  HttpStatus::httpStatusCodeToString($status_code),
                                                                  $content)),
                                $additional_response_fields,
                                HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                                HttpResponse::CHARACTER_SET_UTF_8);
            break;
        
        case 5: // 5xx (Server Error)
            parent::__construct($status_code,
                                array('Error' => new ErrorMessage('Server Error',
                                                                  HttpStatus::httpStatusCodeToString($status_code),
                                                                  $content)),
                                $additional_response_fields,
                                HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                                HttpResponse::CHARACTER_SET_UTF_8);
            break;
        
        default:
            throw new \Exception('Invalid argument: $status_code');
        }
    }
    
    public static function getLastJsonErrorMessage()
    {
        if(function_exists('json_last_error_msg'))
        {
            return json_last_error_msg();
        }
        else
        {
            // from http://php.net/manual/en/function.json-last-error-msg.php
            static $errors = array(
                JSON_ERROR_NONE             => null,
                JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
                JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
                JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
                JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
                JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
            );
            $error = json_last_error();
            return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
        }
    }
}

?>
