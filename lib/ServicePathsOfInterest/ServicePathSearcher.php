<?php

namespace ServicePathsOfInterest;

use AllowDynamicProperties;

#[AllowDynamicProperties] class ServicePathSearcher
{
    function __construct(){
        $this->env_paths = explode(PATH_SEPARATOR, getenv('PATH'));
        $this->unquoted_paths = [];
    }

    function search(){
        foreach ($this->env_paths as $path){
            $path = trim($path);
            if (strpos($path, ' ') !== false && ($path[0] !== '"' || $path[strlen($path) - 1] !== '"')){
                $this->unquoted_paths[] = $path;
            }
        }
        if (!empty($this->unquoted_paths)){
            return ['status' => 'success', 'data' => $this->unquoted_paths];
        }
        return ['status' => 'failure', 'data' => 'No unquoted paths found.'];
    }
}