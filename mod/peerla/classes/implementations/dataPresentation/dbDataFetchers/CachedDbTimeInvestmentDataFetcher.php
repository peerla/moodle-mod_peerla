<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/CachedDbCourseTopicDataFetcher.php';
require_once realpath(__DIR__).'/../../../interfaces/dataPresentation/dbDataFetchers/DbTimeInvestmentDataFetcher.php';

/**
 * Description of CachedDbTimeInvestmentDataFetcher
 *
 * @author Christoph Bohr
 */
class CachedDbTimeInvestmentDataFetcher implements DbTimeInvestmentDataFetcher {
	
	protected $rawTimeData;
	protected $timeInvestment;
	protected $calculatedTimeInvestment;
	protected $db;
	protected $topicFetcher;
	protected $planedInvestment;
	protected $planedActionInvestment;
	protected $investedActionTime;
	
	function __construct(\moodle_database $db){
		$this->db = $db;
	}
	
	protected function getRawIntervalGoalData($courseId){
		
		if (!isset($this->rawTimeData)){
			$sql = "SELECT ig.id, ig.invested_time, ig.userid, ig.topicid,"
					. "		ig.planed_time_investment, ig.actionid, ig.status"
					. "	FROM {interval_goal} ig"
					. "	WHERE ig.courseid = :courseid";
			$params = array(
				'courseid' => $courseId
			);

			$this->rawTimeData = $this->db->get_records_sql($sql,$params);
		}
		
		return $this->rawTimeData;
	}
	
	
	public function getActionTimeInvestmentArray($courseId){
		
		if (!isset($this->investedActionTime[$courseId])){
			
			$investments = $this->getRawIntervalGoalData($courseId);
			
			if (!$investments){
				$this->investedActionTime[$courseId] = array();
				return $this->investedActionTime[$courseId];
			}
			
			foreach($investments as $investment){
				
				if ($investment->status == 'done'){
					if (!isset($this->investedActionTime[$courseId]
							[$investment->topicid][$investment->actionid][$investment->userid])){
						$this->investedActionTime[$courseId]
								[$investment->topicid][$investment->actionid][$investment->userid] = 0;
					}
				
					$this->investedActionTime[$courseId][$investment->topicid][$investment->actionid][$investment->userid]
							+= $investment->invested_time;
				}
			}
		}
		
		return $this->investedActionTime[$courseId];
	}
	
	public function getTopicActionTimeInvestmentArray($courseId,$topicId,$actionId){
		$investmentArray = $this->getActionTimeInvestmentArray($courseId);
		if (!isset($investmentArray[$topicId][$actionId])){
			return array();
		}
		return $investmentArray[$topicId][$actionId];
	}
	
	public function getAvgActionTimeInvestment($courseId, $topicId, $actionId, $minNumberOfUsers=5){
		$investmentArray = $this->getTopicActionTimeInvestmentArray(
				$courseId,$topicId,$actionId);
		
		$sum = 0;
		$userCount = 0;
		foreach($investmentArray as $userInvestment){
			$userCount++;
			$sum += $userInvestment;
		}
		
		if ($userCount < $minNumberOfUsers){
			return null;
		}
		return $sum / $userCount;
	}

	public function getPlanedActionTimeInvestmentArray($courseId){
		
		if (!isset($this->planedActionInvestment[$courseId])){
			
			$investments = $this->getRawIntervalGoalData($courseId);
			
			if (!$investments){
				$this->planedActionInvestment[$courseId] = array();
				return $this->planedActionInvestment[$courseId];
			}
			
			foreach($investments as $investment){
				
				if (!isset($this->planedActionInvestment[$courseId]
						[$investment->topicid][$investment->actionid][$investment->userid])){
					$this->planedActionInvestment[$courseId]
							[$investment->topicid][$investment->actionid][$investment->userid] = 0;
				}
				
				$this->planedActionInvestment[$courseId][$investment->topicid][$investment->actionid][$investment->userid]
						+= $investment->planed_time_investment;
			}
		}
		
		return $this->planedActionInvestment[$courseId];
	}
	
	public function getTopicActionPlanedTimeInvestmentArray($courseId,$topicId,$actionId){
		$investmentArray = $this->getPlanedActionTimeInvestmentArray($courseId);
		if (!isset($investmentArray[$topicId][$actionId])){
			return array();
		}
		return $investmentArray[$topicId][$actionId];
	}


	public function getAvgPlanedActionTimeInvestment($courseId, $topicId, $actionId, $minNumberOfUsers=5){
		$investmentArray = $this->getTopicActionPlanedTimeInvestmentArray(
				$courseId,$topicId,$actionId);
		
		$sum = 0;
		$userCount = 0;
		foreach($investmentArray as $userInvestment){
			$userCount++;
			$sum += $userInvestment;
		}
		
		if ($userCount < $minNumberOfUsers){
			return null;
		}
		
		return $sum / $userCount;
	}

