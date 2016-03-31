<?php

namespace mod_kom_peerla;

/**
 * Create, update, delete operations for interval knowledge estimations
 * 
 * @author Christoph Bohr
 */
interface LearningIntervalCud {
	
	/**
	 * Create a persistent object
	 * 
	 * Save a new object to the persistence. Returns null, if the object could
	 * not be saved.
	 * 
	 * @param \mod_kom_peerla\LearningInterval $object Object which will be updated/created
	 * @return int|null Id value for the saved object
	 */
	function create(LearningInterval $object);
	
	/**
	 * Update the persently saved data of the object.
	 * 
	 * Returns true, if the object could be saved. False otherwise.
	 * 
	 * @param \mod_kom_peerla\LearningInterval $object Object to be updated
	 * @return bool Success of updating the object
	 */
	function update(LearningInterval $object);
	
}
