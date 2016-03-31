<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/interval/goalEdit.php';
include(realpath(__DIR__).'/../includes/coursePageHeader.php');
$includePath = realpath(__DIR__).'/../../classes/';
require_once($includePath.'helper/CourseTopicSelectOptionHelper.php');
require_once($includePath.'helper/TimeInvestementSelectHelper.php');
require_once($includePath.'helper/TimeInvestmentFormater.php');
require_once($includePath.'implementations/cud/SqlIntervalGoalCud.php');
require_once($includePath.'implementations/cud/SqlIntervalTopicKnowledgeCud.php');
require_once($includePath.'helper/KnowledgeEstimationHtmlBuilder.php');

$backLink = 'view';
if (isset($_GET['back'])){
	$backLink = $_GET['back'];
}

if (isset($_GET['goBack']) && $_GET['goBack']){
	if (isset($_GET['back']) && $_GET['back'] == 'retrospective'){
		$redirectUrl = new \moodle_url('/mod/peerla/pages/intervalRetrospective/intervalGoalTransfer.php', $paramArray);
	}
	else{
		$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	}
	redirect($redirectUrl);
}

if (!isset($_GET['goalId']) || !$_GET['goalId']){
	print_error('error_goal_not_found','peerla');
}

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant($courseId, $USER->id);

$interval = $currentParticipant->getCurrentLearningInterval();

if (is_null($interval)){
	print_error('error_goal_not_found','peerla');
}

//check if the given goal is a current goal of the user
$goal = null;
foreach($interval->getIntervalGoals() as $intervalGoal){
	if ($intervalGoal->getGoalId() == $_GET['goalId']){
		$goal = $intervalGoal;
	}
}

if (is_null($goal)){
	print_error('error_goal_not_found','peerla');
}


$errors = array();
if (optional_param('save','',PARAM_TEXT)){
	
	if (!isset($_POST['status']) || ($_POST['status'] != 'open'
			&& $_POST['status'] != 'done' && $_POST['status'] != 'cancelled')){
		$errors['status'] = 1;
	}
	
	if (!isset($_POST['investedTimeHours']) || !isset($_POST['investedTimeMinutes'])){
		$errors['investedTime'] = 1;
	}
	
	if (!isset($_POST['weekday']) || !is_array($_POST['weekday']) 
			|| count($_POST['weekday']) == 0){
		$errors['weekday'] = 1;
	}
	
	if (count($errors) == 0){
		$intervalGoalCud = new SqlIntervalGoalCud($DB);
		
		$timeInvestment = $_POST['investedTimeHours']*60+$_POST['investedTimeMinutes'];
		$goal->setTimeInvestment($timeInvestment);
		$goal->setStatus($_POST['status']);
		$goal->setLearningDays($_POST['weekday']);
		if ($intervalGoalCud->update($goal)){
			
			//save topic knowledge update
			if (isset($_POST['topicEstimation'])){
				$topicKnowledge = array();

				foreach($_POST['topicEstimation'] as $topicId => $knowledge){

					$topic = new LazyLoadingIntervalKnowledge();
					$topic->setEstimation($knowledge);
					$topic->setTopicId($topicId);
					$topic->setEstimationUserId($USER->id);
					$topic->setIntervalId($interval->getIntervalId());
					$topicKnowledge[] = $topic;
				}

				$cud = new SqlIntervalTopicKnowledgeCud($DB);
				$cud->updateTopicsKnowledge($topicKnowledge);
			}
			
			if (isset($_GET['back']) && $_GET['back'] == 'retrospective'){
				$redirectUrl = new \moodle_url('/mod/peerla/pages/intervalRetrospective/intervalGoalTransfer.php', $paramArray);
			}
			else{
				$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
			}
			redirect($redirectUrl);
		}
		else{
			$errors['db'] = get_string('error_db','peerla');
		}
	}
}

$investmentFormater = new TimeInvestmentFormater();

//echo $OUTPUT->heading(get_string('goal_edit_headline', 'peerla'));

include(realpath(__DIR__).'/../includes/formResult.php');
?>

<form method="post" action="goalEdit.php?courseId=<?=$courseId?>&amp;goalId=<?=$_GET['goalId']?>&amp;back=<?=$backLink?>">
	<div class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			<?=$goal->getGoalText()?>
		</h3>

		<div class="goalEditContainer formContent">
			<div class="goalEditLeft">
				<div style="line-height: 30px;" class="formRow">
					<label><?=get_string('goal_label_planed_time_investment', 'peerla')?></label>
					<?=$investmentFormater->formatForOutput($goal->getPlanedTimeInvestment())?>
				</div>
				<div style="line-height: 30px;" class="formRow">
					<label><?=get_string('goal_label_comment', 'peerla')?></label>
					<div style="float: left; width: 400px;">
						<?=nl2br($goal->getGoalComment())?>
					</div>
				</div>
				<div class="formRow">
					<label for="status">
						<?=get_string('status', 'peerla')?>
					</label>
					<select name="status" id="status" 
							<?=(isset($errors['status']) && $errors['status']) ? ' class="inputError"':''?>>
						<option value="open"<?=($goal->getStatus() == 'open') ? ' selected="selected"':''?>>
							<?=get_string('status_open', 'peerla')?>
						</option>
						<option value="done"<?=($goal->getStatus() == 'done') ? ' selected="selected"':''?>>
							<?=get_string('status_done', 'peerla')?>
						</option>
						<option value="cancelled"<?=($goal->getStatus() == 'cancelled') ? ' selected="selected"':''?>>
							<?=get_string('status_cancelled', 'peerla')?>
						</option>
					</select>
				</div>
				<div class="formRow">
					<label for="investedTimeHours">
						<?=get_string('invested_time', 'peerla')?>
					</label>
					<select name="investedTimeHours" id="investedTimeHours"
							<?=(isset($errors['investedTime']) && $errors['investedTime']) ? ' class="inputError"':''?>>
