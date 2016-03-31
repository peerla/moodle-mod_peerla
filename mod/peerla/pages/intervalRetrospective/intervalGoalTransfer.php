<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/coursePlaning/empty.php';
include(realpath(__DIR__).'/../includes/coursePageHeader.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant(
		$currentCourse->getCourseId(), $USER->id
);
$interval = $currentParticipant->getCurrentLearningInterval();

//there is already a running interval -> redirect
if ($interval->isRunning() && $interval->retrospectiveDone()) {
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

$includePath = realpath(__DIR__).'/../../classes/';

$errors = array();
if (optional_param('save','',PARAM_TEXT)){
	$redirectUrl = new \moodle_url('/mod/peerla/pages/intervalRetrospective/intervalReflection.php', $paramArray);
	redirect($redirectUrl);
}

//echo $OUTPUT->heading(get_string('HEADLINE_STRING', 'peerla'));
?>
<div class="infoBox">
	<h3>
		<span class="glyphicon glyphicon-edit"></span>
		<?=get_string('view_interval_goal_headline_part1','peerla')?>
		<?=userdate($interval->getStartDate(),get_string('strftimedateshort'))?>
		<?=get_string('view_interval_goal_headline_part2','peerla')?>
		<?=userdate($interval->getEndDate(),get_string('strftimedateshort'))?>
		<?=get_string('view_interval_goal_headline_part3','peerla')?>
	</h3>
	<div class="infoTextContainer">
		<p>
			Bitte überprüfe noch mal deine alten Ziele. All offenen Ziele 
			(<span class="glyphicon glyphicon-question-sign goalStatusIcon"></span>) werden
			automatisch ins nächste Intervall übernommen. Du hast jetzt die Chance deine 
			Ziele noch einmal zu bearbeiten (<span class="glyphicon glyphicon-pencil"></span>).
		</p>
	</div>
	<div class="infoTextMore infoTextContainer">
		<p>
			<span class="glyphicon glyphicon-question-sign goalStatusIcon"></span> 
			<i>in Bearbeitung: </i> 
			Du verfolgst noch das Ziel, hast es aber noch nicht erreicht.
			<br/>
			<span class="glyphicon glyphicon-ok-sign goalStatusIcon"></span> 
			<i>fertig: </i> 
			Du hast das Ziel erreicht
			<br/>
			<span class="glyphicon glyphicon-remove-sign goalStatusIcon"></span> 
			<i>verworfen: </i> 
			Du hast das Ziel nicht erreicht und möchtest es (momentan) auch nicht erreichen.
		</p>
	</div>
	<div class="intervalGoalOverviewContainer formContent">
<?php
			foreach($interval->getIntervalGoals() as $intervalGoal){
				$statusClass = 'glyphicon-question-sign';
				if ($intervalGoal->getStatus() == 'done'){
					$statusClass = 'glyphicon-ok-sign';
				}
				if ($intervalGoal->getStatus() == 'cancelled'){
					$statusClass = 'glyphicon-remove-sign';
				}
?>
		<div class="intervalGoalOverviewEntry">
			<span class="glyphicon <?=$statusClass?> goalStatusIcon"></span> 
			<?=$intervalGoal->getGoalShortText()?>
			<a href="<?=$CFG->wwwroot?>/mod/peerla/pages/interval/goalEdit.php?courseId=<?=$currentCourse->getCourseId()?>&amp;goalId=<?=$intervalGoal->getGoalId()?>&amp;back=retrospective" 
			   class="editLink glyphicon glyphicon-pencil"></a>
		</div>
<?php
			}
?>
	</div>
	<a class="infoMoreTextLink" data-toggletext="Weniger Informationen anzeigen">
		<span class="glyphicon glyphicon glyphicon-menu-down"></span>
		<span class="toggleText">Mehr Informationen anzeigen</span>
	</a>
</div>
<form method="post" action="">
	<input type="submit" name="save" value="<?=get_string('btn_next','peerla')?>" />
</form>
		
<?php
include(realpath(__DIR__).'/../includes/footer.php');