<?php

namespace mf\utils;

class ClassLoader extends AbstractClassLoader {

    public function getFilename(string $classname): string {
        $fileName = str_replace("\\", DIRECTORY_SEPARATOR, $classname) . ".php";
        return $fileName;
    }

    public function makePath(string $filename): string {
        $path = $this->prefix . DIRECTORY_SEPARATOR . $filename;
        return $path;
    }

    public function loadclass(string $classname) {
        $filename = $this->getFilename($classname);
        $path = $this->makePath($filename);
        if(file_exists($path)) {
            return require_once($path);
        }
    }

}
