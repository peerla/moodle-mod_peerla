<?php
namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataViewProvider.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbCourseTopicDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbKnowledgeGoalDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbTimeInvestmentDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbPreCourseKnowledgeDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbUserDataFetcher.php';
require_once realpath(__DIR__).'/BasicDataViewNode.php';
require_once realpath(__DIR__).'/ChildSumDataViewNode.php';

/**
 * Description of TimeInvestmentOwnInvestmentDataProvider
 *
 * @author Christoph Bohr
 */
class TimeInvestmentOwnInvestmentDataProvider implements DataViewProvider {
	
	protected $courseId;
	protected $userId;
	protected $db;
	
	protected $goalFetcher;
	protected $investmentFetcher;
	protected $currentKnowledgeFetcher;
	protected $topicFetcher;
	protected $userFetcher;
	protected $preCourseKnowFetcher;
	
	/**
	 * 
	 * @param \moodle_database $db
	 * @param int $courseId Course id for the data
	 * @param int $userId User id to compare the data against
	 */
	function __construct(\moodle_database $db, $courseId, $userId) {
		$this->db = $db;
		$this->courseId = $courseId;
		$this->userId = $userId;
	}
	
	public function getDataView() {
		return $this->getRemainingTimeDataView();
	}
	
	/**
	 * Get the root data view node for the compareson of the actually invested 
	 * time of the user and the computed remaining time.
	 * 
	 * The remaining time for each topic is calculated as:
	 * [actual time investment] / ([current knowledge] - [start knowledge]) * ([knowledge goal] - [start knowledge])
	 * 
	 * @return \mod_kom_peerla\DataViewTreeNode
	 */
	protected function getRemainingTimeDataView(){
		$topicProvider = $this->getDbCourseTopicDataFetcher();
		$topics = $topicProvider->getCourseTopicsDbData($this->courseId,$this->userId);
		
		$rootNode = new ChildSumDataViewNode();
		$rootNode->setDisplayChildData(true);
		$rootNode->setDisplayOwnData(true);
		$rootNode->setLabel('Alle Themen');
		$rootNode->setDescriptionText('Zeitinvestment über alle Themenbereiche.');
		
		foreach($topics as $topic){
			$rootNode->addChildNode($this->getTopicChildDataViews($topic));
		}
		
		return $rootNode;
	}
	
	/**
	 * Build the data view tree structure recursivly.
	 * 
	 * @param \mod_kom_peerla\CourseTopic $topic
	 * @return DataViewTreeNode
	 */
	protected function getTopicChildDataViews(CourseTopic $topic){
		
		$subTopics = $topic->getSubTopics();
		if (count($subTopics) == 0){
			return $this->getNewLeafNode($topic);
		}
		
		$node = $this->getNewLeafNode($topic);
		foreach ($subTopics as $subTopic){
			$node->addChildNode($this->getTopicChildDataViews($subTopic));
		}
		
		return $node;
	}
	
	/**
	 * Get a new leaf node for the given topic.
	 * 
	 * @param \mod_kom_peerla\CourseTopic $topic
	 * @return \mod_kom_peerla\DataViewTreeNode
	 */
	protected function getNewLeafNode(CourseTopic $topic){
		
		$node = new BasicDataViewNode();
		$node->setDisplayChildData(TRUE);
		$node->setDisplayOwnData(TRUE);
		$node->setLabel($topic->getName());
		
		$description = 'Zeitinvestment im Themenbereich ';
		$description .= '"'.$topic->getName().'".';
		$node->setDescriptionText($description);
		
		$investedTime = $this->getCurrentUserTopicTimeInvestment($topic->getTopicId());
		$remainingTime = $this->getRemainingTimeInvestment($topic->getTopicId());
		$avgTime = $this->getAvgTimeInvestment($topic->getTopicId());

		//current user time investment
		$userKnowDataPoint = new BasicDataPoint2D();
		$userKnowDataPoint->setXvalueIsNominal();
		$userKnowDataPoint->setValueX('eigenes Zeitinvestment');
		$userKnowDataPoint->setValueY($this->formatTimeValue($investedTime));
		$node->addDataPoint($userKnowDataPoint);
		
		//avg time investment
		$avgTimeDataPoint = new BasicDataPoint2D();
		$avgTimeDataPoint->setXvalueIsNominal();
		$avgTimeDataPoint->setValueX('durchschnittliches Zeitinvestment anderer Teilnehmer');
		$avgTimeDataPoint->setValueY($this->formatTimeValue($avgTime));
		$node->addDataPoint($avgTimeDataPoint);

		//average knowledge of other participants
		$avgKnowDataPoint = new BasicDataPoint2D();
		$userKnowDataPoint->setXvalueIsNominal();
		$avgKnowDataPoint->setValueX('Hochrechnung verbleibende Zeit für Zielsetzung');
		$avgKnowDataPoint->setValueY($this->formatTimeValue($remainingTime));
		$node->addDataPoint($avgKnowDataPoint);
		
		return $node;
	}
	
	protected function getAvgTimeInvestment($topicId){
		$userFetcher = $this->getUserDataFetcher();
		$activeUsers = $userFetcher->getActiveCourseUsers($this->courseId);
		
		$investmentFetcher = $this->getDbTimeInvestmentDataFetcher();
		
		$investSum = 0;
		$userCount = 0;
		
		foreach($activeUsers as $userId){
			if ($userId != $this->userId){
				$investSum += $investmentFetcher->getCalculatedTimeInvestmentForTopicAndUser(
									$this->courseId, $userId, $topicId);
				$userCount++;
			}
		}
		
		if ($userCount == 0){
			return 0;
		}
		
		return $investSum / $userCount;
	}
	
