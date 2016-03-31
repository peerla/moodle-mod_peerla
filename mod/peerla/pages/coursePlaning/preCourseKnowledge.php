<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/coursePlaning/preCourseKnowledge.php';
$includePath = realpath(__DIR__).'/../../classes/';
include(realpath(__DIR__).'/../includes/coursePageHeader.php');
require_once($includePath.'implementations/BasicLearningInterval.php');
require_once($includePath.'implementations/LazyLoadingPreCourseKnowledge.php');
require_once($includePath.'helper/KnowledgeEstimationHtmlBuilder.php');
require_once($includePath.'implementations/cud/SqlCoursePlaningCud.php');
require_once($includePath.'implementations/cud/SqlPreCourseKnowledgeCud.php');
require_once($includePath.'implementations/IntervalTimeManager.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant($courseId, $USER->id);

$coursePlaningFactory = new LazyLoadingCoursePlaningFactory($DB);
$planing = $coursePlaningFactory->getParticipantCoursePlaning($courseId,$USER->id);

//there is already an interval -> redirect
if (!is_null($planing) && $planing->userHasFinishedCourseGoalSetting()){
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

$topicKnowledge = $currentParticipant->getCurrentCourseKnowledge();
	
$errors = array();
if (optional_param('save','',PARAM_TEXT)){
	
	if (!isset($_POST['topicEstimation']) || !is_array($_POST['topicEstimation'])){
		$errors[] = 'Interner Fehler: Keine Themen gefunden.';
	}
	
	if (count($errors) == 0){
		
		//save knowledge
		if (count($errors) == 0){
			$topicKnowledge = array();
			foreach($_POST['topicEstimation'] as $topicId => $knowledge){
				$topic = new LazyLoadingPreCourseKnowledge();
				$topic->setEstimation($knowledge);
				$topic->setTopicId($topicId);
				$topic->setCourseId($courseId);
				$topic->setEstimationUserId($USER->id);
				$topic->setIntervalId(null);
				$topicKnowledge[] = $topic;
			}
			
			if (is_null($planing)){
				$newPlaning = new LazyLoadingCoursePlaning($courseId, $USER->id);
				$planingCud = new SqlCoursePlaningCud($DB);
				$planingCud->create($newPlaning);
			}
			
			//save pre course knowledge
			$preCourseCud = new SqlPreCourseKnowledgeCud($DB);
			$preCourseCud->savePreCourseKnowledgeArray($topicKnowledge);
			$preCourseCud->updatePlaningTimestamps();

			$redirectUrl = new \moodle_url('/mod/peerla/pages/coursePlaning/courseGoals.php', $paramArray);
			redirect($redirectUrl);
		}
	}
}


//echo $OUTPUT->heading(get_string('pre_course_knowledge_heading', 'peerla'));

include(realpath(__DIR__).'/../includes/formResult.php');
?>

<form method="post" action="">
	
	<div class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			<?=get_string('pre_course_knowledge_heading', 'peerla')?>
			- 
			Veranstaltungsplanung 1/3
		</h3>
		<div class="infoTextContainer">
			<p>
				Unten siehst Du alle Themengebiete der Veranstaltung. Benutze den Schieberegler, um 
				deinen aktuellen Wissenstand einzuschätzen. Mit <span class="glyphicon glyphicon-arrow-down"></span> 
				und <span class="glyphicon glyphicon-arrow-up"></span> kannst Du dir die Unterthemen anzeigen lassen 
				und diese einzeln bewerten.
			</p>
			
			<p>
				Die Position ganz <b>links</b> bedeutet, dass Du <i>kein Wissen zum Thema</i> besitzt
			</p>
			
			<p>
				Die Position ganz <b>rechts</b> bedeutet, dass Du <i>den Veranstaltungsstoff bereits vollständig beherrschst</i>
			</p>
		</div>
		<div class="formContent">
<?php

$builder = new KnowledgeEstimationHtmlBuilder();
echo $builder->getTopicEstimationHtml($topicKnowledge);
?>
		</div>
	</div>
	<input type="submit" name="save" value="<?=get_string('btn_next','peerla')?>" />
</form>
<?php
include(realpath(__DIR__).'/../includes/footer.php');