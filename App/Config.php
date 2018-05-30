<?php 

namespace App;

/*
    Application Configuration
*/
class Config {
    
    /*
        Database connection settings
        
        DB_HOST : url of database
        DB_NAME : Name of database
        DB_USER : Username for connection;
        DB_PASS : Password for connection;
        
        @vars string
    */
    const DB_HOST = 'localhost';
    const DB_NAME = 'test';
    const DB_USER = 'root';
    const DB_PASS = '';
 
    /*
        Show or hide errors
        @var boolean
    */
    const SHOW_ERRORS = true;
    const LOG_TO_FILE = false;
    
    /*
        Secret Key for Remember me functionality
        
    */                  
    const SECRET_KEY = 'Nt3sHPgWHjIBLUxl8MTJklacPwDnbuQ2';
    
}


?>