	protected function getCurrentUserTopicTimeInvestment($topicId){
		$investmentFetcher = $this->getDbTimeInvestmentDataFetcher();
		$investment = $investmentFetcher->getCalculatedTimeInvestmentForTopicAndUser(
							$this->courseId, $this->userId, $topicId);
		return $investment;
	}
	
	protected function getRemainingTimeInvestment($topicId){
		$knowledgeFetcher = $this->getCurrentKnowledgeDataFetcher();
		$knowledge = $knowledgeFetcher->getUserTopicKnowledge(
				$this->courseId,$topicId,$this->userId);
		
		$goalFetcher = $this->getDbCourseKnowledgeGoalDataFetcher();
		$goal = $goalFetcher->getTopicKnowledgeGoalForUser(
				$this->courseId, $this->userId, $topicId);
		
		$invesetedTime = $this->getCurrentUserTopicTimeInvestment($topicId);
		
		$preKnowFetcher = $this->getPreCourseKnowledgeDataFetcher();
		$startingKnowledge = $preKnowFetcher->getUserTopicKnowledge($this->courseId, $topicId, $this->userId);
		
		if ($knowledge >= $goal){
			return 0;
		}
		
		if ($startingKnowledge == $knowledge || $startingKnowledge == $goal){
			return 0;
		}
		
		if ($startingKnowledge > $knowledge || $startingKnowledge > $goal){
			return null;
		}
		
		if (!$invesetedTime){
			return null;
		}
		
		$remaining = $invesetedTime / ($knowledge - $startingKnowledge) 
						* ($goal - $startingKnowledge); 
		
		return $remaining;
	}
	
	protected function formatTimeValue($value){
		return $value/60;
	}
	
	/**
	 * Set a DbCurrentKnowledgeDataFetcher object.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbCurrentKnowledgeDataFetcher $provider
	 */
	public function setCurrentKnowledgeDataFetcher(DbCurrentKnowledgeDataFetcher $fetcher){
		$this->currentKnowledgeFetcher = $fetcher;
	}
	
	/**
	 * Get the existing DbCurrentKnowledgeDataFetcher object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return DbCurrentKnowledgeDataFetcher
	 */
	public function getCurrentKnowledgeDataFetcher(){
		
		if (!isset($this->currentKnowledgeFetcher)){
			$this->currentKnowledgeFetcher = new CachedDbCurrentKnowledgeDataFetcher($this->db);
		}
		
		return $this->currentKnowledgeFetcher;
	}
	
	/**
	 * Set a DbKnowledgeGoalDataFetcher object.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbKnowledgeGoalDataFetcher $provider
	 */
	public function setDbCourseKnowledgeGoalDataFetcher(DbKnowledgeGoalDataFetcher $fetcher){
		$this->goalFetcher = $fetcher;
	}
	
	/**
	 * Get the existing DbKnowledgeGoalDataFetcher object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return DbKnowledgeGoalDataFetcher
	 */
	public function getDbCourseKnowledgeGoalDataFetcher(){
		
		if (!isset($this->goalFetcher)){
			$this->goalFetcher = new CachedDbKnowledgeGoalDataFetcher($this->db);
		}
		
		return $this->goalFetcher;
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
	
	/**
	 * Set a DbTimeInvestmentDataFetcher object.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbTimeInvestmentDataFetcher $provider
	 */
	public function setDbTimeInvestmentDataFetcher(DbTimeInvestmentDataFetcher $fetcher){
		$this->investmentFetcher = $fetcher;
	}
	
	/**
	 * Get the existing DbTimeInvestmentDataFetcher object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return DbTimeInvestmentDataFetcher
	 */
	public function getDbTimeInvestmentDataFetcher(){
		
		if (!isset($this->investmentFetcher)){
			$this->investmentFetcher = new CachedDbTimeInvestmentDataFetcher($this->db);
			$this->investmentFetcher->setDbCourseTopicDataFetcher(
					$this->getDbCourseTopicDataFetcher());
		}
		
		return $this->investmentFetcher;
	}
	
	/**
	 * Set a DbUserDataFetcher object.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbUserDataFetcher $provider
	 */
	public function setUserDataFetcher(DbUserDataFetcher $fetcher){
		$this->userFetcher = $fetcher;
	}
	
	/**
	 * Get the existing DbUserDataFetcher object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return DbUserDataFetcher
	 */
	public function getUserDataFetcher(){
		
		if (!isset($this->userFetcher)){
			$this->userFetcher = new CachedDbUserDataFetcher($this->db);
		}
		
		return $this->userFetcher;
	}
	
	/**
	 * Set a DbPreCourseKnowledgeDataFetcher object.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbPreCourseKnowledgeDataFetcher $provider
	 */
	public function setPreCourseKnowledgeDataFetcher(DbPreCourseKnowledgeDataFetcher $fetcher){
		$this->preCourseKnowFetcher = $fetcher;
	}
	
	/**
	 * Get the existing DbPreCourseKnowledgeDataFetcher object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return \mod_kom_peerla\DbPreCourseKnowledgeDataFetcher
	 */
	public function getPreCourseKnowledgeDataFetcher(){
		
		if (!isset($this->preCourseKnowFetcher)){
			$this->preCourseKnowFetcher = new CachedDbPreCourseKnowledgeDataFetcher($this->db);
		}
		
		return $this->preCourseKnowFetcher;
	}
}
