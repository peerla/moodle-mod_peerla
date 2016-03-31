<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/CourseTopic.php';

/**
 *
 * @author Christoph Bohr
 */
interface CourseTopicKnowledge extends CourseTopic {
	
	
	/**
	 * Get an array of sub topics for this topic.
	 * 
	 * @return CourseTopicKnowledge[] Array of subtopics
	 */
	function getSubTopics();
	
	/**
	 * Get the estimation for this topic.
	 * 
	 * @return int Estimation between 0 and 100
	 */
	function getEstimation();
	
	/**
	 * Get the user id of the user who estimated this topic.
	 * 
	 * @return int User id
	 */
	function getEstimationUserId();
	
	/**
	 * Returns the timestamp of the knowledge estimation.
	 * 
	 * @return int Unix timestamp
	 */
	function getEstimationTimestamp();
}