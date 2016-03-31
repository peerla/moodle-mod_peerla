<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/coursePlaning/goalTransfer.php';
$includePath = realpath(__DIR__).'/../../classes/';
require_once($includePath.'implementations/factories/LazyLoadingCourseParticipantFactory.php');
require_once($includePath.'helper/KnowledgeEstimationHtmlBuilder.php');
require_once($includePath.'implementations/cud/SqlTopicKnowledgeGoalCud.php');
include(realpath(__DIR__).'/../includes/coursePageHeader.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant($courseId, $USER->id);
$coursePlaning = $currentParticipant->getCoursePlaning();

if (is_null($coursePlaning) || $coursePlaning->userHasFinishedGoalToTopicTransformation()
		|| !$coursePlaning->userHasFinishedCourseGoalSetting()){
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

$errors = array();

if (optional_param('save','',PARAM_TEXT)){
	if (!isset($_POST['topicEstimation']) || !is_array($_POST['topicEstimation'])){
		$errors[] = 'Internal error. No Topic data found.';
	}
	
	if (count($errors) == 0){
		$topicKnowledge = array();
		
		foreach($_POST['topicEstimation'] as $topicId => $knowledgeGoal){
			
			$topic = new BasicCourseTopicKnowledge();
			$topic->setEstimation($knowledgeGoal);
			$topic->setTopicId($topicId);
			$topic->setEstimationUserId($USER->id);
			$topic->setCourseId($courseId);
			
			$topicKnowledge[] = $topic;
		}
		
		$cud = new SqlTopicKnowledgeGoalCud($DB);
		$cud->updateTopicKnowledgeGoals($topicKnowledge);
		$cud->updatePlaningTimestamps();
		
		$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
		redirect($redirectUrl);
	}
}

//echo $OUTPUT->heading(get_string('goal_transfer_headline', 'peerla'));

include(realpath(__DIR__).'/../includes/formResult.php');


?>
<!--
<p></p>
<p><?=get_string('goal_transfer_intro2', 'peerla')?></p>
-->

<div class="infoBox">
	<h3>
		<span class="glyphicon glyphicon-info-sign"></span>
		Deine Veranstaltungsziele
	</h3>
	<div class="infoTextContainer">
		<ul class="numberedList">
<?php
	foreach($coursePlaning->getCourseGoals() as $goal){
?>
			<li><?=$goal->getGoalText()?></li>
<?php
	}
?>
		</ul>
	</div>
</div>

<form method="post" action="">
	<div class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			Übertrage Deine Ziele auf die Themen
			- 
			Veranstaltungsplanung 3/3
		</h3>
		<div class="infoTextContainer">
			<p>
				Schätze den Wissenstand ein, den du am Ende des Kurses erlangen möchtest.
				Bei Themen die dir besonders wichtig sind bietet es sich an dir mit 
				<span class="glyphicon glyphicon-arrow-down"></span> 
				und <span class="glyphicon glyphicon-arrow-up"></span> die Unterthemen 
				anzeigen zu lassen und diese einzeln zu bewerten.
			</p>
			<p>
				Was bedeuten Deine persönlichen Ziele für die Veranstaltungsthemen? 
				In welchen Themengebieten musst du ein hohes Wissen erlangen um deine Ziele zu erreichen?
				Welche Themen sind nicht so wichtig?
			</p>
			<p>
				Die Position ganz <b>links</b> bedeutet, dass Du dich mit dem Thema <b>überhaupt nicht beschäftigen</b> möchtest.
			</p>
			
			<p>
				Die Position ganz <b>rechts</b> bedeutet, dass Du <b>Veranstaltungsstoff bis ins kleinste Detail beherrschen</b> möchtest.
			</p>
		</div>
		
		<div class="formContent">
<?php
$builder = new KnowledgeEstimationHtmlBuilder();
/** @todo Fehlermeldung, falls keine Topics vorhanden sind "Kontaktieren Sie den Dozenten" */
echo $builder->getTopicEstimationHtml($currentParticipant->getCurrentCourseKnowledge());
?>
		</div>
	</div>
	<input type="submit" name="save" value="<?=get_string('btn_next','peerla')?>" />
</form>
<?php
include(realpath(__DIR__).'/../includes/footer.php');