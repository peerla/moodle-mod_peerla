<?php
namespace mod_kom_peerla;

class KnowledgeEstimationHtmlBuilder{
	
	protected $visibleTopicId;
	protected $visibleTopicParentIds = array();
	protected $visibleTopicChildIds = array();
	
	public function setOnlyShowFromTopicDownwards($topicId){
		$this->visibleTopicId = $topicId;
		$this->visibleTopicChildIds = array();
		$this->visibleTopicParentIds = array();
	}

	/**
	 * 
	 * @param \mod_kom_peerla\CourseTopicKnowledge $courseTopics
	 */
	protected function setVisibilityTopicIds(CourseTopicKnowledge $topic,$isChild=false){
		
		if ($isChild){
			$this->visibleTopicChildIds[] = $topic->getTopicId();
		}
		
		$isParent = false;
		if ($topic->getTopicId() == $this->visibleTopicId){
			$isChild = true;
		}
		
		foreach($topic->getSubTopics() as $subTopic){
			$subIsParent = $this->setVisibilityTopicIds($subTopic,$isChild);
			if ($subIsParent){
				$isParent = true;
			}
		}
		
		if ($isParent){
			$this->visibleTopicParentIds[] = $topic->getTopicId();
		}
		
		if ($topic->getTopicId() == $this->visibleTopicId){
			$isParent = true;
		}
		
		return $isParent;
	}

	/**
	 * Get the HTML for a topic knownledge estimation form element.
	 * 
	 * @param \mod_kom_peerla\CourseTopicKnowledge[] $courseTopics
	 * @return string
	 */
	function getTopicEstimationHtml($courseTopics){
		
		if (isset($this->visibleTopicId)){
			foreach($courseTopics as $subTopic){
				$this->setVisibilityTopicIds($subTopic);
			}
		}
		
		return $this->getHtml($courseTopics);
	}
	
	/**
	 * 
	 * 
	 * @param \mod_kom_peerla\CourseTopicKnowledge $courseTopics
	 * @return string
	 */
	protected function getListDisplayClass(CourseTopicKnowledge $topic){
		
		$displayClass = '';
		if (isset($this->visibleTopicId)){
			$displayClass = 'hiddenTopic';
			
			if ($topic->getTopicId() == $this->visibleTopicId){
				$displayClass = 'visibleTopic';
			}
			
			if (in_array($topic->getTopicId(), $this->visibleTopicChildIds)){
				$displayClass = 'visibleChildTopic';
			}
			
			if (in_array($topic->getTopicId(), $this->visibleTopicParentIds)){
				$displayClass = 'hiddenParentTopic';
			}
		}
		
		return $displayClass;
	}
	
	/**
	 * Get the HTML for a topic knownledge estimation form element.
	 * 
	 * @param \mod_kom_peerla\CourseTopicKnowledge[] $courseTopics
	 * @return string
	 */
	protected function getHtml($courseTopics){
		$html = '';

		if (count($courseTopics) == 0){
			return '';
		}
		
		$html .= '<ul class="courseTopicPrioList">';

		foreach($courseTopics as $topic){
			$displayClass = $this->getListDisplayClass($topic);
			$html .= '	<li class="'.$displayClass.'">';
			$html .= '		<span>'.$topic->getName().'</span>';

			$subtopics = $topic->getSubTopics();

			if (count($subtopics) > 0){
				$html .= '	<a class="showSubTopics" href="#">'
								.'<span class="glyphicon glyphicon-arrow-down"></span>'
								.get_string('btn_show_subtopics', 'peerla').'</a>';
				$html .= '	<a class="hideSubTopics" href="#">'
								.'<span class="glyphicon glyphicon-arrow-up"></span>'
								.get_string('btn_hide_subtopics', 'peerla').'</a>';
			}

			$html .= '		<div class="slider"></div>';
			$html .= '		<input type="hidden" class="sliderValue" '
					. '			name="topicEstimation['.$topic->getTopicId().']" '
					. '			value="'.$topic->getEstimation().'" />';

			if (count($subtopics) > 0){
				$html .= $this->getHtml($topic->getSubTopics());
			}

			$html .= '	</li>';
		}

		$html .= '</ul>';

		return $html;
	}
	
}