<?php

namespace App\Models;

use PDO;
//use \App\Auth;
use \App\Models\Course;


/*
    Advisement Model
*/
class Advisement extends \Core\Model {

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
        gets all advisements for this user
    */
    public static function getAdvisements($user_id) {
        
        $sql = "
            SELECT * 
            FROM advisement
            JOIN advised_course USING (advisement_id)
            JOIN semester USING (semester_id)
            JOIN course USING (course_code)
            WHERE student = :user_id
            ORDER BY semester_id DESC";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        
        //$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            
        $stmt->execute();
        
        $advisements = []; 
        
        // loop through results and section data for each course into 
        // associated arrays from related table data
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            // check if this advisement was created and create if not
            if (!isset($advisements[$row['advisement_id']]))
                $advisements[$row['advisement_id']] = [
                    'advisement_id' => $row['advisement_id'],
                    'student' => $row['student'],
                    'advisor' => $row['advisor'],
                    'semester_id' => $row['semester_id'],
                    'date_begin' => $row['date_begin'],
                    'date_end' => $row['date_end'],
                    'is_open' => $row['is_open'],
                    'is_dirty' => $row['is_dirty'],
                    'advised_courses' => [] 
                ];
            // now that the advisement has been ensured as created, add the advised course data
            // if not already in there 
            if (!isset($advisements[$row['advisement_id']]['advised_courses'][$row['advised_course_id']])){
                $advisements[$row['advisement_id']]['advised_courses'][$row['advised_course_id']] = [
                    'course_code' => $row['course_code'],
                    'approved' => $row['approved'],
                    'credit_hours' => $row['credit_hours'],
                    'course_name' => $row['course_name']
                ];
            }
            
        }
        
        return $advisements;
        //return $stmt->fetchall();
        
        
    }
    
    public function addNewCourse() {
        /*
            expected data:
            new_advisement_id
            new_course
            checkbox new_approved
        */
        
        $this->validateCourse();
        
        if (empty($this->errors)) {
            
            $approved = false;
            if(isset($this->new_approved)) {
                $approved = true;
            }

            $sql = 'INSERT INTO advised_course ( course_code, advisement_id, approved) 
                            VALUES ( :course_code, :advisement_id, :approved)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            
            $this->new_course = strtoupper($this->new_course);

            $stmt->bindValue(':course_code', $this->new_course, PDO::PARAM_STR);
            $stmt->bindValue(':advisement_id', $this->new_advisement_id, PDO::PARAM_INT);
            $stmt->bindValue(':approved',$approved, PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                return static::setDirtyFlag($this->new_advisement_id, true);
            }
            
        }
        return false;
        
        
    }
    
    public function updateCourse() {
        /*
            expected data:
            new_advisement_id
            old_course
            new_course            
        */
        
        $this->validateCourse();
        
        if (empty($this->errors)) {            
            
            $sql = 'UPDATE advised_course SET course_code= :new_course WHERE advisement_id = :advisement_id AND course_code = :old_course';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            
            $this->change_course = strtoupper($this->new_course);

            $stmt->bindValue(':old_course', $this->old_course, PDO::PARAM_STR);
            $stmt->bindValue(':advisement_id', $this->new_advisement_id, PDO::PARAM_INT);
            $stmt->bindValue(':new_course',$this->new_course, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return static::setDirtyFlag($this->new_advisement_id, true);
            }
            
        }
        return false;        
        
    }
    
    public function removeCourse(){
        /*
            expected data:
            advisement_id
            courseCode           
        */
        if (empty($this->errors)) {
            
            if($this->radioRemove!="Yes"){
                return false;
            }else{
               $sql = 'DELETE FROM advised_course  WHERE advisement_id = :advisement_id AND course_code = :old_course';
            
                $db = static::getDB();
                $stmt = $db->prepare($sql);
            

                $stmt->bindValue(':old_course', $this->courseCode, PDO::PARAM_STR);
                $stmt->bindValue(':advisement_id', $this->edit_advisement_id, PDO::PARAM_INT);
            
                if ($stmt->execute()) {
                    return static::setDirtyFlag($this->edit_advisement_id, true);
                }
            }
            
            
            
        }
        return false; 
        
    }
    
    
    
    public function approveCourse() {
        /*
            expected data:
            advisement_id
            courseCode
        */
        if (empty($this->errors)) {

            if($this->radioRemove!="Yes"){
                return false;
            }else{
                $sql = 'UPDATE advised_course SET approved= 1 WHERE advisement_id = :advisement_id AND course_code = :courseCode';

                $db = static::getDB();
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':courseCode', $this->courseCode, PDO::PARAM_STR);
                $stmt->bindValue(':advisement_id', $this->edit_advisement_id, PDO::PARAM_INT);

                return $stmt->execute();
            }



        }
        return false;

    }
    

    public function validateCourse() {
        if (Course::courseExists($this->new_course) == false) {
            $this->errors[] = 'The Course specified does not exist.';
        }
        
        if (Course::courseExistsInAdvisement($this->new_course, $this->new_advisement_id)) {
            $this->errors[] = 'The Course has already been added to this advisement.';
        }
        
    }
    

    public static function setDirtyFlag($adv, $flag) {
        
        $sql = 'UPDATE advisement SET is_dirty = :is_dirty WHERE advisement_id = :advisement_id';
            
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        //$this->change_course = strtoupper($this->change_course);

        $stmt->bindValue(':is_dirty', $flag, PDO::PARAM_BOOL);
        $stmt->bindValue(':advisement_id', $adv, PDO::PARAM_INT);

        return $stmt->execute();

    }
    
    
    public function clearDirty() {
        return static::setDirtyFlag($this->edit_advisement_id, false);
    }
    
    
}// end class

?>