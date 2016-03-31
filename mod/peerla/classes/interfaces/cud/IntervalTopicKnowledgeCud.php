<?php

namespace mod_kom_peerla;

/**
 * Create, update, delete operations for interval knowledge estimations
 * 
 * @author Christoph Bohr
 */
interface IntervalTopicKnowledgeCud {
	
	/**
	 * Create a persistent object
	 * 
	 * Save a new object to the persistence. Returns null, if the object could
	 * not be saved.
	 * 
	 * @param \mod_kom_peerla\IntervalCourseTopicKnowledge $object Object which will be updated/created
	 * @return int|null Id value for the saved object
	 */
	function create(IntervalCourseTopicKnowledge $object);
	
	/**
	 * Update the persently saved data of the object.
	 * 
	 * Returns true, if the object could be saved. False otherwise.
	 * 
	 * @param IntervalCourseTopicKnowledge $object Object to be updated
	 * @return bool Success of updating the object
	 */
	function update(IntervalCourseTopicKnowledge $object);
	
	/**
	 * Delete an object from persistence.
	 * 
	 * Returns true, if the object was deleted. False otherwise.
	 * 
	 * @param IntervalCourseTopicKnowledge $object Object which will be deleted
	 * @return bool Success of updating the object
	 */
	function delete(IntervalCourseTopicKnowledge $object);
	
	/**
	 * Replace the existing topic knowledge estimations.
	 * 
	 * Compares all IntervalCourseTopicKnowledge objects and only changes the existing 
	 * estimations, that have changed. Will also update all sub topic 
	 * estimations.
	 * 
	 * @param IntervalCourseTopicKnowledge[] $knowledgeEstimation Topic knowledge estimations
	 */
	function updateTopicsKnowledge(array $knowledgeEstimation);
}
