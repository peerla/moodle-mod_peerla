<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/Course.php';

/**
 * Description of DbCourse
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourse implements Course {
	
	protected $courseId;
	protected $fullname;
	protected $shortname;
	protected $startdate;
	
	protected $currentCourse;
	protected $participantFactory;
	
	protected $participants;
	protected $currentParticipants;
	protected $formerParticipants;
	
	protected $allParticipantsLoaded = false;

	/**
	 * 
	 * @param int|string $courseId Db course id
	 * @param \mod_kom_peerla\CourseParticipantFactory $participantFactory Participant factory for lazy loading
	 */
	public function __construct($courseId, CourseParticipantFactory $participantFactory) {
		$this->courseId = $courseId;
		$this->participantFactory = $participantFactory;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getParticipant($userId) {
		if (!isset($this->participants[$userId])){
			$this->participants[$userId] = $this->participantFactory
					->getCourseParticipant($this->courseId, $userId);
		}
		
		return $this->participants[$userId];
	}

	public function getParticipants() {
		if (!$this->allParticipantsLoaded){
			$this->participants = $this->participantFactory
					->getAllParticipantsForCourse($this->courseId);
			
			$this->allParticipantsLoaded = true;
		}
		
		return $this->participants;
	}

	public function isActiveCourse() {
		return $this->currentCourse;
	}

	public function getName() {
		return $this->fullname;
	}
	
	/**
	 * Set the course name.
	 * 
	 * @param string $name
	 */
	public function setName($name){
		$this->fullname = $name;
	}
	
	/**
	 * Set if the course is currently running/active.
	 * @param bool $isCurrentCourse
	 */
	public function setIsActiveCourse($active=true){
		$this->currentCourse = $active;
	}

	public function getShortName() {
		return $this->shortname;
	}
	
	/**
	 * Set the short name
	 * 
	 * @param string $shortName
	 */
	public function setShortName($shortName){
		$this->shortname = $shortName;
	}

	public function getStartDate() {
		return $this->startdate;
	}
	
	/**
	 * Set the start date
	 * 
	 * @param string $startDate
	 */
	public function setStartDate($startDate){
		$this->startdate = $startDate;
	}

}
