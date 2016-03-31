<?php
namespace mod_kom_peerla;

require_once($includePath.'implementations/dataPresentation/KnowledgeDataProvider.php');
require_once($includePath.'implementations/dataPresentation/TimeInvestmentOwnInvestmentDataProvider.php');
require_once($includePath.'implementations/dataPresentation/BarChartDataPresentation.php');



$timeInvProvider = new TimeInvestmentOwnInvestmentDataProvider($DB, $courseId, $USER->id);
$knowledgeProvider = new KnowledgeDataProvider($DB, $courseId, $USER->id);

$presenterKnowledge = new BarChartDataPresentation();
$knowDescription = 'Die Grafik zeigt Deinen aktuellen Wissenstand im Vergleich';
$knowDescription .= ' zu verschiedenen Referenzgrößen. Dies soll Dir ein Gefühl';
$knowDescription .= ' dafür geben, wie dein aktueller Wissenstand im Vergleich zu ';
$knowDescription .= ' Deinen Zielen und dem Wissenstand Deiner Komilitonen darsteht.';
$presenterKnowledge->setGraphDescription('#knowledgeStatText', '');
$presenterKnowledge->setData($knowledgeProvider->getDataView());

$presenterTime = new BarChartDataPresentation();
$presenterTime->setGraphDescription('#timeStatText', '');
$presenterTime->setData($timeInvProvider->getDataView());

?>

<div class="infoBox">
	<h3>
		<span class="glyphicon glyphicon-stats"></span>
		Wissenstand
	</h3>
	<div class="infoTextMore infoTextContainer">
		<p><?=$knowDescription?></p>
	</div>
	<div class="graphContainer">
		<div id="knowledgeStatGraph" class="statsLeft">
			<?=$presenterKnowledge->getHtmlString('#knowledgeStatGraph')?>
		</div>
		<div id="knowledgeStatText" class="statsRight">

		</div>
	</div>
	<a class="infoMoreTextLink" data-toggletext="Beschreibung verbergen">
		<span class="glyphicon glyphicon glyphicon-menu-down"></span>
		<span class="toggleText">Beschreibung anzeigen</span>
	</a>
</div>

<div class="infoBox">
	<h3>
		<span class="glyphicon glyphicon-stats"></span>
		Zeitinvestment
	</h3>
	<div class="infoTextMore infoTextContainer">
		<p><?=$knowDescription?></p>
	</div>
	<div class="graphContainer">
		<div id="timeStatGraph" class="statsLeft">
			<?=$presenterTime->getHtmlString('#timeStatGraph')?>
		</div>
		<div id="timeStatText" class="statsRight">

		</div>
	</div>
	<a class="infoMoreTextLink" data-toggletext="Beschreibung verbergen">
		<span class="glyphicon glyphicon glyphicon-menu-down"></span>
		<span class="toggleText">Beschreibung anzeigen</span>
	</a>
</div>