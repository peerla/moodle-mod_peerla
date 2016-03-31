<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/interval/goalCreate.php';
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
if (is_null($interval) || !$interval->isRunning() || !$interval->userHasFinishedGoalSetting()){
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

$errors = array();

if (optional_param('save','',PARAM_TEXT)){
	$goalData = $_POST;
	
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
		$errors['actionSelect'] = 1;
	}
	else{
		$goal->setActionId($goalData['actionSelect']);
	}

	if (!$goalData['topicSelect']){
		$errors['topicSelect'] = 1;
	}
	else{
		$goal->setTopicId($goalData['topicSelect']);
	}

	if (!isset($goalData['planedTimeInvestmentHours']) 
			|| !isset($goalData['planedTimeInvestmentMinutes'])
			|| (!$goalData['planedTimeInvestmentHours'] 
						&& !$goalData['planedTimeInvestmentMinutes'])){
		$errors['planedTimeInvestment'] = 1;
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
		$errors['weekday'] = 1;
	}
	else{
		$goal->setPlanedLearningDays($goalData['weekday']);
	}
	
	if (count($errors) == 0){
		$intervalGoalCud = new SqlIntervalGoalCud($DB);
		$goal->setIntervalId($interval->getIntervalId());
		$intervalGoalCud->create($goal);
		
		$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
		redirect($redirectUrl);
	}
}



if (!isset($goal)){
	$goal = new BasicLearningIntervalGoal();
	$goal->setActionId('');
	$goal->setGoalComment('');
	$goal->setPlanedTimeInvestment('');
	$goal->setTopicId('');
}

include(realpath(__DIR__).'/../includes/formResult.php');

$investmentFormater = new TimeInvestmentFormater();
$intervalEndDate = userdate($interval->getEndDate(),get_string('strftimedateshort'));

$topicFactory = new LazyLoadingCourseTopicFactory($DB);
$topics = $topicFactory->getCourseTopicsVisibleToParticipant(
		$currentParticipant->getCourseId(), $currentParticipant->getUserId());
$topicOptionsHelper = new CourseTopicSelectOptionHelper();

$actionFactory = new LazyLoadingIntervalGoalActionFactory($DB);
$actions = $actionFactory->getActionsVisibleToUser($currentParticipant->getUserId());
?>
<script>
$(function(){
	$('.planedTimeInvestmentHours').change(function(){displayTimeAvg($('.intervalGoalContainer'))});
	$('.planedTimeInvestmentMinutes').change(function(){displayTimeAvg($('.intervalGoalContainer'))});
	$('.topicSelect').change(function(){displayTimeAvg($('.intervalGoalContainer'))});
	$('.actionSelect').change(function(){displayTimeAvg($('.intervalGoalContainer'))});
});
</script>
<form method="post" action="">
	<input type="hidden" name="courseId" value="<?=$courseId?>" id="courseId" />
<div class="intervalGoalContainer infoBox">
		
	<h3>
		<span class="glyphicon glyphicon-edit"></span>
		Intervallziel erstellen
	</h3>

	<div class="formContent">
	<input class="intervalGoalIdInput" type="hidden" 
		   name="intervalGoalId" 
		   value="<?=$goal->getGoalId()?>" />
	<input class="intervalStatusInput" type="hidden" 
		   name="intervalStatus" 
		   value="<?=$goal->getStatus()?>" />

	<div class="goalSelect formRow">
		<label>
			Bis zum 
			<?=$intervalEndDate?>:
		</label>
		<select class="topicSelect<?=(isset($errors['topicSelect'])) ? ' inputError':''?>" 
				name="topicSelect">
			<?=$topicOptionsHelper->getSelectOptions($topics, $goal->getTopicId())?>
		</select>

		<select class="actionSelect<?=(isset($errors['actionSelect'])) ? ' inputError':''?>" 
				name="actionSelect">
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
				<textarea class="commentInput<?=(isset($errors['comment'])) ? ' inputError':''?>" 
						  name="comment"><?=$goal->getGoalComment()?></textarea>
			</div>

			<div class="formRow planedTimeInvestmentRow">
				<label>
					<?=get_string('goal_label_planed_time_investment', 'peerla')?>
				</label>
				<select name="planedTimeInvestmentHours"
						class="planedTimeInvestmentHours
						<?=(isset($errors['planedTimeInvestment']) 
							&& $errors['planedTimeInvestment']) ? ' inputError':''?>"
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
				<select style="margin-left: 10px;" name="planedTimeInvestmentMinutes"
						class="planedTimeInvestmentMinutes
						<?=(isset($errors['planedTimeInvestment']) 
							&& $errors['planedTimeInvestment']) ? ' inputError':''?>"
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
</div>
	<input type="submit" name="save" value="speichern" />
</form>

<?php
include(realpath(__DIR__).'/../includes/footer.php');