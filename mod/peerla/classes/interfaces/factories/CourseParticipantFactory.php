<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CourseParticipantFactory {
	
	/**
	 * Get the CourseParticipant object for the given course and user.
	 * 
	 * Will return null, if the course or user doesn't exist or if the user isn't
	 * participating in the course.
	 * 
	 * @param int $courseId Unique id of the course
	 * @param int $userId Unique id of the user
	 * @return \mod_kom_peerla\CourseParticipant Course participant
	 */
	function getCourseParticipant($courseId, $userId);
	
	/**
	 * Get all participants of a specific course.
	 * 
	 * @param int $courseId Unique db id of the course
	 * @return \mod_kom_peerla\CourseParticipant[] Course participants 
	 */
	function getAllParticipantsForCourse($courseId);
	
}
