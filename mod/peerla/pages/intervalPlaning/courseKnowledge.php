<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/intervalPlaning/courseKnowledge.php';
$includePath = realpath(__DIR__).'/../../classes/';
include(realpath(__DIR__).'/../includes/coursePageHeader.php');
require_once($includePath.'implementations/BasicLearningInterval.php');
require_once($includePath.'helper/KnowledgeEstimationHtmlBuilder.php');
require_once($includePath.'implementations/cud/SqlIntervalTopicKnowledgeCud.php');
require_once($includePath.'implementations/cud/SqlLearningIntervalCud.php');
require_once($includePath.'implementations/IntervalTimeManager.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant($courseId, $USER->id);

$topicKnowledge = array();
$lastLearningInterval = $currentParticipant->getCurrentLearningInterval();

$addShowAllLink = false;
//no interval for this course has been planed
if (is_null($lastLearningInterval)){
	$topicKnowledge = $currentParticipant->getCurrentCourseKnowledge();
}
//there is already a running interval -> redirect
elseif ($lastLearningInterval->isRunning() && $lastLearningInterval->userHasFinishedKnowledgeEstimation()) {
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}
else{
	$topicKnowledge = $lastLearningInterval->getPreIntervalKnowledge();
}

$errors = array();
if (optional_param('save','',PARAM_TEXT)){
	
	if (!isset($_POST['topicEstimation']) || !is_array($_POST['topicEstimation'])){
		$errors[] = 'Internal error. No Topic data found.';
	}
	
	if (count($errors) == 0){
		
		$savePreCourseKnowledge = false;
		if (is_null($lastLearningInterval) || !$lastLearningInterval->isRunning()){
			$timeManager = new IntervalTimeManager();
			$savePreCourseKnowledge = true;
			//create new interval
			$intervalCud = new SqlLearningIntervalCud($DB);
			$interval = new BasicLearningInterval();
			$interval->setCourseId($courseId);
			$interval->setUserId($USER->id);
			$interval->setStartDate($timeManager->getIntervalStartTime(time()));
			$interval->setEndDate($timeManager->getIntervalEndTime(time()));
			
			$intervalId = $intervalCud->create($interval);
		}
		else{
			$intervalId = $lastLearningInterval->getIntervalId();
		}
		
		if (!$intervalId){
			$errors[] = 'Internal Error: Could not save to database';
		}
		
		//save knowledge
		if (count($errors) == 0){
			$topicKnowledge = array();

			foreach($_POST['topicEstimation'] as $topicId => $knowledge){

				$topic = new LazyLoadingIntervalKnowledge();
				$topic->setEstimation($knowledge);
				$topic->setTopicId($topicId);
				$topic->setEstimationUserId($USER->id);
				$topic->setIntervalId($intervalId);
				$topicKnowledge[] = $topic;
				
			}

			$cud = new SqlIntervalTopicKnowledgeCud($DB);
			$cud->updateTopicsKnowledge($topicKnowledge);
			$cud->updateIntervalKnowledgeEstimationTimestamps();

			$redirectUrl = new \moodle_url('/mod/peerla/pages/intervalPlaning/createIntervalGoal.php', $paramArray);
			redirect($redirectUrl);
		}
	}
}


echo $OUTPUT->heading(get_string('pre_course_knowledge_heading', 'peerla'));

include(realpath(__DIR__).'/../includes/formResult.php');
?>

<form method="post" action="">
<?php
if ($addShowAllLink){
?>
	<a class="glyphicon glyphicon-align-left" href="?courseId=<?=$courseId?>&amp;showAllTopics=1"></a>
<?php
}

$builder = new KnowledgeEstimationHtmlBuilder();
echo $builder->getTopicEstimationHtml($topicKnowledge);
?>
	<input type="submit" name="save" value="<?=get_string('btn_next','peerla')?>" />
</form>
<?php
include(realpath(__DIR__).'/../includes/footer.php');