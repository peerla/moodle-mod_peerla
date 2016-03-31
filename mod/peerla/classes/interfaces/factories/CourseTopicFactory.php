<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CourseTopicFactory {
	
	/**
	 * Get the topic with the given id.
	 * 
	 * @param int $topicId Topic id
	 * @return \mod_kom_peerla\CourseTopic|null CourseTopic object
	 */
	function getTopic($topicId);
	
	/**
	 * Get all non user generated course topics of the highest hierarchy level.
	 * 
	 * @return \mod_kom_peerla\CourseTopic[] Course topic array
	 */
	function getBaseCourseTopcis($courseId);
	
	/**
	 * Get all public course topics (user-generated and base) of the highest 
	 * hierarchy level.
	 * 
	 * @return \mod_kom_peerla\CourseTopic[] Course topic array
	 */
	function getPublicCourseTopics($courseId);
	
	/**
	 * Get all user-generated topics of the highest hierarchy level, 
	 * which are not visible to all participants.
	 * 
	 * @return \mod_kom_peerla\CourseTopic[] Course topic array
	 */
	function getAllNonPublicCourseTopics($courseId);
	
	/**
	 * Get all topics of the highest hierarchy level, which are visible to 
	 * the specifc participant.
	 * 
	 * @return \mod_kom_peerla\CourseTopicKnowledge[] Course topic array
	 */
	function getCourseTopicsVisibleToParticipant($courseId, $userId);
	
	/**
	 * Get the topic knowledge goal for a specific topic and user.
	 * 
	 * This method will return the topic, even if no estimation by the user 
	 * has been done. You must check the estimation timestamp or estimation
	 * to determen, if the user has actually estimated the topic.
	 * 
	 * @param int $topicId Topic id
	 * @param int $userId User id
	 * @return \mod_kom_peerla\CourseTopicKnowledge|null Topic knowledge goal
	 */
	function getUserTopicKnowledgeGoal($topicId, $userId);
	
	/**
	 * Returns all sub topics of the given topic.
	 * 
	 * @param int $topicId Id of the parent topic
	 * @return \mod_kom_peerla\CourseTopic[] Course topic array
	 */
	function getAllSubTopics($topicId);
	
	/**
	 * Returns all sub topics of the given topic, which are visible to the 
	 * given user.
	 * 
	 * @param int $topicId Id of the parent topic
	 * @param int $userId User id
	 * @return \mod_kom_peerla\CourseTopic[] Course topic array
	 */
	function getAllSubTopicsVisibleToParticipant($topicId, $userId);
	
	/**
	 * Get the highest level of course topics with the knowledge estimation 
	 * for the given interval.
	 * 
	 * @param int $intervalId Id of the interval
	 * @return \mod_kom_peerla\IntervalCourseTopicKnowledge[] Array of EstimatedCourseTopic objects
	 */
	function getParticipantPreIntervalKnowledge($intervalId);
	
	/**
	 * Get the knowledge estimation of a user for the given interval and
	 * topic.
	 * 
	 * This method will return the topic, even if no estimation by the user 
	 * has been done. You must check the estimation timestamp or estimation
	 * to determen, if the user has actually estimated the topic.
	 * 
	 * @param int $topicId Topic id
	 * @param int $intervalId Interval id
	 * @return IntervalCourseTopicKnowledge|null Topic interval knowledge
	 */
	function getParticipantPreIntervalTopicKnowledge($topicId, $intervalId);
	
	/**
	 * Get the sub topics of the given topic with the knowledge estimations
	 * for the given interval.
	 * 
	 * @param int $topicId The parent topic id
	 * @param int $intervalId Id of the interval
	 * @return \mod_kom_peerla\IntervalCourseTopicKnowledge[] Array of EstimatedCourseTopic objects
	 */
	function getParticipantPreIntervalKnowledgeSubTopics($topicId, $intervalId);
}
