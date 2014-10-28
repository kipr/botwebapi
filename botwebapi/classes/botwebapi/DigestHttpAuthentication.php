<?php

namespace botwebapi;

define('BOT_WEB_API_REALM', 'BotWebAPI');

class DigestHttpAuthentication
{
    public static function authenticate()
    {
        // if not already done, ask the client to authentificate
        if(empty($_SERVER['PHP_AUTH_DIGEST']))
        {
            $response = new JsonHttpResponse(401,
                                             'Authentication required',
                                             array('WWW-Authenticate' => 'Digest realm="'.BOT_WEB_API_REALM.
                                                                         '",qop="auth",nonce="'.uniqid().
                                                                         '",opaque="'.md5(BOT_WEB_API_REALM).'"'),
                                             HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                                             HttpResponse::CHARACTER_SET_UTF_8);
            HttpResponse::sendHttpResponseAndExit($response);
        }
        
        // check if device.conf exists
        $device_conf_path = '/etc/kovan/device.conf';
        if(is_readable($device_conf_path))
        {
            $content = file_get_contents($device_conf_path);
            preg_match('`kovan_serial/password: (.*)\n`', $content, $matches);
            $password = $matches[1];
            
            if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])))
            {
                $response = new botwebapi\JsonHttpResponse(500, 'Invalid PHP_AUTH_DIGEST');
                HttpResponse::sendHttpResponseAndExit($response);
            }
            
            // calculate the valid response
            $ha1 = md5($data['username'].':'.BOT_WEB_API_REALM.':'.$password);
            $ha2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
            $valid_response = md5($ha1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$ha2);
            
            // and compare it with the one given by the request
            if($data['response'] != $valid_response)
            {
                $response = new JsonHttpResponse(401,
                                                 $valid_response.' - '.$password.' - '.$data['response'],
                                                 array('WWW-Authenticate' => 'Digest realm="'.BOT_WEB_API_REALM.
                                                                             '",qop="auth",nonce="'.uniqid().
                                                                             '",opaque="'.md5(BOT_WEB_API_REALM).'"'),
                                                 HttpResponse::CONTENT_TYPE_APPLICATION_JSON,
                                                 HttpResponse::CHARACTER_SET_UTF_8);
                HttpResponse::sendHttpResponseAndExit($response);
            }
        }
    } 
}

// function to parse the http auth header
// from http://php.net/manual/en/features.http-auth.php
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}
?>
