<?php

namespace mod_kom_peerla;

/**
 * Create, update, delete operations for knowledge goals
 * 
 * @author Christoph Bohr
 */
interface TopicKnowledgeGoalCud {
	
	/**
	 * Create a persistent object
	 * 
	 * Save a new object to the persistence. Returns null, if the object could
	 * not be saved.
	 * 
	 * @param CourseTopicKnowledge $object Object which will be updated/created
	 * @return int|null Id value for the saved object
	 */
	function create(CourseTopicKnowledge $object);
	
	/**
	 * Update the persently saved data of the object.
	 * 
	 * Returns true, if the object could be saved. False otherwise.
	 * 
	 * @param CourseTopicKnowledge $object Object to be updated
	 * @return bool Success of updating the object
	 */
	function update(CourseTopicKnowledge $object);
	
	/**
	 * Delete an object from persistence.
	 * 
	 * Returns true, if the object was deleted. False otherwise.
	 * 
	 * @param CourseTopicKnowledge $object Object which will be deleted
	 * @return bool Success of updating the object
	 */
	function delete(CourseTopicKnowledge $object);
	
	/**
	 * Replace the existing topic knowledge goal of the given topic.
	 * 
	 * Compares all CourseTopicKnowledge objects and only changes the existing 
	 * estimations, that have changed. Will also update all sub topic 
	 * estimations.
	 * 
	 * @param CourseTopicKnowledge[] $newKnowledgeGoals Array of new CourseTopicKnowledge objects
	 */
	public function updateTopicKnowledgeGoals(array $newKnowledgeGoals);
}
