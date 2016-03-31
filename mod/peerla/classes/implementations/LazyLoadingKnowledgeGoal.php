<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/BasicCourseTopicKnowledge.php';

/**
 * Description of LazyLoadingEstimatedCourseTopic
 *
 * @author Christoph Bohr
 */
class LazyLoadingKnowledgeGoal extends BasicCourseTopicKnowledge{
	
	protected $topicFactory;
	protected $subTopicsLoaded = false;
	
	public function setCourseTopicFactory(CourseTopicFactory $factory){
		$this->topicFactory = $factory;
	}

	public function getSubTopics() {
		$topics = parent::getSubTopics();
		
		if (count($topics) == 0 && !$this->subTopicsLoaded){
			$this->setSubTopics($this->topicFactory->
							getAllSubTopicsVisibleToParticipant(
									$this->getTopicId(),
									$this->getEstimationUserId()));
			$this->subTopicsLoaded = true;
		}
		
		return parent::getSubTopics();
	}
	
}
