<?php

namespace mod_kom_peerla;

/**
 * Description of LazyLoadingCourseTopic
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourseTopic extends BasicCourseTopic{
	
	protected $topicFactory;
	protected $parentLoaded = false;
	
	public function setCourseTopicFactory(CourseTopicFactory $factory){
		$this->topicFactory = $factory;
	}

	public function getSubTopics() {
		$topics = parent::getSubTopics();
		
		if (is_null($topics) && !is_null($this->topicFactory)){
			$this->setSubTopics($this->topicFactory->
					getAllSubTopics($this->getTopicId()));
		}
		
		return parent::getSubTopics();
	}
	
	public function getParentTopic() {
		
		if (!$this->parentId){
			return null;
		}
		
		if (!$this->parentLoaded){
			$this->setParentTopic(
					$this->topicFactory->getTopic($this->getParentId()));
			$this->parentLoaded = true;
		}
		
		return parent::getParentTopic();
	}
	
}
