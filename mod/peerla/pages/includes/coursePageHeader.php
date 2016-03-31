<?php

/* @var $PAGE moodle_page */
/* @var $OUTPUT theme_bootstrapbase_core_renderer */
/* @var $USER stdClass */
require_once(realpath(__DIR__).'/../../../../config.php');

require_once(realpath(__DIR__).'/../../classes/implementations/factories/LazyLoadingCourseFactory.php');
require_once(realpath(__DIR__).'/../../classes/implementations/factories/LazyLoadingUserFactory.php');

$courseModuleId = optional_param('id', 0, PARAM_INT); 
$moduleInstanceId  = optional_param('p', 0, PARAM_INT);
$courseId = optional_param('courseId', 0, PARAM_INT);

$courseFactory = new \mod_kom_peerla\LazyLoadingCourseFactory($DB);

$paramArray = array();
if ($courseModuleId) {
	$currentCourse = $courseFactory->getCourseFromCourseModuleId($courseModuleId);
	$paramArray['id'] = $courseModuleId;
	$courseId = $currentCourse->getCourseId();
} elseif ($moduleInstanceId) {
    $currentCourse = $courseFactory->getCourseFromModuleInstanceId($moduleInstanceId);
	$paramArray['p'] = $moduleInstanceIds;
	$courseId = $currentCourse->getCourseId();
} elseif ($courseId) {
    $currentCourse = $courseFactory->getCourse($courseId);
	$paramArray['courseId'] = $courseId;
}

if (!isset($currentCourse) || is_null($currentCourse)){
	print_error('error_course_not_found','peerla');
}

require_login($currentCourse->getCourseId(), true);

if (isset($url) && $url){
	$PAGE->set_url($url, $paramArray);
	if (isset($title) && $title){
		$PAGE->set_title('testseite');
	}
	$PAGE->set_pagelayout('incourse');

	echo $OUTPUT->header();
}
//<script src="http://code.jquery.com/jquery.min.js"></script>
?>
<script src="<?=$CFG->wwwroot?>/mod/peerla/js/jquery.min.js"></script>
<script src="<?=$CFG->wwwroot?>/mod/peerla/js/jquery-ui.min.js"></script>
<script src="<?=$CFG->wwwroot?>/mod/peerla/js/d3.min.js"></script>
<script src="<?=$CFG->wwwroot?>/mod/peerla/js/peerla.js"></script>
<script src="<?=$CFG->wwwroot?>/mod/peerla/js/barChart.js"></script>
<link rel="stylesheet" href="<?=$CFG->wwwroot?>/mod/peerla/css/jquery-ui.min.css" />
<link rel="stylesheet" href="<?=$CFG->wwwroot?>/mod/peerla/css/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?=$CFG->wwwroot?>/mod/peerla/css/peerla.css" />
<div class="peerLaContainer">