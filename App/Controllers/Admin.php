<?php

namespace App\Controllers;

use Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Admin extends Authenticated {

	public function semesterAction() {
        View::render("Admin/semester.html", []);
    }
	public function createAction(){
		$user = new User($_POST);
			
		if($user->save()){				
			$this->redirect('/Home/success');				
		}else{
			View::render("Admin/addUser.html");
		}
	}
	public function addSemesterAction(){
		$user = new User($_POST);
			
		if($user->addSemester()){				
			$this->redirect('/Admin/semesterAdded');				
		}else{
			View::render("Admin/semester.html");
		}
	}
		
		/**
		 * Show the signup success page
		 *
		 * @return void
		 **/
		public function successAction(){			
			View::render("Admin/success.html");
			header("Refresh:5; url=/");
		}
		public function semesterAddedAction(){			
			View::render("Admin/semesterAdded.html");
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