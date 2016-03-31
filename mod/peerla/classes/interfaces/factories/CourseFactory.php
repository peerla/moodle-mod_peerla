<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CourseFactory {
	
	/**
	 * The course object for the given id is created and returned
	 * 
	 * @param int $userId Unique id of the user
	 * @return \mod_kom_peerla\Course|null Course object, if it exists.
	 */
	function getCourse($courseId);
	
	/**
	 * Get the course object corresponding to the given module instance id.
	 * 
	 * @param int $moduleInstanceId Module instance id
	 * @return \mod_kom_peerla\Course Course object, if it exists.
	 */
	function getCourseFromModuleInstanceId($moduleInstanceId);
	
	/**
	 * Get the course object corresponding to the given course module id.
	 * 
	 * @param int $courseModuleId Module instance id
	 * @return \mod_kom_peerla\Course|null Course object, if it exists.
	 */
	function getCourseFromCourseModuleId($courseModuleId);
	
	/**
	 * Get all the currently running courses for the given user.
	 * 
	 * @param int $userId User id
	 * @return Course[] Current courses
	 */
	function getCurrentCoursesForUser($userId);
	
	/**
	 * Get all courses of the given user (current and finished)
	 * 
	 * @return Course[] Array of all courses
	 */
	function getCoursesForUser($userId);
}
