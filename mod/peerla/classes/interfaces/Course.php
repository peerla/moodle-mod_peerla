<?php

namespace mod_kom_peerla;

/**
 * 
 *
 * @author Christoph Bohr
 */
interface Course {
	
	/**
	 * Get the unique course id
	 * 
	 * @return string course id
	 */
	function getCourseId();
	
	/**
	 * Get the course (full-)name.
	 * 
	 * @return string Course name
	 */
	function getName();
	
	/**
	 * Get the course short name.
	 * 
	 * @return string Short name
	 */
	function getShortName();
	
	/**
	 * Get the course start date
	 * 
	 * @return string Date
	 */
	function getStartDate();
	
	/**
	 * Get all users which participate in this course.
	 * 
	 * @return CourseParticipant[] Participants array
	 */
	function getParticipants();
	
	/**
	 * Get the course participant with the given user id.
	 * 
	 * @param int $userId User id of the participant
	 * @return CourseParticipant|null Participant object
	 */
	function getParticipant($userId);
	
	
	/**
	 * Returns if the given is currently active.
	 * 
	 * @return bool True, if active, false otherwise
	 */
	function isActiveCourse();
	
}
