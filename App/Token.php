<?php 

namespace App;

class Token {
    
    /*
        token value
    */
    protected $token;
    
    /*
        This class creates a new token.
        If a token is provided it will use that instead.
    */
    public function __construct($token_value = null) {

        if ($token_value) {
            $this->token = $token_value;
        } else {
            // generate random token // 16 bytes 128 bits or 32 hex characters 
            $this->token = bin2hex(random_bytes(16));    
        }
    }
    
    public function getValue() {
        return $this->token;
    }
    
    /*
        Get the hash of the token
    */
    public function getHash() {
        //64 char
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);
    }
        
}// end class

?>