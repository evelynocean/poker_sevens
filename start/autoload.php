<?php
function autoload($classname)
{
    $classname = ltrim($classname, '\\');
    $file = "classes/";

    if ( ! file_exists($file.$classname.'.php')) {
        $file = "../classes/";
    }

    // exist namespace
    if ($lastpos = strrpos($classname, '\\')) {
        $namespace = substr($classname, 0, $lastpos);
        $classname = substr($classname, $lastpos + 1);
        $file =  __DIR__. DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, strtolower($namespace)) . DIRECTORY_SEPARATOR;
    }

    $file .= $classname . '.php';

    if (is_readable($file)) {

        require $file;
    }
}

spl_autoload_register('autoload');

