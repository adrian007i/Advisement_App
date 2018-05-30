<?php

namespace App\Controllers;

abstract class Authenticated extends \Core\Controller {

    /*
        Action filter, 
        before actions, require login
    */
    protected function before() {
        $this->requireLogin();
    }
    
}// end class


?>