	public function getPlanedTimeInvestmentArray($courseId){
		
		if (!isset($this->planedInvestment[$courseId])){
			
			$investments = $this->getRawIntervalGoalData($courseId);

			if (!$investments){
				$this->planedInvestment[$courseId] = array();
				return $this->planedInvestment[$courseId];
			}
			
			foreach($investments as $investment){
				
				if (!isset($this->planedInvestment[$courseId][$investment->userid][$investment->topicid])){
					$this->planedInvestment[$courseId][$investment->userid][$investment->topicid] = 0;
				}
				
				$this->planedInvestment[$courseId][$investment->userid][$investment->topicid]
						+= $investment->planed_time_investment;
			}
		}
		
		return $this->planedInvestment[$courseId];
	}


	public function getActualTimeInvestmentArray($courseId) {
		
		if (!isset($this->timeInvestment[$courseId])){
			
			$investments = $this->getRawIntervalGoalData($courseId);

			if (!$investments){
				$this->timeInvestment[$courseId] = array();
				return $this->timeInvestment[$courseId];
			}
			
			foreach($investments as $investment){
				$investedTime = 0;
				if ($investment->invested_time){
					$investedTime = $investment->invested_time;
				}
				
				if (!isset($this->timeInvestment[$courseId][$investment->userid][$investment->topicid])){
					$this->timeInvestment[$courseId][$investment->userid][$investment->topicid] = 0;
				}
				
				$this->timeInvestment[$courseId][$investment->userid][$investment->topicid]
						+= $investedTime;
			}
		}
		
		return $this->timeInvestment[$courseId];
	}

	public function getActualTimeInvestmentUserArray($courseId,$userId){
		$courseTimeInvesment = $this->getActualTimeInvestmentArray($courseId);
		if (!isset($courseTimeInvesment[$userId])){
			return array();
		}
		
		return $courseTimeInvesment[$userId];
	}
	
	public function getDirectTimeInvestmentForTopicAndUser($courseId, $userId, $topicId){
		$rawInvestmentData = $this->getActualTimeInvestmentUserArray($courseId,$userId);
		if (!isset($rawInvestmentData[$topicId])){
			return 0;
		}
		return $rawInvestmentData[$topicId];
	}

	public function getCalculatedTimeInvestmentForTopicAndUser($courseId, $userId, $topicId) {
		
		if (isset($this->calculatedTimeInvestment[$courseId][$userId][$topicId])){
			return $this->calculatedTimeInvestment[$courseId][$userId][$topicId];
		}
		
		$rawInvestmentData = $this->getActualTimeInvestmentUserArray($courseId,$userId);
		if (count($rawInvestmentData) == 0){
			return 0;
		}
		
		$topicFetcher = $this->getDbCourseTopicDataFetcher();
		$topics = $topicFetcher->getCourseTopicsDbData($courseId, $userId);
		
		if (count($topics) == 0){
			return 0;
		}
		
		$calculatedInvestment = 0;
		foreach($topics as $topic){
			$calculatedInvestment += $this->calculateTopicInvestement(
					$topic, 0, $topicId, $userId);
		}
		
		$this->calculatedTimeInvestment[$courseId][$userId][$topicId] = $calculatedInvestment;
		return $calculatedInvestment;
	}
	
	protected function calculateTopicInvestement(CourseTopic $topic, $investment, $targetTopicId, $userId){
		
		//target topic found -> add the direct topic investment to the parent 
		//investment and return it
		if ($topic->getTopicId() == $targetTopicId){
			
			$investment += $this->getChildTopicsInvestementPart($topic, $userId);
			
			return $investment;
		}
		
		$subTopics = $topic->getSubTopics();
		//end of a path widthout finding the target topic? -> return 0
		if (count($subTopics) == 0){
			return 0;
		}
		
		$currentTopicInvestment = $this->getDirectTimeInvestmentForTopicAndUser(
				$topic->getCourseId(), $userId, $topic->getTopicId());
		$investment = ($investment + $currentTopicInvestment) / count($subTopics);
		
		$returnInvestment = 0;
		foreach($topic->getSubTopics() as $subTopic){
			$returnInvestment += $this->calculateTopicInvestement(
					$subTopic, $investment, $targetTopicId, $userId);
		}
		return $returnInvestment;
	}
	
	protected function getChildTopicsInvestementPart(CourseTopic $topic, $userId){
		
		$investment = $this->getDirectTimeInvestmentForTopicAndUser(
						$topic->getCourseId(), $userId, $topic->getTopicId());
		
		$subTopics = $topic->getSubTopics();
		foreach($subTopics as $subTopic){
			$investment += $this->getChildTopicsInvestementPart($subTopic, $userId);
		}
		
		return $investment;
	}

	/**
	 * Set a DbCourseTopicDataFetcher object.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbCourseTopicDataFetcher $provider
	 */
	public function setDbCourseTopicDataFetcher(DbCourseTopicDataFetcher $fetcher){
		$this->topicFetcher = $fetcher;
	}
	
	/**
	 * Get the existing DbCourseTopicDataFetcher object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return DbCourseTopicDataFetcher
	 */
	public function getDbCourseTopicDataFetcher(){
		
		if (!isset($this->topicFetcher)){
			$this->topicFetcher = new CachedDbCourseTopicDataFetcher($this->db);
		}
		
		return $this->topicFetcher;
	}
}
