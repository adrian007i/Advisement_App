<?php

namespace App\Controllers;

use Core\View;
use \App\Models\User;
use \App\Models\Chat;
use \App\Auth;
use \App\Flash;

class Messages extends Authenticated {

	public function newAction() {
	
        View::render("Chat/newMessage.html");
    }
	
	public function sendAction(){
		$user = new User($_POST);
		
		if($user->sendMessage()){
			
			Flash::addMessage('Message has been sent');
			$this->redirect('/Home/inbox');
			
		}else{
			Flash::addMessage('There was an error in sending your message');
			$this->redirect('/Messages/new');
		}
	}
	public function deleteConversationAction(){
		$message = $_GET['delete_conversation'];
		
		if($message){
			$user = User::deleteMessage($message);
			if($user){
				Flash::addMessage('Message has been deleted');
				$this->redirect('/Home/inbox');
			}
			Flash::addMessage('There was an error in deleting your message');
			$this->redirect('/Home/inbox');
		}			
	}
	
	public function viewConversationAction(){
		$message = $_GET['view_conversation'];
		$subject = $_GET['subject'];
		
		if($message){
			$user = User::viewMessages($message);	
			if($user){
				User::updateConversationLastView($message);
				View::render("Chat/inbox.html", ['allMessages'=> $user, 'id'=>$message,'subject'=>$subject]);
			}else{
				Flash::addMessage('There was an error in viewing your message');
				$this->redirect('/Home/inbox');
			}
		}			
	}
	public function addMessage(){
		$message = $_GET['view_conversation'];
		$subject = $_GET['subject'];
		
		if($message){
			$user = $user = new User($_POST);		
			if($user->replyToMessage($message)){
				$this->redirect('/Messages/viewConversation&view_conversation='.$message.'&subject='.$subject.'');
			}else{
				Flash::addMessage('There was an error in sending your message');
				$this->redirect('/Home/inbox');
			}
		}			
	}

}// end class

?>