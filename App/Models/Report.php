<?php

namespace App\Models;
use PDO;
use \App\Auth;

class Report extends \Core\Model {

    public $errors = [];


    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }


	// Full list of Students for advisement (advised or not) for logged in advisor
	// Student Advisement List (past or present)

	public static function advisorList(){

		$advisor = Auth::getUser();

		     $sql = '
                    SELECT DISTINCT user_id, stu_fname,stu_lname,advisor
					FROM student
					JOIN advisement ON advisement.student = student.user_id
				    WHERE advisor=:advisor';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindParam(':advisor',$advisor->user_id, PDO::PARAM_STR);

		$stmt->execute();
        return $stmt->fetchAll();
	}


	// List of all students in an advisor's department
	// All Students in Department
	public static function advisorDeptStudents(){

		$advisor = Auth::getUser();

		$sql ="SELECT student.user_id, stu_fname,stu_lname,program_name,dept_name
				FROM student
				JOIN program USING (program_id)
				JOIN department USING (dept_id)";

		$db=static::getDB();
		$stmt=$db->prepare($sql);

		$stmt->bindParam(':advisor', $advisor->user_id, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchAll();


	}


	// All posts sent between advisor and student for logged in advisor, like a full chat history report
	// Post History/Log

	public static function allPosts(){

		$advisor = Auth::getUser();

		$sql="SELECT semester_id, post_date, user_id, content
				FROM post
				JOIN advisement USING (advisement_id)
				JOIN semester USING (semester_id)
			WHERE advisor = :advisor
			ORDER BY semester_id, post_date, post_id";

	 	$db=static::getDB();
		$stmt=$db->prepare($sql);

		$stmt->bindParam(':advisor', $advisor->user_id, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchAll();
	}


	// Display all open (not approved yet) advisements/ courses for current logged in advisor
	// Pending Approvals
	public static function isApproved(){
		$advisor = Auth::getUser();

		$sql ="SELECT user_id, stu_fname,stu_lname, course_code, course_name, approved
				FROM student
				JOIN advisement ON advisement.student = student.user_id
				JOIN advised_course USING (advisement_id)
				JOIN course USING (course_code)
		        WHERE advisor=:advisor_id AND approved = 0 ";


		$db=static::getDB();
		$stmt=$db->prepare($sql);

		$stmt->bindParam(':advisor_id', $advisor->user_id, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchAll();
	}

 } // CLOSE CLASS
