<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/endInterval.php';
include(realpath(__DIR__).'/includes/coursePageHeader.php');

$includePath = realpath(__DIR__).'/../classes/';
require_once($includePath.'implementations/IntervalTimeManager.php');
	
$sql = "UPDATE {learning_interval}"
		. "	SET end_timestamp = :end"
		. "	WHERE userid = :userid"
		. "		AND current_user_interval = 1"
		. "		AND courseid = :courseid";

$intervalTime = new IntervalTimeManager();

$DB->execute($sql, array(
	'userid' => $USER->id,
	'end' => $intervalTime->getIntervalStartTime(time()),
	'courseid' => $courseId
));

echo 'done';

//echo $OUTPUT->heading(get_string('HEADLINE_STRING', 'peerla'));
?>



<?php
include(realpath(__DIR__).'/includes/footer.php');