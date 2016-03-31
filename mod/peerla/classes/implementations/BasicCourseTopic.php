<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/CourseTopic.php';

/**
 * Description of BaseCourseTopic
 *
 * @author Christoph Bohr
 */
class BasicCourseTopic implements CourseTopic{
	
	protected $topicId;
	protected $parentId;
	protected $courseId;
	protected $name;
	protected $subTopics = array();
	protected $parentTopic;
	protected $creatorId;
	protected $participantGenerated = false;
	protected $private;
	
	public function __construct() {
		
	}


	/**
	 * 
	 * @param type $topicId
	 */
	public function setTopicId($topicId){
		$this->topicId = $topicId;
	}

	/**
	 * 
	 * @param type $name
	 */
	public function setName($name){
		$this->name = $name;
	}
	
	/**
	 * 
	 * @param array $topics
	 */
	public function setSubTopics(array $topics){
		$this->subTopics = $topics;
	}

	/**
	 * 
	 * @param type $userId
	 */
	public function setCreatorUserId($userId){
		$this->creatorId = $userId;
		$this->participantGenerated = true;
	}
	
	/**
	 * 
	 * @param type $private
	 */
	public function setIsPrivate($private=true){
		$this->private = $private;
	}

	public function getCreatorUserId() {
		return $this->creatorId;
	}

	public function getSubTopics() {
		return $this->subTopics;
	}

	public function getTopicId() {
		return $this->topicId;
	}

	public function isParticipantGenerated() {
		return $this->participantGenerated;
	}

	public function isPrivate() {
		return $this->private;
	}

	public function getName() {
		return $this->name;
	}

	public function getParentId() {
		return $this->parentId;
	}
	
	/**
	 * Set the id of the parent topic.
	 * 
	 * @param int $id
	 */
	public function setParentId($id){
		$this->parentId = $id;
	}

	public function getParentTopic() {
		return $this->parentTopic;
	}
	
	public function setParentTopic(CourseTopic $parentTopic){
		$this->parentTopic = $parentTopic;
	}

	public function getCourseId() {
		return $this->courseId;
	}
	
	/**
	 * Set the database course id.
	 * 
	 * @param int $courseId
	 */
	public function setCourseId($courseId) {
		$this->courseId = $courseId;
	}
	

}
