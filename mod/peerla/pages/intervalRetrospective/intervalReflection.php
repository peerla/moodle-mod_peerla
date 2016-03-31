<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/coursePlaning/empty.php';
include(realpath(__DIR__).'/../includes/coursePageHeader.php');

$includePath = realpath(__DIR__).'/../../classes/';
require_once($includePath.'implementations/cud/SqlLearningIntervalCud.php');

$participantFactory = new LazyLoadingCourseParticipantFactory($DB);
$currentParticipant = $participantFactory->getCourseParticipant(
		$currentCourse->getCourseId(), $USER->id
);
$interval = $currentParticipant->getCurrentLearningInterval();

//there is already a running interval -> redirect
if ($interval->isRunning() || $interval->retrospectiveDone()) {
	$redirectUrl = new \moodle_url('/mod/peerla/view.php', $paramArray);
	redirect($redirectUrl);
}

$errors = array();
	
$goodText = $interval->getRetrospectiveGoodText();
$badText = $interval->getRetrospectiveBadText();

if (optional_param('saveRetro','',PARAM_TEXT)){
	
	if (isset($_POST['goodText'])){
		$goodText = $_POST['goodText'];
	}
	if (isset($_POST['badText'])){
		$badText = $_POST['badText'];
	}
	
	if (count($errors) == 0){
		$interval->setRetrospectiveBadText($badText);
		$interval->setRetrospectiveGoodText($goodText);
		
		$intervalCud = new SqlLearningIntervalCud($DB);
		$intervalCud->update($interval);
		
		$redirectUrl = new \moodle_url('/mod/peerla/pages/intervalPlaning/createIntervalGoal.php', $paramArray);
		redirect($redirectUrl);
	}
}


//echo $OUTPUT->heading(get_string('HEADLINE_STRING', 'peerla'));

include(realpath(__DIR__).'/../includes/formResult.php');
?>

<form method="post" action="">
	<div class="infoBox">
		<h3>
			<span class="glyphicon glyphicon-edit"></span>
			Intervall Retrospektive
		</h3>
		<div class="infoTextContainer">
			<p>
				Hier kannst Du alles angeben, was im letzten Lernintervall gut oder schlecht lief.
				 Trage einfach alles ein, was du im nächten Intervall beibehalten oder vermeiden möchtest, damit dein
				  Lernen angenehmer und produktivier wird.
			</p>
			<p>
				Gab es z.B. Situation oder Umgebunden, indem Du besonders gut lernen konntest? 
				Haben dich bestimmte Faktoren vom Lernen abgehalten?
			</p>
		</div>
		<div class="formContent retrospectiveForm">
			<div class="formRow">
				<label for="goodText">Was lief gut im letzten Intervall?</label>
				<textarea name="goodText" id="goodText"><?=(isset($goodText) ? $goodText : '')?></textarea>
			</div>

			<div class="formRow">
				<label for="badText">Was leif schlecht im letzten Intervall?</label>
				<textarea name="badText" id="badText"><?=(isset($badText) ? $badText : '')?></textarea>
			</div>
		</div>
	</div>
	
	<input type="submit" name="saveRetro" value="<?=get_string('btn_next','peerla')?>" />
</form>

<?php
include(realpath(__DIR__).'/../includes/footer.php');