<?php
namespace mod_kom_peerla;

require_once(realpath(__DIR__).'/classes/implementations/factories/LazyLoadingCourseFactory.php');
require_once(realpath(__DIR__).'/classes/implementations/factories/LazyLoadingUserFactory.php');

$url = '/mod/peerla/view.php';
include(realpath(__DIR__).'/pages/includes/coursePageHeader.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant(
		$currentCourse->getCourseId(), $USER->id
);

if (is_null($currentParticipant)){
	print_error('error_participant_not_found','peerla');
}

$coursePlaning = $currentParticipant->getCoursePlaning();

if (is_null($coursePlaning) || !$coursePlaning->userHasFinishedPlaning()){
	
	include realpath(__DIR__).'/pages/includes/viewIncludes/introTextBox.php';
	
	if (is_null($coursePlaning) || !$coursePlaning->userHasFinishedPreCourseKnowledgeSetting()){
?>

<a class="navigationLink"
	href="<?=$CFG->wwwroot?>/mod/peerla/pages/coursePlaning/preCourseKnowledge.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>
		<?=get_string('course_planing','peerla')?>
</a>
<?php
	}
	elseif (!$coursePlaning->userHasFinishedCourseGoalSetting()){
?>
<a class="navigationLink" 
   href="<?=$CFG->wwwroot?>/mod/peerla/pages/coursePlaning/courseGoals.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>
		<?=get_string('course_planing','peerla')?>
</a>
<?php
	}
	elseif (!$coursePlaning->userHasFinishedGoalToTopicTransformation()){
?>
<a class="navigationLink" 
   href="<?=$CFG->wwwroot?>/mod/peerla/pages/coursePlaning/goalTransfer.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>
		<?=get_string('course_planing','peerla')?>
</a>
<?php
	}
}
else{
	
	$interval = $currentParticipant->getCurrentLearningInterval();
	
	//no active interval
	if (is_null($interval) || !$interval->isRunning()){
		
		//retrospectve done?
		if (!is_null($interval) && !$interval->retrospectiveDone()){
			
			include realpath(__DIR__).'/pages/includes/viewIncludes/intervalPlaningTextBox.php';
?>
	<a class="navigationLink"
			href="<?=$CFG->wwwroot?>/mod/peerla/pages/intervalRetrospective/intervalGoalTransfer.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>	
		<?=get_string('link_interval_planing','peerla')?>
	</a>	
<?php
		}
		else{
			if (is_null($interval)){
				include realpath(__DIR__).'/pages/includes/viewIncludes/intervalPlaningFirstTextBox.php';
			}
			else{
				include realpath(__DIR__).'/pages/includes/viewIncludes/intervalPlaningTextBox.php';
			}
?>
	<a class="navigationLink"
			href="<?=$CFG->wwwroot?>/mod/peerla/pages/intervalPlaning/createIntervalGoal.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>	
		<?=get_string('link_interval_planing','peerla')?>
	</a>	
<?php
		}
	}
	else{
		if (!$interval->userHasFinishedGoalSetting()){
			
			include realpath(__DIR__).'/pages/includes/viewIncludes/intervalPlaningTextBox.php';
?>
	<a class="navigationLink" href="<?=$CFG->wwwroot?>/mod/peerla/pages/intervalPlaning/createIntervalGoal.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>
			<?=get_string('link_interval_planing','peerla')?>
	</a>	
<?php
		}
?>
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
<?php
		if ($interval->userHasFinishedPlaning()){
?>
	<div style="margin-bottom:10px;" class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-info-sign"></span>
			<?=get_string('view_interval_goal_headline_part1','peerla')?>
			<?=userdate($interval->getStartDate(),get_string('strftimedateshort'))?>
			<?=get_string('view_interval_goal_headline_part2','peerla')?>
			<?=userdate($interval->getEndDate(),get_string('strftimedateshort'))?>
			<?=get_string('view_interval_goal_headline_part3','peerla')?>
		</h3>
		<div class="infoTextContainer">
			<p>
				Hier siehst Du alle deine Ziele für das aktuelle Intervall. Drücke 
				<span class="glyphicon glyphicon-pencil"></span> um den Status deines
				Ziels zu bearbeiten.
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
		<div class="intervalGoalOverviewContainer infoTextContainer">
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
				<a href="<?=$CFG->wwwroot?>/mod/peerla/pages/interval/goalEdit.php?courseId=<?=$currentCourse->getCourseId()?>&amp;goalId=<?=$intervalGoal->getGoalId()?>" 
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

	<a class="navigationLink" href="<?=$CFG->wwwroot?>/mod/peerla/pages/interval/goalCreate.php?courseId=<?=$currentCourse->getCourseId()?>">
		<span class="glyphicon glyphicon-expand"></span>
		Neues Intervallziel erstellen
	</a>
	
	<!--<h3>Dein aktueller Stand</h3>-->
<?php
			$includePath = realpath(__DIR__).'/classes/';
			include(realpath(__DIR__).'/pages/includes/statistik.php');
		}
	}
}

echo $OUTPUT->footer();