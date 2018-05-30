<?php

namespace App\Controllers;

use Core\View;
use App\Models\Advisement;
use App\Auth;
use App\Flash;

class Advisements extends Authenticated {


       
    public function viewAction($user_id) {
        
        // get current logged in user
        $current_user = Auth::getUser();
        
        // check if user is valid
        if ($current_user->user_id != $user_id && $current_user->role_name != 'Advisor') {
            Flash::addMessage('Permission Denied', Flash::DANGER);
            $this->redirect('/');
        } else {
            $advisements = Advisement::getAdvisements($user_id);
            View::render('/advisements.html', ['advisements' => $advisements]);
        }
       
    }
    
    
    /*
        save a new course for an advisement
    */
    
    public function newCourseAction() {
        $advisement = new Advisement($_POST);
        if ($advisement->addNewCourse()) {
            // nothing
        } else {
            foreach ($advisement->errors as $error) {
                Flash::addMessage($error, Flash::DANGER);
            }
        }

        $this->redirect('/advisements/'.$advisement->new_student.'/view');
    }
    
    
    public function updateCourseAction() {
        $advisement = new Advisement($_POST);
        if ($advisement->updateCourse()) {
            // nothing
        } else {
            foreach ($advisement->errors as $error) {
                Flash::addMessage($error, Flash::DANGER);
            }
        }
        $this->redirect('/advisements/'.$advisement->new_student.'/view');
    }
   
    public function removeCourseAction() {
        $advisement = new Advisement($_POST);
        if ($advisement->removeCourse()) {
            // nothing
        } else {
            foreach ($advisement->errors as $error) {
                Flash::addMessage($error, Flash::DANGER);
            }
        }
        $this->redirect('/advisements/'.$advisement->new_student.'/view');
    }
    
    
    public function approveCourseAction()
    {
        $advisement = new Advisement($_POST);
        if ($advisement->approveCourse()) {
            // nothing
        } else {
            foreach ($advisement->errors as $error) {
                Flash::addMessage($error, Flash::DANGER);
            }
        }
        $this->redirect('/advisements/'.$advisement->new_student.'/view');

    }
    
    public function clearDirtyAction()
    {
        $advisement = new Advisement($_POST);
        if ($advisement->clearDirty()) {
            // nothing
        } else {
            foreach ($advisement->errors as $error) {
                Flash::addMessage($error, Flash::DANGER);
            }
        }
        $this->redirect('/advisements/'.$advisement->new_student.'/view');

    }

    
   
}// end class

?>