<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface IntervalGoalCud {
	
	/**
	 * Create a persistent object
	 * 
	 * Save a new object to the persistence. Returns null, if the object could
	 * not be saved.
	 * 
	 * @param LearningIntervalGoal $object Object which will be updated/created
	 * @param bool $createRecurseivly If true, one goal for each lowest level of subtopics will be created. If false only one goal for this topic will be created.
	 * @return int|null Id value for the saved object
	 */
	function create(LearningIntervalGoal $object, $createRecurseivly=false);
	
	/**
	 * Update the persently saved data of the object.
	 * 
	 * Returns true, if the object could be saved. False otherwise. Only 
	 * status and 
	 * 
	 * @param LearningIntervalGoal $object Object to be updated
	 * @return bool Success of updating the object
	 */
	function update(LearningIntervalGoal $object);
	
	/**
	 * Delete an object from persistence.
	 * 
	 * Returns true, if the object was deleted. False otherwise.
	 * 
	 * @param LearningIntervalGoal $object Object which will be deleted
	 * @return bool Success of updating the object
	 */
	function delete(LearningIntervalGoal $object);
	
	/**
	 * Updates the goal setting timestamps of all intervals for which
	 * a goal has been set/changed. 
	 * 
	 * @return boolean
	 */
	public function updateIntervalGoalSettingTimestamps();
	
	
	/**
	 * Discards all intervals for the goal setting timestamps.
	 * 
	 * This method can be called after update operations in which the interval
	 * timestamp for goal updates should NOT be changed. All 
	 * preceding operations will not result in a timestamp update. All following
	 * operation will again mark the new intervals for update.
	 */
	public function discardAllGoalSettingIntervals();
}
