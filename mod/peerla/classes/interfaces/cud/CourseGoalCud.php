<?php

namespace mod_kom_peerla;

/**
 * Create, update, delete operations for CourseGoal objects
 * 
 * @author Christoph Bohr
 */
interface CourseGoalCud {
	
	/**
	 * Create a persistent object
	 * 
	 * Save a new object to the persistence. Returns null, if the object could
	 * not be saved.
	 * 
	 * @param CourseGoal $object Object which will be updated/created
	 * @return int|null Id value for the saved object
	 */
	function create(CourseGoal $object);
	
	/**
	 * Update the persently saved data of the object.
	 * 
	 * Returns true, if the object could be saved. False otherwise.
	 * 
	 * @param CourseGaol $object Object to be updated
	 * @return bool Success of updating the object
	 */
	function update(CourseGaol $object);
	
	/**
	 * Delete an object from persistence.
	 * 
	 * Returns true, if the object was deleted. False otherwise.
	 * 
	 * @param CourseGoal $object Object which will be deleted
	 * @return bool Success of updating the object
	 */
	function delete(CourseGoal $object);
	
	/**
	 * Replace the existing course goals of one user with the given goals.
	 * 
	 * Compares all CourseGoal objects and only changes the existing goals,
	 * that have changed.
	 * 
	 * @param int $userId User id
	 * @param int $courseId Course id
	 * @param CourseGoal[] $newGoals Array of new CourseGoal objects
	 */
	public function updateAllUserCourseGoals($userId, $courseId, array $newGoals);
}
