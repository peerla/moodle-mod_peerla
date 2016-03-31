<?php
namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataViewProvider.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbCurrentKnowledgeDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbCourseTopicDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbKnowledgeGoalDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbUserDataFetcher.php';
require_once realpath(__DIR__).'/BasicDataViewNode.php';
require_once realpath(__DIR__).'/ChildAverageDataViewNode.php';

/**
 * Description of KnowledgeDataProvider
 *
 * @author Christoph Bohr
 */
class KnowledgeDataProvider implements DataViewProvider{
	protected $courseId;
	protected $userId;
	protected $db;
	
	protected $topicFetcher;
	protected $currentKnowledgeFetcher;
	protected $goalFetcher;
	protected $userFetcher;
	
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


	public function getDataView() {
		return $this->getCurrentKnowledgeAllUsersDataView();
	}
	
	/**
	 * Get the root data view node for the compareson of the current knowledge
	 * of the user to the average knowledge of alle users.
	 * 
	 * @return \mod_kom_peerla\DataViewTreeNode
	 */
	protected function getCurrentKnowledgeAllUsersDataView(){
		$topicProvider = $this->getDbCourseTopicDataFetcher();
		$topics = $topicProvider->getCourseTopicsDbData($this->courseId,$this->userId);
		
		$rootNode = new ChildAverageDataViewNode();
		$rootNode->setMinDisplayValueY(0);
		$rootNode->setMaxDisplayValueY(100);
		$rootNode->setDisplayChildData(true);
		$rootNode->setDisplayOwnData(true);
		$rootNode->setLabel('Alle Themen');
		$rootNode->setDescriptionText('Durchschnitt der Wissenstände über alle Themenbereiche.');
		/*
		$level1Node = new ChildAverageDataViewNode();
		$level1Node->setMinDisplayValueY(0);
		$level1Node->setMaxDisplayValueY(100);
		$level1Node->setDisplayChildData(true);
		$level1Node->setDisplayOwnData(false);
		$level1Node->setDescriptionText('Durchschnitt der Wissenstände über alle Themenbereiche.');
		$level1Node->setLabel('Alle Themen');
		*/
		foreach($topics as $topic){
			$rootNode->addChildNode($this->getTopicChildDataViews($topic));
		}
		
		//$rootNode->addChildNode($level1Node);
		
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
		
		$node = $this->getNewInnerTreeNode($topic);
		foreach ($subTopics as $subTopic){
			$node->addChildNode($this->getTopicChildDataViews($subTopic));
		}
		
		return $node;
	}
	
	/**
	 * Get a new (inner) tree node for the given topic.
	 * 
	 * @param \mod_kom_peerla\CourseTopic $label
	 * @return \mod_kom_peerla\DataViewTreeNode
	 */
	protected function getNewInnerTreeNode(CourseTopic $topic){
		
		$node = new ChildAverageDataViewNode();
		$node->setMinDisplayValueY(0);
		$node->setMaxDisplayValueY(100);
		$node->setDisplayChildData(true);
		$node->setDisplayOwnData(true);
		$node->setLabel($topic->getName());
		
		$description = 'Durchschnitt der Wissenstände im Themenbereiche ';
		$description .= '"'.$topic->getName().'".';
		$node->setDescriptionText($description);
		
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
		$node->setDisplayChildData(FALSE);
		$node->setDisplayOwnData(TRUE);
		$node->setMinDisplayValueY(0);
		$node->setMaxDisplayValueY(100);
		$node->setLabel($topic->getName());
		
		$description = 'Wissenstände im Themenbereich ';
		$description .= '"'.$topic->getName().'".';
		$node->setDescriptionText($description);

		//current user knowledge
		$userKnowDataPoint = new BasicDataPoint2D();
		$userKnowDataPoint->setXvalueIsNominal();
		$userKnowDataPoint->setValueX('Eigener Wissenstand');
		$userKnowDataPoint->setValueY(
				$this->getCurrentUserTopicKnowledge($topic->getTopicId()));
		$node->addDataPoint($userKnowDataPoint);

		//average knowledge of other participants
		$knowOwnGoal = new BasicDataPoint2D();
		$knowOwnGoal->setXvalueIsNominal();
		$knowOwnGoal->setValueX('Eigene Zielsetzung');
		$knowOwnGoal->setValueY(
				$this->getCurrentUserTopicKnowledgeGoal($topic->getTopicId()));
		$node->addDataPoint($knowOwnGoal);

		//average knowledge of other participants
		$avgKnowDataPoint = new BasicDataPoint2D();
		$avgKnowDataPoint->setXvalueIsNominal();
		$avgKnowDataPoint->setValueX('Durchschnittlicher, aktueller Wissenstand aller Teilnehmer');
		$avgKnowDataPoint->setValueY(
				$this->getAvgCurrentTopicKnowledge($topic->getTopicId()));

		$node->addDataPoint($avgKnowDataPoint);
		
		/*
		//only add the data, if a similar user data is present
		$avgSimilar = $this->getAvgSimilarUserTopicKnowledge($topic->getTopicId());
		if (!is_null($avgSimilar)){
			//average knowledge of similar participants
			$avgKnowSimilar = new BasicDataPoint2D();
			$avgKnowSimilar->setXvalueIsNominal();
			$avgKnowSimilar->setValueX('Durchschnitt ähnliche Teilnehmer');
			$avgKnowSimilar->setValueY($avgSimilar);
			$node->addDataPoint($avgKnowSimilar);
		}
		*/
		
		
		
		return $node;
	}
	
