<?php

namespace mod_kom_peerla;

/* @var $PAGE moodle_page */
/* @var $OUTPUT theme_bootstrapbase_core_renderer */
/* @var $USER stdClass */
require_once('../../config.php');

require_once(realpath(__DIR__).'/classes/implementations/factories/LazyLoadingCourseFactory.php');
require_once('classes/implementations/factories/LazyLoadingUserFactory.php');

$id = required_param('id', PARAM_INT); // Course.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$systemContext = \context_system::instance();
$url = new \moodle_url('/mod/peerla/index.php',array('id' => $id));

$PAGE->set_context($systemContext);
$PAGE->set_url($url);
$PAGE->set_title('testseite');
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();
echo $OUTPUT->heading('test');

$courseFactory = new LazyLoadingCourseFactory($DB);
$courses = $courseFactory->getCurrentCoursesForUser($USER->id);
/* @var $courses Course[] */


echo 'aktive Lernziele<br/>';
foreach($courses as $course){
	echo $course->getName().'<br/>';
	$participant = $course->getParticipant($USER->id);
	$currentInterval = $participant->getCurrentLearningInterval();
	if (isset($currentInterval)){
		echo $course->getName().'<br/>';
		$goals = $currentInterval->getIntervalGoals();
		foreach($goals as $goal){
			echo $goal->getGoalText().'<br />';
		}
	}
}

echo $OUTPUT->footer();