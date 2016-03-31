<?php

namespace mod_kom_peerla;

/**
 * Create, update, delete operations for CoursePlaning objects
 *
 * @author Christoph Bohr
 */
interface CoursePlaningCud {
	
	/**
	 * Create a persistent object
	 * 
	 * Save a new object to the persistence. Returns null, if the object could
	 * not be saved.
	 * 
	 * @param CoursePlaning $planing Object which will be updated/created
	 * @return int|null Id value for the saved object
	 */
	function create(CoursePlaning $object);
	
	/**
	 * Update the persently saved data of the object.
	 * 
	 * Returns true, if the object could be saved. False otherwise.
	 * 
	 * @param CoursePlaning $goal Object to be updated
	 * @return bool Success of updating the object
	 */
	function update(CoursePlaning $object);
	
	/**
	 * Delete an object from persistence.
	 * 
	 * Returns true, if the object was deleted. False otherwise.
	 * 
	 * @param CoursePlaning $planing Object which will be deleted
	 * @return bool Success of updating the object
	 */
	function delete(CoursePlaning $object);
	
}
