<?php

/**
 * Description of block_kom_peerla
 *
 * @author Christoph Bohr
 */
class block_kom_peerla extends \block_base{
	
	protected $courseParticipant;
	
	public function init() {
        $this->title = get_string('kom_peerla', 'block_kom_peerla');
    }
	
	protected function userHasFinishedCoursePlaning($userId){
		
	}
	
	protected function userHasRunningInterval($userId){
		
	}
	
	protected function getCurrentCourseId(){
		global $COURSE;
		return $COURSE->id;
	}
	
	protected function getCurrentUserId(){
		global $USER;
		return $USER->id;
	}
	
	protected function getPeerLaIncludePath(){
		global $CFG;
		return $CFG->dirroot.'/mod/peerla/';
	}
	
	protected function getPeerLaUrl($relativePath, $parameters=array()){
		global $CFG;
		$url = $CFG->wwwroot.'/mod/peerla/';
		$url .= $relativePath;
		$url .= '?courseId='.$this->getCurrentCourseId();
		
		foreach($parameters as $key => $value){
			$url .= '&amp;'.$key.'='.$value;
		}
		
		return $url;
	}
	
	/**
	 * 
	 * @param int $userId
	 * @return \mod_kom_peerla\LearningInterval
	 */
	protected function getCourseParticipant($courseId, $userId){
		if (!isset($this->courseParticipant)){
			
			require_once $this->getPeerLaIncludePath()
					.'/classes/implementations/factories/LazyLoadingCourseParticipantFactory.php';
			
			$db = $this->getDbObject();
			
			$factory = new \mod_kom_peerla\LazyLoadingCourseParticipantFactory($db);
			$this->courseParticipant = $factory->getCourseParticipant($courseId, $userId);
		}
		
		return $this->courseParticipant;
	}
	
	protected function getDbObject(){
		global $DB;
		return $DB;
	}
	
	public function get_content() {
		
		if ($this->content !== null) {
			return $this->content;
		}
		
		$participant = $this->getCourseParticipant(
							$this->getCurrentCourseId(), $this->getCurrentUserId());
		
		$this->content = new stdClass;
		$this->content->footer = '<a href="'.$this->getPeerLaUrl('view.php').'">Zum Lernplaner</a>';
		
		$planing = $participant->getCoursePlaning();
		if (is_null($planing) || !$planing->userHasFinishedPlaning()){
			$this->content->text = 'Du hast deine Kursplanung noch nicht abgeschlossen.';
			return $this->content;
		}
		
		$interval = $participant->getCurrentLearningInterval();
		
		if (is_null($interval) || !$interval->isRunning()){
			$this->content->text = 'Bitte plan dein nächstes Lernintervall.';
			return $this->content;
		}

		$this->content->text   = $this->getIntervalGoalHtml($interval);

		return $this->content;
	}
	
	protected function getIntervalGoalHtml($interval){
		
		$html = '';
		$goals = $interval->getIntervalGoals();
		$openGoals = array();
		
		foreach($goals as $goal){
			if ($goal->getStatus() == 'open'){
				$openGoals[] = $goal;
			}
		}
		
		foreach($openGoals as $goal){
			$html .= '<div>'.$goal->getGoalShortText().'</div>';
		}
		
		return $html;
	}
	
}
