<?php

namespace App\Controllers;

use Core\View;
use \App\Models\User;
use \App\Models\Advisement;
use \App\Auth;
use \App\Flash;
use \App\Models\Degree;

class Home extends Authenticated {
    
    public function indexAction() {	
		$user = Auth::getUser();
		if($user){	
			View::render("Home/index.html");
		}else{
			View::render('Login/index.html');	
		}
	}
	
	public function newAction() {
        View::render("Admin/addUser.html", []);
    }
	
	public function deleteUserAction(){
		View::render("Admin/deleteUser.html");
	}
	
	public function semesterAction() {
        View::render("Admin/semester.html", []);
    }
	
	public function profileAction(){
		View::render("Home/profile.html");
	}
	
	public function advisementAction(){
		$user = Auth::getUser();
		if($user->role_name == "Advisor"){
			View::render("Advisor/advisement.html");
		}else if($user->role_name == "Student"){
			$advisements = Advisement::getAdvisements($user->user_id);
            View::render('Student/advisements.html', ['advisements' => $advisements]);
		}
	}
	
	public function degreeAction(){
		$student = $_SESSION['user_id'];
		$results = Degree::getDegreeCourses(Degree::getProgramId($student)['program_id']);
		
		View::render("Student/degree.html", ['results' => $results]);
	}
	
	public function inboxAction(){
		View::render("Chat/inbox.html");
	}
	
	public function successAction(){
		
		header("Refresh:5; url=/");
		View::render("Home/success.html");

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
