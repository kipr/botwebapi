<?php

if(!is_dir(CLASS_PATH))
{
    throw new Exception('Internal error: CLASS_PATH is not a directory! ('.CLASS_PATH.')');
}

spl_autoload_register(function ($class)
{
    $class_file_path = CLASS_PATH.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
    
    if(!is_readable($class_file_path))
    {
        throw new Exception('Internal error while loading '.$class.': '.$class_file_path.' is not a file');
    }
    
    require_once $class_file_path;
});

?>
