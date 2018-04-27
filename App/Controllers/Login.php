<?php

namespace App\Controllers;

use Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller {

    /*
        render the login page...
    */
    public function newAction() {
        if(Auth::getUser()) {            
            //Flash::addMessage('You are already signed in', Flash::INFO);            
            $this->redirect('/home');
        
        } else {            
            View::render("/Login/login.html");        
        }
    }
        
    /*
        perform the task of logging in
        using session.
        if remember me checked, then store cookie and database data
    */
    public function createAction() {
        
        // try to authenticate
        $user = User::authenticate($_POST['user_id'], $_POST['password']);
        
        // find if remember be checked  
        $remember_me = isset($_POST['remember_me']);
        
        // if authenticated...
        if ($user) {
            
            // perform login, with remember me 
            Auth::login($user, $remember_me);
            
            // flash a message
            Flash::addMessage('You are now signed in.');
            
            $this->redirect(Auth::getReturnToPage());
        
        } else {
            
            Flash::addMessage('Login Failed, invalid username or password.', Flash::DANGER);            
            
            View::render("Login/login.html", [ 
                'username' => $_POST['user_id'],
                'remember_me' => $remember_me
            ]);
            
        }
    }
    
  
    
    
    
}// end class

?>