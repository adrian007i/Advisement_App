<?php

namespace App\Controllers;

use Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Home extends \Core\Controller {

    
    /*
        Before and After methods
        Action filters
    */
    
    protected function before() {
        //echo "before ";
        //return false;
    }
    
    protected function after() {
        //echo " after";
    }
    
    public function indexAction() {
        View::render("home/index.html", []);
    }
	
	 public function newAction() {
         View::render("home/addUser.html", []);
    }
	
	
	public function createAction(){
		$user = new User($_POST);
			
		if($user->save()){
				
			$this->redirect('/home/success');
				
		}else{
			View::render("home/addUser.html", ['user' => $user]);
		}
	}
		
		/**
		 * Show the signup success page
		 *
		 * @return void
		 **/
		public function successAction(){			
			View::render("home/success.html");
			header("Refresh:5; url=/");
		}
	
	
	
      /**
	  * Log out the user
	  * 
	  * @return void
	 **/
	public function destroyAction(){			
		Auth::logout();			
			
		$this->redirect('/');
	}

}// end class

?>