<?php
namespace mod_kom_peerla;

$url = '/mod/peerla/pages/dbInit.php';
include(realpath(__DIR__).'/includes/coursePageHeader.php');

$includePath = realpath(__DIR__).'/../classes/';

$sqlTopicDelete = "TRUNCATE {course_topic}";

//$dbCourseId = $courseId;
//$dbCourseId = 10;

if (!$_GET['courseIds'] || !is_array($_GET['courseIds'])){
	exit('false');
}

$index = 0;
foreach ($_GET['courseIds'] as $dbCourseId){
	$index++;
	
	$sqlTopicsInsert[$index] = "
	INSERT INTO {course_topic} (`id`, `courseid`, `parentid`, `creator_userid`, `name`, `public`, `course_scope`, `create_timestamp`, `delete_timestamp`) VALUES
	(".$index."1, ".$dbCourseId.", NULL, NULL, 'Rechengesetze', 1, 0, 0, NULL),
	(".$index."2, ".$dbCourseId.", NULL, NULL, 'Potenzen', 1, 0, 0, NULL),
	(".$index."3, ".$dbCourseId.", NULL, NULL, 'Funktionen', 1, 0, 0, NULL),
	(".$index."4, ".$dbCourseId.", NULL, NULL, 'Höhere Funktionen', 1, 0, 0, NULL),
	(".$index."5, ".$dbCourseId.", NULL, NULL, 'Analysis', 1, 0, 0, NULL),
	(".$index."6, ".$dbCourseId.", NULL, NULL, 'Vektorrechnung', 1, 0, 0, NULL),
	(".$index."70, ".$dbCourseId.", NULL, NULL, 'Logik', 1, 0, 0, NULL),";

	//rechengesetze unterthemen
	$sqlTopicsInsert[$index] .= "
	(".$index."7, ".$dbCourseId.", ".$index."1, NULL, 'Körperaxiome und Rechenregeln', 1, 0, 0, NULL),
	(".$index."8, ".$dbCourseId.", ".$index."1, NULL, 'Ungleichungen', 1, 0, 0, NULL),
	(".$index."9, ".$dbCourseId.", ".$index."1, NULL, 'Mengen von Zahlen', 1, 0, 0, NULL),
	(".$index."10, ".$dbCourseId.", ".$index."1, NULL, 'Arithmetik', 1, 0, 0, NULL),";
	//(11, ".$dbCourseId.", 1, NULL, 'Logik und Beweis', 1, 0, 0, NULL),

	$sqlTopicsInsert[$index] .= "
	(".$index."12, ".$dbCourseId.", ".$index."7, NULL, 'Binomische Formeln', 1, 0, 0, NULL),
	(".$index."13, ".$dbCourseId.", ".$index."7, NULL, 'Rechenregeln und Termumformungen', 1, 0, 0, NULL),
	(".$index."14, ".$dbCourseId.", ".$index."7, NULL, 'Elementare Gleichungen', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."15, ".$dbCourseId.", ".$index."8, NULL, 'Anordunungen', 1, 0, 0, NULL),
	(".$index."16, ".$dbCourseId.", ".$index."8, NULL, 'Betrag', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."17, ".$dbCourseId.", ".$index."9, NULL, 'Grundlagen', 1, 0, 0, NULL),
	(".$index."18, ".$dbCourseId.", ".$index."9, NULL, 'Mengenoperationen', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."19, ".$dbCourseId.", ".$index."10, NULL, 'Stellenwertsystem', 1, 0, 0, NULL),
	(".$index."20, ".$dbCourseId.", ".$index."10, NULL, 'Teilbarkeit', 1, 0, 0, NULL),";
	/*
	$sqlTopicsInsert .= "
	(21, ".$dbCourseId.", 11, NULL, 'Aussagen und Wahrheitswerte', 1, 0, 0, NULL),
	(22, ".$dbCourseId.", 11, NULL, 'Wenn-dann-Aussagen und Äquivalenzen', 1, 0, 0, NULL),
	(23, ".$dbCourseId.", 11, NULL, 'Beweisstrategien, Methodik und Formalia', 1, 0, 0, NULL),";
	*/

	//Potenzen unterthemen
	$sqlTopicsInsert[$index] .= "
	(".$index."24, ".$dbCourseId.", ".$index."2, NULL, 'Potenzen mit ganzzahligen Exponenten', 1, 0, 0, NULL),
	(".$index."25, ".$dbCourseId.", ".$index."24, NULL, 'Rechengesetze', 1, 0, 0, NULL),
	(".$index."26, ".$dbCourseId.", ".$index."24, NULL, 'Die geometrische Folge und die geometrische Reihe', 1, 0, 0, NULL),
	(".$index."27, ".$dbCourseId.", ".$index."24, NULL, 'Binomialkoeffizient und der binomische Lehrsatz', 1, 0, 0, NULL),
	(".$index."28, ".$dbCourseId.", ".$index."24, NULL, 'Zinsrechnung', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."29, ".$dbCourseId.", ".$index."2, NULL, 'Potenzen mit rationalen Exponenten', 1, 0, 0, NULL),
	(".$index."30, ".$dbCourseId.", ".$index."29, NULL, 'Quadratwurzeln und rationale Exponenten', 1, 0, 0, NULL),
	(".$index."31, ".$dbCourseId.", ".$index."29, NULL, 'Quadratische Gleichungen', 1, 0, 0, NULL),";


	//Funktionen unterthemen
	$sqlTopicsInsert[$index] .= "
	(".$index."33, ".$dbCourseId.", ".$index."3, NULL, 'Lineare Funktionen', 1, 0, 0, NULL),
	(".$index."34, ".$dbCourseId.", ".$index."3, NULL, 'Quadratische Funktionen', 1, 0, 0, NULL),
	(".$index."35, ".$dbCourseId.", ".$index."3, NULL, 'Funktionen und ihre Eigenschaften', 1, 0, 0, NULL),";


	//Höhere Funktionen unterthemen
	$sqlTopicsInsert[$index] .= "
	(".$index."36, ".$dbCourseId.", ".$index."4, NULL, 'Polynome', 1, 0, 0, NULL),
	(".$index."37, ".$dbCourseId.", ".$index."36, NULL, 'Polynomfunktionen', 1, 0, 0, NULL),
	(".$index."38, ".$dbCourseId.", ".$index."36, NULL, 'Hornerschema', 1, 0, 0, NULL),
	(".$index."39, ".$dbCourseId.", ".$index."36, NULL, 'Polynomdivision', 1, 0, 0, NULL),
	(".$index."40, ".$dbCourseId.", ".$index."36, NULL, 'Nullstellen', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."41, ".$dbCourseId.", ".$index."4, NULL, 'Exponential- und Logarithmusfunktion', 1, 0, 0, NULL),
	(".$index."42, ".$dbCourseId.", ".$index."41, NULL, 'Potenz- und Logarithmengesetze', 1, 0, 0, NULL),
	(".$index."43, ".$dbCourseId.", ".$index."41, NULL, 'Die allgemeine Exponentialfunktion', 1, 0, 0, NULL),
	(".$index."44, ".$dbCourseId.", ".$index."41, NULL, 'Die Exponentialfunktion zur Basis e', 1, 0, 0, NULL),
	(".$index."45, ".$dbCourseId.", ".$index."41, NULL, 'Der natürliche Logarithmus', 1, 0, 0, NULL),
	(".$index."46, ".$dbCourseId.", ".$index."41, NULL, 'Allgemeine Potenzen und Logarithmen', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."47, ".$dbCourseId.", ".$index."4, NULL, 'Trigonometrische Funktionen', 1, 0, 0, NULL),
	(".$index."48, ".$dbCourseId.", ".$index."47, NULL, 'Strahlensätze', 1, 0, 0, NULL),
	(".$index."49, ".$dbCourseId.", ".$index."47, NULL, 'Die Zahl pi, das Grad- und das Bogenmaß', 1, 0, 0, NULL),
	(".$index."50, ".$dbCourseId.", ".$index."47, NULL, 'Sinus, Kosinus und Tangens am rechtwinkligen Dreieck', 1, 0, 0, NULL),
	(".$index."51, ".$dbCourseId.", ".$index."47, NULL, 'Winkelfunktionen am allgemeinen Dreieck', 1, 0, 0, NULL),
	(".$index."52, ".$dbCourseId.", ".$index."47, NULL, 'Winkelfunktionen am Einheitskreis', 1, 0, 0, NULL),
	(".$index."53, ".$dbCourseId.", ".$index."47, NULL, 'Funktionen periodischer Vorgänge', 1, 0, 0, NULL),";


	//Analysis unterthemen
	$sqlTopicsInsert[$index] .= "
	(".$index."54, ".$dbCourseId.", ".$index."5, NULL, 'Folgen und Grenzwerte', 1, 0, 0, NULL),
	(".$index."55, ".$dbCourseId.", ".$index."54, NULL, 'Zahlenfolgen', 1, 0, 0, NULL),
	(".$index."56, ".$dbCourseId.", ".$index."54, NULL, 'Grenzwerte von Folgen', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."57, ".$dbCourseId.", ".$index."5, NULL, 'Grenzwerte von Funktionen und Stetigkeit', 1, 0, 0, NULL),
	(".$index."58, ".$dbCourseId.", ".$index."57, NULL, 'Grenzwerte von Funktionen', 1, 0, 0, NULL),
	(".$index."59, ".$dbCourseId.", ".$index."57, NULL, 'Stetigkeit', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."60, ".$dbCourseId.", ".$index."5, NULL, 'Differentialrechnung', 1, 0, 0, NULL),
	(".$index."61, ".$dbCourseId.", ".$index."60, NULL, 'Differenzierbarkeit', 1, 0, 0, NULL),
	(".$index."162, ".$dbCourseId.", ".$index."60, NULL, 'Interpretation erster und höherer Ableitungen', 1, 0, 0, NULL),
	(".$index."163, ".$dbCourseId.", ".$index."60, NULL, 'Ableitungsregeln', 1, 0, 0, NULL),
	(".$index."164, ".$dbCourseId.", ".$index."60, NULL, 'Lokale Extrema und Wendepunkte', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."155, ".$dbCourseId.", ".$index."5, NULL, 'Kurvendiskussion', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."62, ".$dbCourseId.", ".$index."5, NULL, 'Integralrechnung', 1, 0, 0, NULL),
	(".$index."63, ".$dbCourseId.", ".$index."62, NULL, 'Flächenberechnung und Integralbegriff', 1, 0, 0, NULL),
	(".$index."64, ".$dbCourseId.", ".$index."62, NULL, 'Integrale berechnen: Der Hauptsatz', 1, 0, 0, NULL),
	(".$index."65, ".$dbCourseId.", ".$index."62, NULL, 'Partielle Integration', 1, 0, 0, NULL),
	(".$index."71, ".$dbCourseId.", ".$index."62, NULL, 'Substitution', 1, 0, 0, NULL),
	(".$index."72, ".$dbCourseId.", ".$index."62, NULL, 'Integration gebrochen-rationaler Funktionen', 1, 0, 0, NULL),";


	$sqlTopicsInsert[$index] .= "
	(".$index."66, ".$dbCourseId.", ".$index."6, NULL, 'Vektoren', 1, 0, 0, NULL),
	(".$index."67, ".$dbCourseId.", ".$index."6, NULL, 'Geraden und Ebenen', 1, 0, 0, NULL),
	(".$index."68, ".$dbCourseId.", ".$index."6, NULL, 'Abstände und Winkel', 1, 0, 0, NULL),";

	//Logik unterthemen
	$sqlTopicsInsert[$index] .= "
	(".$index."73, ".$dbCourseId.", ".$index."70, NULL, 'Logik kompakt', 1, 0, 0, NULL),
	(".$index."74, ".$dbCourseId.", ".$index."73, NULL, 'Aussagen und Wahrheitswerte', 1, 0, 0, NULL),
	(".$index."75, ".$dbCourseId.", ".$index."73, NULL, 'Wenn-dann-Aussagen und Äquivalenzen', 1, 0, 0, NULL),
	(".$index."76, ".$dbCourseId.", ".$index."73, NULL, 'Beweisstrategien, Methodik und Formalia', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."77, ".$dbCourseId.", ".$index."70, NULL, 'Aussagenlogik', 1, 0, 0, NULL),
	(".$index."78, ".$dbCourseId.", ".$index."77, NULL, 'Aufbau der Aussagenlogik', 1, 0, 0, NULL),
	(".$index."79, ".$dbCourseId.", ".$index."77, NULL, 'Negation', 1, 0, 0, NULL),
	(".$index."80, ".$dbCourseId.", ".$index."77, NULL, 'Konjunktionen und Disjunktionen', 1, 0, 0, NULL),
	(".$index."81, ".$dbCourseId.", ".$index."77, NULL, 'Implikationen und Äquivalenzen', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."82, ".$dbCourseId.", ".$index."70, NULL, 'Prädikatenlogik', 1, 0, 0, NULL),";

	$sqlTopicsInsert[$index] .= "
	(".$index."83, ".$dbCourseId.", ".$index."70, NULL, 'Logische Schlussweisen', 1, 0, 0, NULL)";

}

$sqlActivityDelete = "TRUNCATE {interval_goal_action}";
$sqlActivityInsert = "INSERT INTO {interval_goal_action} (`id`, `action`) "
		. "VALUES (1, 'Modul-Theorieteil bearbeiten'), "
		. "(2, 'Modul-Übungsaufgaben bearbeiten'), "
		. "(3, 'Modul wiederholen'), "
		. "(4, 'Vortest bearbeiten'), "
		. "(5, 'Nachtest bearbeiten'), "
		. "(6, 'sonstiges')";

$transaction = $DB->start_delegated_transaction();
	
try{
	
	$DB->execute($sqlTopicDelete,array());
	
	foreach($sqlTopicsInsert as $insertSql){
		$DB->execute($insertSql,array());
	}
	
	$DB->execute($sqlActivityDelete,array());
	$DB->execute($sqlActivityInsert,array());
	
	$transaction->allow_commit();
	
	echo 'done';
} catch (Exception $ex) {
	$transaction->rollback($e);
	$ex->getMessage();
	echo 'fehler';
}


//echo $OUTPUT->heading(get_string('HEADLINE_STRING', 'peerla'));
?>



<?php
include(realpath(__DIR__).'/includes/footer.php');