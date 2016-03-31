<?php

namespace mod_kom_peerla;

/**
 * Description of CourseTopicSelectOptionHelper
 *
 * @author Christoph Bohr
 */
class CourseTopicSelectOptionHelper {
	
	protected $selectedId = 0;
	
	/**
	 * Get all select options for the given topics and there subtopics
	 * 
	 * @param \mod_kom_peerla\CourseTopic[] $topics
	 */
	public function getSelectOptions(array $topics, $selectedId=0){
		
		$html = '';
		$this->selectedId = $selectedId;
		
		foreach($topics as $topic){
			$html .= $this->getTopicHtml($topic);
		}
		
		return $html;
	}
	
	protected function getTopicOptionString(CourseTopic $topic, $level=0){
		
		$levelString = '';
		for ($i = 0; $i<$level; $i++){
			$levelString .= '-';
		}
		
		if ($levelString){
			$levelString .= ' ';
		}
		
		$selected = '';
		if ($this->selectedId == $topic->getTopicId()){
			$selected = ' selected="selected"';
		}
		
		$html = '<option'.$selected.' value="'.$topic->getTopicId().'">';
		$html .= $levelString;
		$html .= $topic->getName();
		$html .= '</option>';
		
		return $html;
	}
	
	protected function getTopicHtml(CourseTopic $topic, $level=0){
		$html = $this->getTopicOptionString($topic, $level);
		foreach ($topic->getSubTopics() as $subTopic){
			$html .= $this->getTopicHtml($subTopic, $level+1);
		}
		return $html;
	}
}
