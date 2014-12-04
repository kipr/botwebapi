<?php

namespace botwebapi;

class LinksObject
{
    public function addLink($uri = array(), $options = array(), $is_single_link = true)
    {
        // $uri is an URL as array
        if(is_array($uri))
        {
            $scheme = isset($uri['scheme']) ? $uri['scheme'] : 'http';
            $user = isset($uri['user']) ? $uri['user'].'@' : '';
            $host = isset($uri['host']) ? $uri['host'] : $_SERVER['SERVER_NAME'];
            $path = isset($uri['path']) ? ($uri['path'][0] == '/' ? $uri['path'] : '/'.$uri['path']) : '';
            $query = isset($uri['query']) ? '?'.$uri['query'] : '';
            $href = $scheme.'://'.$user.$host.$path.$query;
        }
        // $uri is just the path + query
        else if($uri[0] == '/')
        {
            $href = 'http://'.$_SERVER['SERVER_NAME'].$uri;
        }
        // $uri is a full URL
        else
        {
            $href = $uri;
        }
        
        $link_object = array('href' => $href);
        
        if(is_array($options['additional']))
        {
            foreach($options['additional'] as $key => $value)
            {
                $link_object[$key] = $value;
            }
        }
            
        // add the type if not specified
        if(!array_key_exists('type', $link_object))
        {
            $link_object['type'] = 'application/vnd.KIPR.BotWebApi; charset=utf-8';
        }
        
        $rel = isset($options['rel']) ? $options['rel'] : 'self';
        
        if($is_single_link)
        {
            $this->{$rel} = $link_object;
        }
        else
        {
            if(!isset($this->{$rel}))
            {
                $this->{$rel} = array();
            }
            
            array_push($this->{$rel}, $link_object);
        }
    }
}

?>
