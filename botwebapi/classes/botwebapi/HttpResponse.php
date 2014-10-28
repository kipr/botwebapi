<?php

namespace botwebapi;

class HttpResponse
{
    const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';
    
    const CHARACTER_SET_UTF_8 = 'UTF-8';
    
    private $status_code = 204;
    private $content_type = CONTENT_TYPE_TEXT_PLAIN;
    private $character_set = CHARACTER_SET_UTF_8;
    private $content = "";
    private $additional_response_fields = array();
    
    public function __construct($status_code,
                                $content,
                                $additional_response_fields = array(),
                                $content_type = HttpResponse::CONTENT_TYPE_TEXT_PLAIN,
                                $character_set = HttpResponse::CHARACTER_SET_UTF_8)
    {
        $this->status_code = $status_code;
        
        switch($character_set)
        {
        case HttpResponse::CHARACTER_SET_UTF_8:
            $this->character_set = $character_set;
            break;
        default:
            throw new \Exception('Invalid argument: $character_set');
        }
        
        switch($content_type)
        {
        case HttpResponse::CONTENT_TYPE_TEXT_PLAIN:
        case HttpResponse::CONTENT_TYPE_TEXT_HTML:
            $this->content = $content;
            $this->content_type = $content_type;
            break;
        case HttpResponse::CONTENT_TYPE_APPLICATION_JSON:
            $this->content = json_encode($content);
            $this->content_type = $content_type;
            break;
        default:
            throw new \Exception('Invalid argument: $content_type');
        }
        
        $this->additional_response_fields = $additional_response_fields;
    }
    
    public static function sendHttpResponseAndExit(HttpResponse $response)
    {
        // send header
        $response->sendStatus();
        $response->sendContentType();
        $response->sendAdditionalResponseFields();
        
        // send content
        if(!empty($response->content))
        {
            echo $response->content;
        }
        
        // nothing to do after we sent the response
        exit();
    }
    
    private function sendStatus()
    {
        $sapi_type= substr(php_sapi_name(), 0, 3);
        
        if($sapi_type == 'cgi' || $sapi_type == 'fpm')
        {
            header('Status: '.$this->status_code.' '.HttpStatus::httpStatusCodeToString($this->status_code));
        }
        else
        {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol.' '.$this->status_code.' '.HttpStatus::httpStatusCodeToString($this->status_code));
        }
    }
    
    public function sendContentType()
    {
        if(!empty($this->character_set))
        {
            header('Content-Type: '.$this->content_type);
        }
        else
        {
            header('Content-Type: '.$this->content_type.'; charset='.$this->character_set);
        }
    }
    
    private function sendAdditionalResponseFields()
    {
        if(is_array($this->additional_response_fields))
        {
            foreach($this->additional_response_fields as $field_name => $value)
            {
                header($field_name.': '.$value);
            }
        }
    }
}

?>
