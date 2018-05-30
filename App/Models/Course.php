<?php

namespace App\Models;

use PDO;
//use \App\Auth;

/*
    Semester Model
*/

class Course extends \Core\Model {

    /*
        list of errors
    */
    public $errors = [];
    
    /*
        contructor, accepts post data
        creates a new User object where
        you will be able to access the post values
        via dot notation, e.g. User.first_name
    */ 
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            // creates User.whateverpostvalue = whatever was sent
            $this->$key = $value;
        }
    }
    
    
    /*
        get Course by course code
    */
    public static function findCourseByCode($course_code) {

        
        $sql = "
            SELECT * 
            FROM course
            WHERE course_code = :course_code";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            
        $stmt->execute();
        
        return $stmt->fetch();
        
    }
    
    
    
    
    /*
        find if course exits in database 
    */
    public static function courseExists($course_code) {
        return static::findCourseByCode($course_code)  !== false;        
    }
    
    
    /*
        find if course already in advisement
    */
    public static function findCourseByAdvisement($course_code, $advisement_id) {
        
        $sql = "
            SELECT * 
            FROM advised_course
            WHERE course_code = :course_code AND advisement_id = :advisement_id";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        $stmt->bindParam(':advisement_id', $advisement_id, PDO::PARAM_INT);
        
        //$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            
        $stmt->execute();
        
        return $stmt->fetch();
        
    }
    
    public static function courseExistsInAdvisement($course_code, $advisement_id) {
        return static::findCourseByAdvisement($course_code, $advisement_id)  !== false;        
    }
    
    
}// end class

?>