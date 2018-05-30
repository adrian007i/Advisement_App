<?php 

namespace Core;

class Router {
    
    // Array of routes (the routing table)
    protected $routes = [];
    
    // array of parameters for requested route.
    protected $params = [];
    
    
    // get currently matched params
    public function getParams() {
        return $this->params;
    }
    
    // get all routes
    public function getRoutes() {
        return $this->routes;
    }
    
    // modify string from url format to class/method name format
    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }
    
    
    
    /*
        A URL with a query string e.g. mvc/index?foo=bar will have the query string
        variables (?foo=bar) removed.
        Note: htaccess converts the first ? into &
    
    */
    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            // $parts will contain an array with items separated by &
            $parts = explode('&', $url, 2);
            // if there is no '=' in the string before the first '&' (the url without query string)
            if(strpos($parts[0], '=') === false) {
                $url = $parts[0]; // url is now that first part
            } else {
                // this is a situation where you have http://mvc.com/foo=bar 
                $url = '';
            }
        }
        return $url;
    }
    
    /*
        Get the namespace for the controller class if needed. 
        This is passed as a route parameter sometimes.
    */
    protected function getNamespace() {
        // defaults to default Controllers namespace
        $namespace = 'App\Controllers\\';
        // if the namespace is specified, it as appened to the default
        if (array_key_exists('namespace', $this->params)) {
            $namespace = $namespace . $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
    
    // add a route, $route is the route url with {} to define where the controler and actions are
    // and $params is the controller, action etc.    
    public function add($route, $params = []) {
        
        // convert submitted route to a regular expression.
        // start by escaping forward slashes... replace / with \/
        $route = preg_replace('/\//', '\\/', $route);
        // convert variables... replace {controller} with (?P<controller>[a-z-]+)
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        // Convert variables... allow for custom regular expressions like {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // add the start and end to complete the regular expression (also make it case insensitive)
        $route = '/^' . $route . '$/i';
        
        // now add regular expression as key and params as value to the associative array of routes
        $this->routes[$route] = $params;        
    }
    
    
    // match routes to the routing table, set $params property if match found
    // return true or false if match was found.
    public function match($url) {
        
        // loop through and find match
        foreach ($this->routes as $route => $params) {
            //if url matches a regular expression key
            if (preg_match($route, $url, $matches)) {
                // loop through the matches made by regular expression
                // and add them to the params
                foreach($matches as $key => $match) {
                    if(is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                // set params and return true
                $this->params = $params;
                return true;
                
            }
            
        }
        // return false if no match found...
        return false;
    }
    
    
    /*
        dispath method.
        This method checks for a match in the url first, 
        then it creates the controller specified and invokes the requried action.
    */ 
    public function dispatch($url) {
        
        // remove query string variables
        $url = $this->removeQueryStringVariables($url);
        
        // check if match
        if ($this->match($url)) { 
            
            // convert controller specified in URL to the class name (to create object)
            $controller = $this->convertToStudlyCaps($this->params['controller']);
            
            // add namespace to controller if specified
            $controller = $this->getNamespace() . $controller;
            
            // only if class exists
            if(class_exists($controller)) {
                
                // create the controller object
                $controller_object = new $controller($this->params);
                
                // get action and covert to camel case
                $action = $this->convertToCamelCase($this->params['action']);
                
                // get id for action if exists
                $id = null;
                if (isset($this->params['id'])) {
                    $id = $this->params['id']; 
                }
                
                // if the method exists, then call it, otherwise.. error
                if(is_callable([$controller_object, $action])) {
                    if ( $id != null )  {
                        $controller_object->$action($id);
                    } else {
                        $controller_object->$action();
                    }
                } else {
                    throw new \Exception("Method $action (in controller $controller) not found", 404);
                }
                
            } else {
                throw new \Exception("Controller class $controller not found", 404);
            }
        } else {
            throw new \Exception( "No route matched.", 404);    
        }
    }
    
    
    
    
}// end class
?>