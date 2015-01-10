<?php

namespace botwebapi\resources\api\target;
use botwebapi\resources as resources;
use botwebapi as botwebapi;

/**
 * Projects Resource
 * 
 * Its purpose is to provide access to target information
 */
class Target extends resources\BotWebApiResource
{
    public function __construct($resource_uri, $parent_resource)
    {
        parent::__construct($resource_uri, '1.0', 'https://github.com/kipr/botwebapi', $parent_resource);
    }
    
    public function get()
    {
        $links = new botwebapi\LinksObject();
        $links->addLink($this->getResourceUri());
        
        if($this->getParentUri())
        {
            $links->addLink($this->getParentUri(), array('rel' => 'parent'));
        }
        
        if(PLATFORM == 'LINK')
        {
            $platform = 'KIPR Link';
            
            
            // check if device.conf exists
            $device_conf_path = '/etc/kovan/device.conf';
            if(is_readable($device_conf_path))
            {
                $content = file_get_contents($device_conf_path);
                preg_match('`device_name: (.*)\n`', $content, $matches);
                $name = $matches[1];
            }
            else
            {
                $name = 'Unnamed Link';
            }
            
            $platform = 'KIPR Link';
        } else {
            $platform = php_uname('m');
            $name = php_uname('n');
        }
            
        return new botwebapi\JsonHttpResponse(200, array('about' => new botwebapi\AboutObject($this),
                                                         'os' => php_uname('s'),
                                                         'platform' => $platform,
                                                         'name' => $name,
                                                         'links' => $links));

    }
    
    public function post($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function put($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function delete($content)
    {
        return new botwebapi\JsonHttpResponse(405, $_SERVER['REQUEST_METHOD'].' is not supported');
    }
    
    public function getChild($resource_name)
    {
        return NULL;
    }
}
?>
