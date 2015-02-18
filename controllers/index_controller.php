<?php

class IndexController extends BaseController {

    public static function route() {

        $regex = '/^' . preg_quote(PATH_PREFIX, '/') . '/';
        $uri = preg_replace($regex, '', $_SERVER['REQUEST_URI']);
        $uri = strtolower($uri);
        
        $routes = array('/$' => array('index', 'base', array()), // empty route, just root domain
                        '/404' => array('index', 'misc_page', array('404')),
                        );

        foreach ($routes as $route => $dest) {
            if (preg_match("#^${route}#", $uri, $match)) {
                list($controller, $action, $args) = $dest;
                $route_match = array_shift($match);

                // grab any regex matches if they exist
                if (!empty($match)) {
                    $args = array_merge($args, $match);
                }

                // parse the rest of the uri for additional args
                if (!empty($args)) {
                    $uri = str_replace($route_match, '', $uri);
                }
                $uri = trim($uri, '/');
                if (!empty($uri)) {
                    $args = array_merge($args, explode('/', $uri));
                }
                break;
            }
        }

        //v($controller, $action, $args); exit;

        // 404 page
        if (empty($controller)) {
            list($controller, $action, $args) = array('index', 'missing_404', array());
        }

		// dynamic routing to appropriate controller
        $class = ucwords($controller) . 'Controller';
        $obj = new $class;
        $obj->$action($args);
    }
    
    public static function api_route() {
        $regex = '/^' . preg_quote(PATH_PREFIX, '/') . '/';
        $method = preg_replace($regex, '', $_SERVER['REQUEST_URI']); // strip the prefix hostname

        // check api key
        if ($method != '/help' && (empty($_REQUEST['api_key']) || $_REQUEST['api_key'] != api_key(API_SECRET))) {
            return array('error' => 'missing or invalid api_key');
        }

        return api::call($method, $_REQUEST);
    }

    // 404, tos and contact pages
    public function misc_page($args) {
        $this->_render('misc/' . $args[0]);
    }

    public function base($args) {
        $this->_render('index/base', $args);
    }
}