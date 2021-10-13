<?php

namespace mf\utils;

class HttpRequest extends AbstractHttpRequest {
 
    public function __construct(){
        $this->script_name = $_SERVER['SCRIPT_NAME'];
        if (isset($_SERVER['PATH_INFO'])) {
            $this->path_info = $_SERVER['PATH_INFO'];
        }else {
            $this->path_info = null;
        }
        $this->root = dirname($_SERVER['SCRIPT_NAME']);
        $this->method = $_SERVER['REQUEST_METHOD'] ;
        $this->get = $_GET;
        $this->post = $_POST;
    }


    // public function __construct($script_name, $path_info, $root, $method, $get, $post) {
    //     $this->script_name = $script_name;
    //     $this->path_info = $path_info;
    //     $this->root = $root;
    //     $this->method = $method;
    //     $this->get = $get;
    //     $this->post = $post;
    // }

}