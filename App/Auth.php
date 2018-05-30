<?php 

namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;

/*
    Application Configuration
*/
class Auth {
   
   
    /*
        login a user, save remember_me if they wanted it
    */
    public static function login($user, $remember_me) {
        
        // generate a new session to prevent cross site attack
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user->user_id;
        
        if ($remember_me) {
            
            if ($user->rememberLogin()) {
                // set cookie with cookie name, token, exiredate, apply to all directories
                setcookie('remember_me', $user->remember_token, $user->expires, '/');
            }
            
        }
        
    }
    
    
    /*
        logout a user.
    */
    public static function logout() {
        /*
            Destroy the session completely
            
            Best Practice Code taken from:
            http://php.net/manual/en/function.session-destroy.php
        */
        
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        
        // un remember login 
        static::forgetLogin();
        
    }
    
    /*
        when the user logs in, we can remember the page they wanted to visit
        and redirect to back to it
    */
    public static function rememberRequestedPage() {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }
    
    public static function getReturnToPage() {
        return $_SESSION['return_to'] ?? '/';   
    }
    
    
    /*
        find which user is logged in right now 
        (their id is stored in session or as cookie)
    */
    public static function getUser() {
        
        if ( isset($_SESSION['user_id']) ) {
            
            return User::findByID($_SESSION['user_id']);
        
        } else {
            
            return static::loginFromRememberCookie();
            
        }
    }
	public static function getCampus(){
		if(isset($_SESSION['user_id'])){
			return User::getCampus();
		}
	}
	public static function getDept(){
		if(isset($_SESSION['user_id'])){
			return User::getDept();
		}
	}
	public static function getProgramme(){
		if(isset($_SESSION['user_id'])){
			return User::getProgramme();
		}
	}
	public static function getSemester(){
		if(isset($_SESSION['user_id'])){
			return User::getSemester();
		}
	}
	public static function getMessages(){
		if(isset($_SESSION['user_id'])){
			return User::getConversation();
		}
	}
    
    /*
        if cookie is set (remember me), then get user it belongs to
    */
    protected static function loginFromRememberCookie() {
        
        // if there is no cookie, we return the false
        $cookie = $_COOKIE['remember_me'] ?? false;
        
        // if there was...
        if ($cookie) {
            
            // find the user in remember_me table of the dabatase
            $remembered_login = RememberedLogin::findByToken($cookie);
            
            // if the remember_me data hasn't expired....
            if($remembered_login && ! $remembered_login->hasExpired()) {
                
                // get and return user, also auto-login the user
                $user = $remembered_login->getUser();
                static::login($user, false);
                return $user;
                
            }
        }
    }
    
    /*
        forget login when sign out
    */
    protected static function forgetLogin() {
        
        // get cookie
        $cookie = $_COOKIE['remember_me'] ?? false;
        
        if ($cookie) {
            
            // find row in db corresponding to this cookie
            $remembered_login = RememberedLogin::findByToken($cookie);
            
            if($remembered_login) {
                // if there, delete it
                $remembered_login->delete();
                
            }
            
            // set cookie to expire
            setcookie('remember_me', '', time()-3600);
        }
        
    }
   
}

?>