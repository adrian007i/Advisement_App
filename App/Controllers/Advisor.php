<?php

namespace App\Controllers;

use Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\AdvisorList;
use \App\Models\Report;


class Advisor extends Authenticated {

    /*
    public function viewAction() {
        
        // get current logged in user
        $current_user = Auth::getUser();
        
        // check if user is valid
        if ($current_user->role_name != 'Advisor') {
            Flash::addMessage('Permission Denied', Flash::DANGER);
            $this->redirect('/');
        } else {
            $dept_list = AdvisorList::getListByDept();
            View::render('advisor.html', ['dept_list' => $dept_list]);
        }
        
    }
    */
    
    public function viewAction() {

        // get current logged in user
        $current_user = Auth::getUser();

        // check if user is valid
        if ($current_user->role_name != 'Advisor') {
            Flash::addMessage('Permission Denied', Flash::DANGER);
            $this->redirect('/');
        } else {
            $dept_list = AdvisorList::getListByDept();
            $fullListofStudents = Report::advisorList();
            $deptstudents = Report::advisorDeptStudents();
            $chathistory = Report::allPosts();
            $pendingapproval = Report::isApproved();
            // $studentNotAdvisedYet = Report::studentNotAdvisedYet();

            View::render('advisor.html', ['dept_list' => $dept_list, 'fullListofStudents' => $fullListofStudents ,
                                          'chathistory' => $chathistory, 'pendingapproval' => $pendingapproval,
                                          'deptstudents' => $deptstudents]);
            // View::render('advisor.html', ['fullListofStudents' => $fullListofStudents]);
        }

    }
    
    
}// end class

?>