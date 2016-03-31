<?php
namespace mod_kom_peerla;
require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataViewProvider.php';

require_once realpath(__DIR__).'/dbDataFetchers/CachedDbCurrentKnowledgeDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbCourseTopicDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbUserDataFetcher.php';
require_once realpath(__DIR__).'/dbDataFetchers/CachedDbKnowledgeGoalDataFetcher.php';

require_once realpath(__DIR__).'/BasicDataViewNode.php';
require_once realpath(__DIR__).'/ChildAverageDataViewNode.php';

require_once realpath(__DIR__).'/TimeInvestmentOwnInvestmentDataProvider.php';

/**
 * Description of TimeInvestmentDataProvider
 *
 * @author Christoph Bohr
 */
class TimeInvestmentDataProvider implements DataViewProvider{
	
	protected $db;
	protected $userId;
	protected $courseId;
	
	protected $goalFetcher;
	protected $userFetcher;
	protected $topicFetcher;
	protected $currentKnowledgeFetcher;
	protected $investmentFetcher;
	
	/**
	 * 
	 * @param \moodle_database $db
	 * @param type $courseId Course id for the data
	 * @param int $userId User id to compare the data against
	 */
	function __construct(\moodle_database $db, $courseId, $userId) {
		$this->db = $db;
		$this->courseId = $courseId;
		$this->userId = $userId;
	}
	
	public function getDataView() {
		$currentKnowledgeFetcher = $this->getCurrentKnowledgeDataFetcher();
		$goalFetcher = $this->getDbCourseKnowledgeGoalDataFetcher();
		$userFetcher = $this->getUserDataFetcher();
		$topicFetcher = $this->getDbCourseTopicDataFetcher();
		$investmentFetcher = $this->getDbTimeInvestmentDataFetcher();
		
		$ownInvestmentProvider = new TimeInvestmentOwnInvestmentDataProvider
				($this->db, $this->courseId, $this->userId);
		$ownInvestmentProvider->setCurrentKnowledgeDataFetcher($currentKnowledgeFetcher);
		$ownInvestmentProvider->setDbCourseKnowledgeGoalDataFetcher($goalFetcher);
		$ownInvestmentProvider->setDbCourseTopicDataFetcher($topicFetcher);
		$ownInvestmentProvider->setDbTimeInvestmentDataFetcher($investmentFetcher);
		
		$rootNode = new BasicDataViewNode();
		$rootNode->setDisplayOwnData(FALSE);
		$rootNode->setDisplayChildData(TRUE);
		
		$rootNode->addChildNode($ownInvestmentProvider->getDataView());
		
		return $rootNode;
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
			$this->userFetcher->setDbKnowledgeGoalFetcher(
					$this->getDbCourseKnowledgeGoalDataFetcher());
		}
		
		return $this->userFetcher;
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

}
