<?php

// from http://php.net/manual/en/function.rmdir.php
// When the directory is not empty:
function rrmdir($dir)
{
    if(is_dir($dir))
    {
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if ($object != "." && $object != "..")
            {
                if(filetype($dir."/".$object) == "dir")
                {
                    $result = rrmdir($dir."/".$object);
                }
                else
                {
                    $result = unlink($dir."/".$object);
                }
                
                if(!$result)
                {
                    return false;
                }
            }
        }
        reset($objects);
        return rmdir($dir);
    }
}

?>
