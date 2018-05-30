<?php

namespace App\Controllers;

use Core\View;

class Help extends \Core\Controller {

    
    /*
        Help Controller
    */
    
    
    public function viewAction() {
        View::render("help.html");
    }
    

}// end class

?>