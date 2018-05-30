<?php

namespace App\Controllers;

use \App\Models\Course;


class Validation extends \Core\Controller {

    
       
    /*
        AJAX Validators
        Used by client side validation
    */
    
    public function validateCourseAction() {
        $is_valid = Course::courseExists($_GET['new_course']);   
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
    

}// end class

?>