<?php
	$hours = $investmentFormater->getHourPart($goal->getTimeInvestment());
	for ($i=0; $i<=100; $i++){
		$selected = '';
		if ($i == $hours){
			$selected = ' selected="selected"';
		}
?>
						<option<?=$selected?> value="<?=$i?>"><?=$i?></option>
<?php
	}
?>
					</select>
					<?=get_string('hours','peerla')?>
					<select style="margin-left: 10px;" name="investedTimeMinutes" id="investedTimeMinutes"
							<?=(isset($errors['investedTime']) && $errors['investedTime']) ? ' class="inputError"':''?>>
<?php
	$minutes = $investmentFormater->getMinutePart($goal->getTimeInvestment());
	for ($i=0; $i<60; $i+=15){
		$selected = '';
		if ($i == $minutes){
			$selected = ' selected="selected"';
		}
?>
						<option<?=$selected?> value="<?=$i?>"><?=$i?></option>
<?php
	}
?>
					</select>
					<?=get_string('minutes','peerla')?>
				</div>
				

				<div class="formRow dayOfWeekRow">
					<label>Lerntage</label>
					<div class="dayContainer">
<?php
	$intervalStart = $interval->getStartDate();
	$intervalEnd = $interval->getEndDate();
	$learningDays = $goal->getLearningDays();
	for ($i=$intervalStart; $i<$intervalEnd; $i+=60*60*24){
		$checked = '';
		if (isset($learningDays) && is_array($learningDays) && in_array($i,$learningDays)){
			$checked = ' checked="checked"';
		}
?>
						<input class="dayCheckbox" type="checkbox"<?=$checked?> value="<?=$i?>" name="weekday[]" />
						<span style="margin-right: 10px;"
								<?=(isset($errors['weekday']) 
									&& $errors['weekday']) ? ' class="inputError"':''?>>
							<?=date('d.m.',$i)?>
						</span>
<?php
	}
?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			Wissenstand anpassen
		</h3>
		
		<div class="infoTextContainer">
			<p>
				Schätze deinen neuen Wissenstand nach der Bearbeitung deines Ziels ein.
			</p>
			<p>
				Die Position ganz <b>links</b> bedeutet, dass Du <i>kein Wissen zum Thema</i> besitzt
			</p>
			
			<p>
				Die Position ganz <b>rechts</b> bedeutet, dass Du <i>den Veranstaltungsstoff bereits vollständig beherrschst</i>
			</p>
		</div>

		<div class="goalEditContainer formContent">
<?php

	if (!isset($_GET['showAllTopics']) || !$_GET['showAllTopics']){
?>
				<div>
					<a class="navigationLink" style="margin-bottom: 10px;"
					   href="?courseId=<?=$interval->getCourseId()?>&amp;goalId=<?=$goal->getGoalId()?>&amp;showAllTopics=1">
						<span class="glyphicon glyphicon-align-left"></span>
						Alle Themen anzeigen
					</a>
				</div>
<?php
	}
	else{
?>
				<div>
					<a class="navigationLink" style="margin-bottom: 0;"
					   href="?courseId=<?=$interval->getCourseId()?>&amp;goalId=<?=$goal->getGoalId()?>">
						<span class="glyphicon glyphicon-align-left"></span>
						Nur Themen des Ziels anzeigen
					</a>
				</div>
<?php
	}

	$builder = new KnowledgeEstimationHtmlBuilder();
	$topics = $currentParticipant->getCurrentCourseKnowledge();
	if (!isset($_GET['showAllTopics']) || !$_GET['showAllTopics']){
		$builder->setOnlyShowFromTopicDownwards($goal->getTopic()->getTopicId());
	}
	
	echo $builder->getTopicEstimationHtml($topics);
?>
				
			</div>
			
		</div>
	<input type="submit" name="save" value="speichern" />
</form>


<a class="navigationLink" href="?courseId=<?=$currentCourse->getCourseId()?>&amp;goBack=1">
	<span class="glyphicon glyphicon-expand"></span>
	Zurück
</a>
<?php
include(realpath(__DIR__).'/../includes/footer.php');