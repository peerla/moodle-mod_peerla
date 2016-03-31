<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface PreCourseKnowledgeCud {
	
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
	
}
