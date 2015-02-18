<?php

class api {

    public static function call($method, $args=array()) {
        $method = preg_replace('/\?.*/', '', $method); // no query params
        $method = trim($method, '/');

        list($controller) = explode('/', $method);
        $method = str_replace('/', '_', $method);

        $obj = new ApiController;

        // check if api method exists
        if (!method_exists($obj, $method)) {

			$class_methods = array_diff(get_class_methods($obj), array('__construct'));
            $api_methods = array_map(function($method) {
                    return '/' . preg_replace('/_/', '/', $method, 1);
                }, $class_methods);

            $errmsg = "Invalid API method: /${method}.";
            $errmsg .= ' Full list is: ';
            return array('error' => array($errmsg, $api_methods));
        }

        return $obj->$method($args);
    }

}