	/**/
	
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
	
	protected function getCurrentUserTopicKnowledgeGoal($topicId){
		$goalFetcher = $this->getDbCourseKnowledgeGoalDataFetcher();
		$goalData = $goalFetcher->getKnowledgeGoalArray($this->courseId);
		if (!isset($goalData[$this->userId][$topicId])){
			return 0;
		}
		
		return $goalData[$this->userId][$topicId];
	}
	
	protected function getAvgSimilarUserTopicKnowledge($topicId){
		$knowledgeFetcher = $this->getCurrentKnowledgeDataFetcher();
		$allTopicData = $knowledgeFetcher->getKnowledgeArray($this->courseId);
		
		$userFetcher = $this->getUserDataFetcher();
		$similarUsers = $userFetcher->getSimilarKnowledgeGoalUsers(
				$this->courseId, $topicId, $this->userId);
		
		if (!isset($similarUsers) || count($similarUsers) == 0){
			return null;
		}
		
		if (!isset($allTopicData[$topicId])){
			return null;
		}
		
		$knowledgeData = $allTopicData[$topicId];
		if (count($knowledgeData) == 0){
			return null;
		}
		
		$sum = 0;
		$count = 0;
		foreach($knowledgeData as $userId => $estimation){
			if ($userId != $this->userId && in_array($userId, $similarUsers)){
				$sum += $estimation;
				$count++;
			}
		}
		
		if ($count == 0){
			return null;
		}
		
		return $sum / $count;
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
	
	/**/
	
	protected function getAvgCurrentTopicKnowledge($topicId){
		$knowledgeFetcher = $this->getCurrentKnowledgeDataFetcher();
		$allTopicData = $knowledgeFetcher->getKnowledgeArray($this->courseId);
		
		if (!isset($allTopicData[$topicId])){
			return 0;
		}
		
		$knowledgeData = $allTopicData[$topicId];
		if (count($knowledgeData) == 0){
			return 0;
		}
		
		$sum = 0;
		foreach($knowledgeData as $userId => $estimation){
			if ($userId != $this->userId){
				$sum += $estimation;
			}
		}
		
		return $sum / count($knowledgeData);
	}
	
	protected function getCurrentUserTopicKnowledge($topicId){
		$knowledgeFetcher = $this->getCurrentKnowledgeDataFetcher();
		$knowledgeData = $knowledgeFetcher->getKnowledgeArray($this->courseId);
		
		if (!isset($knowledgeData[$topicId][$this->userId])){
			return 0;
		}
		
		return $knowledgeData[$topicId][$this->userId];
	}
}
