<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/LazyLoadingIntervalKnowledge.php';

/**
 * Description of LazyLoadingPreCourseKnowledge
 *
 * @author Christoph Bohr
 */
class LazyLoadingPreCourseKnowledge extends LazyLoadingIntervalKnowledge{
	
	public function getSubTopics() {
		
		if (!$this->subTopicsLoaded && isset($this->topicFactory)){
			$this->setSubTopics($this->topicFactory->getParticipantPreCourseKnowledgeSubTopics(
						$this->getTopicId(), $this->getEstimationUserId(), $this->getCourseId()
			));
			$this->subTopicsLoaded = true;
		}
		
		return parent::getSubTopics();
	}
	
}
