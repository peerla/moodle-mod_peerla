<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/CourseTopicKnowledge.php';

/**
 *
 * @author Christoph Bohr
 */
interface IntervalCourseTopicKnowledge extends CourseTopicKnowledge {
	
	/**
	 * Get the interval id
	 * 
	 * @return int interval id
	 */
	public function getIntervalId();
	
	/**
	 * Set the interval id
	 * 
	 * @param type $intervalId
	 */
	public function setIntervalId($intervalId);
	
	
	/**
	 * Get an array of sub topics for this topic.
	 * 
	 * @return IntervalCourseTopicKnowledge[] Array of subtopics
	 */
	function getSubTopics();
	
}
