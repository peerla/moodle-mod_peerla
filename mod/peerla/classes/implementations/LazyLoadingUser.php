<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/User.php';

/**
 * Description of StandardUser
 *
 * @author Christoph Bohr
 */
class LazyLoadingUser implements User{
	
	protected $userId;
	protected $courseFactory;
	
	protected $currentCourses;
	protected $courses;
	
	protected $firstName;
	protected $lastName;
	protected $email;
	
	public function __construct($userId, CourseFactory $courseFactory) {
		$this->userId = $userId;
		$this->courseFactory = $courseFactory;
	}

	public function getCourses() {
		
		//lazy loading of courses
		if (!isset($this->courses)){
			$this->courses = $this->courseFactory->getCoursesForUser($this->userId);
			$this->setCurrentCourses($this->courses);
		}
		
		return $this->courses;
	}
	
	/**
	 * Use the given array of course objects to set all the current courses
	 * 
	 * @param \mod_kom_peerla\Course[] $courseArray Array of courses
	 */
	protected function setCurrentCourses($courseArray){
		
		$this->currentCourses = array();
		
		if (!isset($courseArray) || !is_array($courseArray)){
			return;
		}
		
		foreach($courseArray as $course){
			if ($course->isActiveCourse()){
				$this->currentCourses[] = $course;
			}
		}
	}

	public function getCurrentCourses() {
		
		//lazy loading of courses
		if (!isset($this->currentCourses)){
			$this->currentCourses = $this->courseFactory
					->getCurrentCoursesForUser($this->userId);
		}
		
		return $this->currentCourses;
	}

	public function getUserId() {
		return $this->userId;
	}
	
	public function getFirstName(){
		return $this->firstName;
	}
	
	/**
	 * Set the first name of the user
	 * 
	 * @param string $firstName
	 */
	public function setFirstName($firstName){
		$this->firstName = $firstName;
	}
	
	public function getLastName(){
		return $this->lastName;
	}
	
	/**
	 * Set the last name of the user
	 * 
	 * @param string $lastName
	 */
	public function setLastName($lastName){
		$this->lastName = $lastName;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	/**
	 * Set the email address of the user
	 * 
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

}
