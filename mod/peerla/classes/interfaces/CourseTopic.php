<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CourseTopic {
	
	/**
	 * Get the unique database id of the course.
	 * 
	 * @return int Course id
	 */
	function getCourseId();
	
	/**
	 * Get the unique id of an topic
	 * 
	 * @return int Topic id
	 */
	function getTopicId();
	
	/**
	 * Get the name of the topic
	 * 
	 * @return string Name of the topic
	 */
	function getName();
	
	/**
	 * Get an array of sub topics for this topic.
	 * 
	 * @return CourseTopic[] Array of subtopics
	 */
	function getSubTopics();
	
	/**
	 * Returns if the topic was created by an participant or by the teacher.
	 * 
	 * @return bool True if created by an participant, false if created by teacher
	 */
	function isParticipantGenerated();
	
	/**
	 * Get the user id of the topics creator.
	 * 
	 * @return int|null Creators user id, null if created by teacher.
	 */
	function getCreatorUserId();
	
	/**
	 * Returns if the topic is private or public.
	 * 
	 * @return bool true for private, false otherwise
	 */
	function isPrivate();
	
	/**
	 * Returns the id of the parent topic.
	 * 
	 * @return int|null Parent topic id
	 */
	function getParentId();
	
	/**
	 * Get the parent topic object, if one exists.
	 * 
	 * @return CourseTopic|null Parent CourseTopic object
	 */
	function getParentTopic();
	
}
