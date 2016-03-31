<?php
namespace mod_kom_peerla;

require_once(realpath(__DIR__).'/../../../../config.php');

$includePath = realpath(__DIR__).'/../../classes/';
require_once($includePath.'implementations/dataPresentation/dbDataFetchers/CachedDbTimeInvestmentDataFetcher.php');
require_once($includePath.'helper/TimeInvestmentFormater.php');

if (!isset($_GET['courseId']) || !isset($_GET['topicId']) || !isset($_GET['actionId'])){
	echo json_encode(array(
		'planedAvg' => null,
		'investedAvg' => null
	));
	exit();
}

$dataFetcher = new CachedDbTimeInvestmentDataFetcher($DB);
$planedAvg = $dataFetcher->getAvgPlanedActionTimeInvestment(
		$_GET['courseId'], $_GET['topicId'], $_GET['actionId'], 1);
$investedAvg = $dataFetcher->getAvgActionTimeInvestment(
		$_GET['courseId'], $_GET['topicId'], $_GET['actionId'], 1);

$timeFormater = new TimeInvestmentFormater();
if ($planedAvg){
	$planedAvg = $timeFormater->formatForOutput($planedAvg);
}
if ($investedAvg){
	$investedAvg = $timeFormater->formatForOutput($investedAvg);
}

echo json_encode(array(
	'planedAvg' => $planedAvg,
	'investedAvg' => $investedAvg
));

