<?php

namespace botwebapi;

class LinksObject
{
    public function addLink($href, $rel = 'self', $is_single_link = true)
    {
        if($is_single_link)
        {
            $this->{$rel} = array('href' => $href);
        }
        else
        {
            if(!isset($this->{$rel}))
            {
                $this->{$rel} = array();
            }
            
            array_push($this->{$rel}, array('href' => $href));
        }
    }
}

?>
