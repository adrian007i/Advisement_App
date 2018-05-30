<?php

namespace App\Models;

use \App\Models\User;
use \App\Token;
use PDO;

class RememberedLogin extends \Core\Model {
    
    
    /*
        find row that corresponds to token,
        return $this class with the data
    */
    public static function findByToken($token) {
        
        $token = new Token($token);
        $token_hash = $token->getHash();
        
        $sql = 'SELECT * FROM remember_me WHERE token_hash = :token_hash';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        
        /* get class instead of data array */
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            
        $stmt->execute();
        
        return $stmt->fetch();
        
    }
    
    /*
        Get a User instance that corresponds to $this Remembered Login
    */
    public function getUser() {
        
        return User::findById($this->user_id);
    }
    
    
    /*
        find if expire date has passed
    */
    public function hasExpired() {
        return strtotime($this->expires_at) < time();
    }
    
    
    /*
        delete the row, in order to forget $this login
    */
    public function delete() {
        
        $sql = 'DELETE FROM remember_me WHERE token_hash = :token_hash';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);
        
        $stmt->execute();
        
    }
    
}
?>