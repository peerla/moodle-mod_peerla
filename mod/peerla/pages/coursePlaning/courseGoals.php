<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/coursePlaning/courseGoals.php';
$includePath = realpath(__DIR__).'/../../classes/';
require_once($includePath.'implementations/LazyLoadingCoursePlaning.php');
require_once($includePath.'implementations/BasicCourseGoal.php');
require_once($includePath.'implementations/factories/LazyLoadingCoursePlaningFactory.php');
require_once($includePath.'implementations/cud/SqlCoursePlaningCud.php');
include(realpath(__DIR__).'/../includes/coursePageHeader.php');

$errors = array();
$planingExists = true;

$coursePlaningFactory = new LazyLoadingCoursePlaningFactory($DB);
$planing = $coursePlaningFactory->getParticipantCoursePlaning($courseId,$USER->id);

if (!is_null($planing) && $planing->userHasFinishedCourseGoalSetting()){
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

if (is_null($planing)){
	$planingExists = false;
	$planing = new LazyLoadingCoursePlaning($courseId, $USER->id);
}
//load existing data
elseif(!optional_param('save','',PARAM_TEXT)){

	$_POST['aspiredMark'] = $planing->getAspiredMark();

	$goals = array();
	$counter = 1;
	foreach($planing->getCourseGoals() as $goal){
		$goals[$counter]['text'] = $goal->getGoalText();
		$goals[$counter]['goalId'] = $goal->getGoalId();
		$counter++;
	}
	$_POST['goals'] = $goals;
}

if (optional_param('save','',PARAM_TEXT)){
	
	$newGoals = array();
	
	if (isset($_POST['goals']) && is_array($_POST['goals'])){
		foreach($_POST['goals'] as $goalData){
			if (trim($goalData['text'])){
				$newGoals[] = array(
					'text' => $goalData['text'],
					'goalId' => $goalData['goalId']
				);
			}
		}
	}
	
	if (count($newGoals) == 0){
		$errors['goals'] = 'Gib bitte mindestens ein Ziel an.';
	}
	
	if (count($errors) === 0){
		
		$planing->setAspiredMark(optional_param('aspiredMark','',PARAM_TEXT));
		
		if (count($newGoals) > 0){
			$goals = array();
			foreach($newGoals as $goalData){
				if (trim($goalData['text'])){
					$goal = new BasicCourseGoal();
					$goal->setCourseId($courseId);
					$goal->setUserId($USER->id);
					$goal->setGoalText(trim($goalData['text']));
					if ($goalData['goalId']){
						$goal->setGoalId($goalData['goalId']);
					}
					$goals[] = $goal;
				}
			}
			$planing->setCourseGoals($goals);
		}
		
		$cud = new SqlCoursePlaningCud($DB);
		if ($planingExists){
			$cud->update($planing);
		}
		else{
			$cud->create($planing);
		}
		
		$redirectUrl = new \moodle_url('/mod/peerla/pages/coursePlaning/goalTransfer.php', $paramArray);
		redirect($redirectUrl);
		
	}
}

//echo $OUTPUT->heading(get_string('course_goal_setting_heading', 'peerla'));

include(realpath(__DIR__).'/../includes/formResult.php');
?>
<form method="post" action="">
	<div class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			Deine Veranstaltungsziele
			- 
			Veranstaltungsplanung 2/3
		</h3>
		<div class="infoTextContainer">
			<p>
				Selten ist es möglich jedes Detail des Veranstaltungsstoffes zu 
				erlernen. Deshalb ist es besonders wichtig dir frühzeitig klar
				zu machen, was genau Du persönlich erreichen möchte. 
			</p>
			<p>
				Überleg dir deine persönlichen Ziele und trag sie unten ein.
				<br/>
				Mit <span class="glyphicon glyphicon-plus-sign"></span> kannst 
				du beliebig viele Ziele hinzufügen.
			</p>
		</div>
		<div class="infoTextContainer infoTextMore">
			<p style="margin-top: 15px;">
				Vielleicht helfen dir folgende Fragen bei der Zielfindung:
			</p>
			<ul>
				<li>Was hat dich dazu bewegt diesen Kurs zu wählen?</li>
				<li>Welche Inhalte findest du besonders interessant?</li>
				<li>Welches Wissen und welche Fertigkeiten versprichst Du dir von deiner Teilnahme?</li>
				<li>Was wäre besonders hilfreich für dein weiteres Studium oder dein späteres Berufleben?</li>
			</ul>
		</div>
		
		<div class="formContent">
		<!--
			<div class="formRow goalContainer">
				<label>Angestrebte Note</label>
				<select name="aspiredMark">
					<option>2.3</option>
				</select>
			</div>
  -->
<?php
	$goals = array();
	if (isset($_POST['goals'])){
		$goals = $_POST['goals'];
	}
	
	if (is_array($goals) && count($goals) > 0){
		
		$counter = 1;
		foreach($goals as $goal){
?>
			<div class="formRow goalContainer">
				<label>Ziel <?=$counter?></label>
				<input type="text" class="goalText" name="goals[<?=$counter?>][text]" value="<?=$goal['text']?>" />
				<input type="hidden" class="goalId" name="goals[<?=$counter?>][goalId]" value="" />
			</div>
<?php
			$counter++;
		}
	}
	else{
?>
			<div class="formRow goalContainer">
				<label>Ziel 1</label>
				<input type="text" class="goalText<?=isset($errors['goals']) ? ' inputError':''?>" name="goals[1][text]" value="" />
				<input type="hidden" class="goalId" name="goals[1][goalId]" value="" />
			</div>
			<div class="formRow goalContainer">
				<label>Ziel 2</label>
				<input type="text" class="goalText" name="goals[2][text]" value="" />
				<input type="hidden" class="goalId" name="goals[2][goalId]" value="" />
			</div>
<?php
	}
?>
			<div class="formRow newGoalContainer">
				<label>weiteres Ziel</label>
				<a style="color:#333;" href="#" id="addCourseGoal"><span class="glyphicon glyphicon-plus-sign"></span></a>
			</div>
		</div>
		<a class="infoMoreTextLink" data-toggletext="Weniger Text anzeigen">
			<span class="glyphicon glyphicon glyphicon-menu-down"></span>
			<span class="toggleText">Mehr Text anzeigen</span>
		</a>
	</div>
	
	<input type="submit" name="save" value="<?=get_string('btn_next','peerla')?>" />
</form>
<?php

include(realpath(__DIR__).'/../includes/footer.php');




