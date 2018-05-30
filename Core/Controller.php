<?php 

namespace Core;

use App\Auth;
use \App\Flash;

/*
    Base Controller
    
    Controllers created by developers will inherit this class

*/

abstract class Controller {

    /*
        Parameters from the matched route..
    */
    protected $route_params = [];
    
    /*
        constructor: set the route_params var
    */
    public function __construct($route_params) {
        $this->route_params = $route_params;
    }
    
    
    /*
        Action Filters: 
        
        __call magic method in php is automatically called when
        the intended method was not found or was private and unavailable.
        
        All controllers should have method names with 'Action' at the end.
        When index() is request for example, this method will be called instead,
        which will then call indexAction().
        
        The roundabout setup lets us run code before and after every method call (view)
        so that we can check if the user is logged in, perform cleanup etc.
        
        @see http://php.net/manual/en/language.oop5.magic.php
    */
    public function __call($name, $args) {
     
        // index becomes indexAction
        $method = $name . 'Action';
        
        // if method exists
        if(method_exists($this, $method)) {
            // this calls the before method, if the before method is true 
            // (user logged in for example) then the method is called
            if($this->before() !== false) {
                // call the method, also pass in the arguments if any
                call_user_func_array([$this, $method], $args);
                // run the after method...
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in Controller " . get_class($this), 404);
        }
    }
    
    /*
        Action filters.. Before and After which can be inherited by the concrete class
        These methods should remain empty.
    */
    protected function before() {
    }
    protected function after() {
    }
    
    
    /*
        redirect to another page immediately.
        sets http response to 303 = redirect.
    */
    public function redirect($url) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }
    
    /*
        require a login,
        redirects to login page if the user isn't logged in
    */
    public function requireLogin() {
        
        // if not logged in
        if(!Auth::getUser()) {
            
            Flash::addMessage('You must login to view this page.', Flash::INFO);
            
            Auth::rememberRequestedPage();
            
            $this->redirect('/login/new');
        
        }
    }
    
}// end class

?>