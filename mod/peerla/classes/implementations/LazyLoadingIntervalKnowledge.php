<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/IntervalCourseTopicKnowledge.php';
require_once realpath(__DIR__).'/BasicCourseTopicKnowledge.php';

/**
 * Description of LazyLoadingIntervalKnowledge
 *
 * @author Christoph Bohr
 */
class LazyLoadingIntervalKnowledge extends BasicCourseTopicKnowledge implements IntervalCourseTopicKnowledge {
	protected $topicFactory;
	protected $subTopicsLoaded = false;
	protected $intervalId;
	
	public function getIntervalId(){
		return $this->intervalId;
	}

	public function setIntervalId($intervalId){
		$this->intervalId = $intervalId;
	}

	public function setCourseTopicFactory(CourseTopicFactory $factory){
		$this->topicFactory = $factory;
	}

	public function getSubTopics() {
		$topics = parent::getSubTopics();
		
		if (count($topics) == 0 && !$this->subTopicsLoaded 
				&& isset($this->topicFactory)){
			$this->setSubTopics($this->topicFactory->
							getParticipantPreIntervalKnowledgeSubTopics(
									$this->getTopicId(),
									$this->getIntervalId()));
			$this->subTopicsLoaded = true;
		}
		
		return parent::getSubTopics();
	}
}
