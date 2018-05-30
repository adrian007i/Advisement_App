<?php 

namespace Core;

use PDO;
use App\Config;

/*
    Base Model
    
    Models created by developers will inherit this class

*/

abstract class Model {

    /*
        Get PDO db connection
    */
    protected static function getDB() {
        
        static $db = null;
        
        if($db === null) {
            
            $db_host = Config::DB_HOST;
            $db_name = Config::DB_NAME;
            $user = Config::DB_USER; 
            $pass = Config::DB_PASS;
            
            $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";

            $db = new PDO($dsn, $user, $pass);

            // throw exception when error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        }
        
        return $db;
    }

    
}// end class

?>