<?php

namespace App\Controllers;

use Core\View;
use \App\Models\User;
use \App\Flash;

class Signup extends \Core\Controller {

    /*
        Sign up for new new account view.
    */
    public function newAction() {
        View::render("Signup/signup.html");
    }
    
    /*
        try to create new account. 
        redirect on success.
        re-render page with data if failed.
    */
    public function createAction() {
        $user = new User($_POST);
        
        if($user->save()) {
            
            Flash::addMessage( 'Welcome, ' . ucfirst($user->first_name) );
        
            $this->redirect('/signup/success');
        
        } else {
            View::render('Signup/signup.html', ['user' => $user]);           
        }
        
    }
    
    /*
        display a success page if sign up success.
    */
    public function successAction() {
        View::render('signup/success.html');
    }
    
    
    
    
    /*
        AJAX Validators
        Checks to see if an email or a username already exists 
        in the database and returns true or false as JSON
    */
    
    public function validateEmailAction() {
        $is_valid = ! User::emailExists($_GET['email']);   
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
    
    public function validateUsernameAction() {
        $is_valid = ! User::userNameExists($_GET['username']);   
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
    
}// end class

?>