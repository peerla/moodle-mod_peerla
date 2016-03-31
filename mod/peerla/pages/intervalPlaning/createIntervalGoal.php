<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/coursePlaning/createIntervalGoal.php';
$includePath = realpath(__DIR__).'/../../classes/';
include(realpath(__DIR__).'/../includes/coursePageHeader.php');
require_once($includePath.'helper/CourseTopicSelectOptionHelper.php');
require_once($includePath.'implementations/cud/SqlIntervalGoalCud.php');
require_once($includePath.'implementations/cud/SqlLearningIntervalCud.php');
require_once($includePath.'implementations/cud/SqlIntervalTopicKnowledgeCud.php');
require_once($includePath.'helper/TimeInvestmentFormater.php');
require_once($includePath.'implementations/IntervalTimeManager.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant($courseId, $USER->id);

$interval = $currentParticipant->getCurrentLearningInterval();

//create new interval, if the last interval has been finished
if (is_null($interval) || (!$interval->isRunning() && $interval->retrospectiveDone())){
	
	$topicKnowledge = $currentParticipant->getCurrentCourseKnowledge();
	
	//create new interval
	$timeManager = new IntervalTimeManager();
	$intervalCud = new SqlLearningIntervalCud($DB);
	$newInterval = new BasicLearningInterval();
	$newInterval->setCourseId($courseId);
	$newInterval->setUserId($USER->id);
	$newInterval->setStartDate($timeManager->getIntervalStartTime(time()));
	$newInterval->setEndDate($timeManager->getIntervalEndTime(time()));

	$intervalId = $intervalCud->create($newInterval,true);

	//copy current knowledge
	$intervalKnowledgeCud = new SqlIntervalTopicKnowledgeCud($DB);
	$intervalKnowledgeCud->copyIntervalKnowledge($topicKnowledge, $intervalId);
	$intervalKnowledgeCud->updateIntervalKnowledgeEstimationTimestamps();
	
	if (!is_null($interval)){
		//copy open interval goals
		$intervalGoalCud = new SqlIntervalGoalCud($DB);
		$intervalGoalCud->copyOpenIntervalGoals($interval, $intervalId);
	}
	
	//load created interval
	$intervalFactory = new LazyLoadingLearningIntervalFactory($DB);
	$interval = $intervalFactory->getLearningInterval($intervalId);
}

//intervall still is active or the retrospective has not been finished? -> redirect
if ($interval->userHasFinishedGoalSetting()){
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

/*
if (is_null($interval) || !$interval->isRunning() || !$interval->retrospectiveDone()){
	$redirectUrl = new \moodle_url('/mod/peerla/pages/intervalPlaning/courseKnowledge.php', $paramArray);
	redirect($redirectUrl);
}
 */

$errors = array();
$goals = array();
if (optional_param('save','',PARAM_TEXT)){
	
	if (!isset($_POST['goal']) || !is_array($_POST['goal']) || count($_POST['goal']) == 0){
		$errors[] = get_string('error_no_interval_goal');
	}
	else{
		//check each goal for the required data
		foreach($_POST['goal'] as $index => $goalData){
			
			//init goal objects
			$goal = new BasicLearningIntervalGoal();
			$goal->setCreateTimestamp(time());
			$goal->setIntervalId($interval->getIntervalId());
			$goal->setStatus('open');
			$goal->setUserId($USER->id);
			$goal->setCourseId($courseId);
			
			if (isset($goalData['intervalStatus']) && (
					$goalData['intervalStatus'] == 'open'
					|| $goalData['intervalStatus'] == 'done'
					|| $goalData['intervalStatus'] == 'cancelled')){
				$goal->setStatus($goalData['intervalStatus']);
			}
			else{
				$goal->setStatus('open');
			}
			
			if (!isset($goalData['actionSelect']) || !$goalData['actionSelect']){
				$errors['actionSelect_'.$index] = 1;
			}
			else{
				$goal->setActionId($goalData['actionSelect']);
			}
			
			if (!$goalData['topicSelect']){
				$errors['topicSelect_'.$index] = 1;
			}
			else{
				$goal->setTopicId($goalData['topicSelect']);
			}
			
			if (!isset($goalData['planedTimeInvestmentHours']) 
					|| !isset($goalData['planedTimeInvestmentMinutes'])
					|| (!$goalData['planedTimeInvestmentHours'] 
								&& !$goalData['planedTimeInvestmentMinutes'])){
				$errors['planedTimeInvestment_'.$index] = 1;
			}
			else{
				$goal->setPlanedTimeInvestment(
						$goalData['planedTimeInvestmentHours']*60
						+$goalData['planedTimeInvestmentMinutes']);
			}
			
			if (isset($goalData['goalId']) && $goalData['goalId']){
				$goal->setGoalId($goalData['goalId']);
			}
			
			if (isset($goalData['comment']) && $goalData['comment']){
				$goal->setGoalComment($goalData['comment']);
			}
			
			if (!isset($goalData['weekday']) || !is_array($goalData['weekday']) 
					|| count($goalData['weekday']) == 0){
				$errors['weekday_'.$index] = 1;
			}
			else{
				$goal->setPlanedLearningDays($goalData['weekday']);
			}
			
			$goals[] = $goal;
		}
	}
	
	if (count($errors) === 0){
		
		
		//create new interval goals
		$intervalGoalCud = new SqlIntervalGoalCud($DB);
		foreach($goals as $goal){
			$goal->setIntervalId($interval->getIntervalId());
			$intervalGoalCud->create($goal);
			$intervalGoalCud->updateIntervalGoalSettingTimestamps();
		}
		
		$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
		redirect($redirectUrl);
	}
}
else{
	//load existing data, if no data was submitted
	//$goals = $interval->getIntervalGoals();
}

$intervalEndDate = userdate($interval->getEndDate(),get_string('strftimedateshort'));

$headline = get_string('interval_goal_planing_headline_part1', 'peerla');
$headline .= $intervalEndDate;
$headline .= get_string('interval_goal_planing_headline_part2', 'peerla');
//echo $OUTPUT->heading($headline);

$actionFactory = new LazyLoadingIntervalGoalActionFactory($DB);
$actions = $actionFactory->getActionsVisibleToUser($currentParticipant->getUserId());

$topicFactory = new LazyLoadingCourseTopicFactory($DB);
$topics = $topicFactory->getCourseTopicsVisibleToParticipant(
		$currentParticipant->getCourseId(), $currentParticipant->getUserId());

$topicOptionsHelper = new CourseTopicSelectOptionHelper();

//no goals exist? => add a dummy goal for the form creation
if (count($goals) == 0){
	$goal = new BasicLearningIntervalGoal();
	$goal->setActionId('');
	$goal->setGoalComment('');
	$goal->setPlanedTimeInvestment('');
	$goal->setTopicId('');
	$goals[] = $goal;
}

include(realpath(__DIR__).'/../includes/formResult.php');

$investmentFormater = new TimeInvestmentFormater();
?><script>
$(function(){
	$('.planedTimeInvestmentHours').change(function(){displayTimeAvg($(this).parents('.intervalGoalContainer'))});
	$('.planedTimeInvestmentMinutes').change(function(){displayTimeAvg($(this).parents('.intervalGoalContainer'))});
	$('.topicSelect').change(function(){displayTimeAvg($(this).parents('.intervalGoalContainer'))});
	$('.actionSelect').change(function(){displayTimeAvg($(this).parents('.intervalGoalContainer'))});
});

</script>
	<input type="hidden" name="courseId" value="<?=$courseId?>" id="courseId" />
<div class="infoBox">
	<h3>
		<span class="glyphicon glyphicon-info-sign"></span>
		<?=$headline?>
	</h3>
	<div class="infoTextContainer">
		<p>
			Plane Dein Lernen von Heute bis zum <?=$intervalEndDate?>. Was genau 
			möchtest du bis dahin machen?
		</p>
		<p>
			Erstelle dafür ein Ziel für jede Aufgabe, die du erledigen möchtest. 
			Bei den Themenbereichen kannst du selber wählen, ob du einen kompletten
			Themenbereich oder einen der Unterthemen angeben möchtest.
		</p>
		<p>
			Nimm dir etwas Zeit für die Planung und überleg dir wann du 
			wie viel Zeit für welche Inhalte investieren möchtest.
		</p>
	</div>
</div>
<style>
	.intervalGoalContainer .formRow label {width: 300px;}
</style>
<form method="post" action="">
<?php

$goalCounter = 0;
foreach($goals as $goal){
	$goalCounter++;
?>
	
	<div class="intervalGoalContainer infoBox">
		
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			<?=get_string('goal','peerla')?>
			 <span class="intervalGoalNumber"><?=$goalCounter?></span>
		</h3>
		
		<div class="formContent">
		<input class="intervalGoalIdInput" type="hidden" 
			   name="goal[<?=$goalCounter?>][intervalGoalId]" 
			   value="<?=$goal->getGoalId()?>" />
		<input class="intervalStatusInput" type="hidden" 
			   name="goal[<?=$goalCounter?>][intervalStatus]" 
			   value="<?=$goal->getStatus()?>" />
		
		<div class="goalSelect formRow">
			<label>
				Ich möchte bis zum 
				<?=$intervalEndDate?>
				im Bereich
			</label>
			<select class="topicSelect<?=(isset($errors['topicSelect_'.$goalCounter])) ? ' inputError':''?>" 
					name="goal[<?=$goalCounter?>][topicSelect]">
				<?=$topicOptionsHelper->getSelectOptions($topics, $goal->getTopicId())?>
			</select>
		</div>
		<div class="goalSelect formRow">
			<label></label>
			<select class="actionSelect<?=(isset($errors['actionSelect_'.$goalCounter])) ? ' inputError':''?>" 
					name="goal[<?=$goalCounter?>][actionSelect]">
<?php
	foreach($actions as $intervalGaolAction){
		$selected = '';
		if ($intervalGaolAction->getActionId() == $goal->getActionId()){
			$selected = ' selected="selected"';
		}
?>
			<option<?=$selected?> value="<?=$intervalGaolAction->getActionId()?>">
				<?=$intervalGaolAction->getActionName()?>
			</option>
<?php
	}
?>
			</select>
		</div>
		<div class="bottomContent">

			<div class="leftBottomContent">
				
				
				<div class="formRow commentRow">
					<label><?=get_string('goal_label_comment','peerla')?></label>
					<textarea class="commentInput<?=(isset($errors['comment_'.$goalCounter])) ? ' inputError':''?>" 
							  name="goal[<?=$goalCounter?>][comment]"><?=$goal->getGoalComment()?></textarea>
				</div>
				
				<div class="formRow planedTimeInvestmentRow">
					<label>
						<?=get_string('goal_label_planed_time_investment', 'peerla')?>
					</label>
					<select name="goal[<?=$goalCounter?>][planedTimeInvestmentHours]"
							class="planedTimeInvestmentHours
							<?=(isset($errors['planedTimeInvestment_'.$goalCounter]) 
								&& $errors['planedTimeInvestment_'.$goalCounter]) ? ' inputError':''?>"
							>
<?php
	$hours = $investmentFormater->getHourPart($goal->getPlanedTimeInvestment());
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
					<select style="margin-left: 10px;" name="goal[<?=$goalCounter?>][planedTimeInvestmentMinutes]"
							class="planedTimeInvestmentMinutes
							<?=(isset($errors['planedTimeInvestment_'.$goalCounter]) 
								&& $errors['planedTimeInvestment_'.$goalCounter]) ? ' inputError':''?>"
							>
<?php
	$minutes = $investmentFormater->getMinutePart($goal->getPlanedTimeInvestment());
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
				
				<div class="formRow">
					<label></label>
					<div class="goalCreateTimeBox">

					</div>
				</div>
				
				<div class="formRow dayOfWeekRow">
					<label>Lerntage</label>
					<div class="dayContainer">
<?php
	$intervalStart = $interval->getStartDate();
	$intervalEnd = $interval->getEndDate();
	$learningDays = $goal->getPlanedLearningDays();
	for ($i=$intervalStart; $i<$intervalEnd; $i+=60*60*24){
		$checked = '';
		if (isset($learningDays) && is_array($learningDays) && in_array($i,$learningDays)){
			$checked = ' checked="checked"';
		}
?>
						<input class="dayCheckbox" type="checkbox"<?=$checked?> value="<?=$i?>" name="goal[<?=$goalCounter?>][weekday][]" />
						<span style="margin-right: 10px;"
								<?=(isset($errors['weekday_'.$goalCounter]) 
									&& $errors['weekday_'.$goalCounter]) ? ' class="inputError"':''?>>
							<?=date('d.m.',$i)?>
						</span>
<?php
	}
?>
					</div>
				</div>
				
			</div>

			<div style="padding-bottom: 10px;" class="rightBottomContent">

			</div>

			<a class="removeIntervalGoal navigationLink" href="#">
				<span class="glyphicon glyphicon-remove-sign"></span>
				<?=get_string('link_remove_interval_goal','peerla')?>
			</a>

		</div>
	</div>
	</div>
<?php	
}
?>
	<div style="padding: 15px 0 25px 0;" class="addIntervalContainer">
		<a class="addIntervalGoal navigationLink" href="#">
			<span class=" glyphicon glyphicon-plus-sign"></span>
			<?=get_string('link_add_interval_goal','peerla')?>
		</a>
	</div>
	<input type="submit" name="save" value="<?=get_string('btn_next','peerla')?>" />
</form>

<?php
include(realpath(__DIR__).'/../includes/footer.php');