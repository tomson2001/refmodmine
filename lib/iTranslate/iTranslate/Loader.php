<?php

abstract class iTranslate_Loader {

    public static final function loadClass($class) {
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        $includePaths[] = iTranslate::getInstance()->getBasePath();
        foreach ($includePaths as $ip) {
            $path = realpath($ip . '/' . str_replace("_", "/", $class) . ".php");
            if (is_readable($path)) {
                require_once $path;
                return;
            }
        }
        throw new Exception("Could not autoload class: ".$class);
    }

}