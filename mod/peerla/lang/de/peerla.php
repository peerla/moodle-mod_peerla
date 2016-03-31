<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * English strings for newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_newmodule
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'PeerLA';
$string['course_planing'] = 'Kurs planen';

//view
$string['view_course_goal_headline'] = 'Deine Kursziele';
$string['view_interval_goal_headline_part1'] = 'Deine Ziele vom ';
$string['view_interval_goal_headline_part2'] = ' bis zum ';
$string['view_interval_goal_headline_part3'] = '';

$string['intro_text'] = 'Hier kommt ein Introtext...';
$string['coruse_planing_intro'] = 'Erklärung wie man die Kursplanung startet...';

$string['link_interval_planing'] = 'Plane dein kommendes Lernintervall';
$string['interval_planing_link_intro_first'] = '...';

//course planing
$string['course_goal_setting_heading'] = 'Kurs planen';
$string['pre_course_knowledge_heading'] = 'Vorwissen einschätzen';

//goal transformation
$string['goal_transfer_headline'] = 'Lehrinhalte';
$string['goal_transfer_intro1'] = 'Bitte überführe Deine Ziele auf die Lehrinhalte. Gib dazu einfach an, wie wichtig die einzelnen Themengebiete zum Erreichen deiner Ziele sind.';
$string['goal_transfer_intro2'] = 'Schiebst du den Regler ganz nach rechts, bedeutet dies, dass der Themenbereich für dich sehr wichtig ist und du den Veranstaltungsstoff bis ins kleinste Detail beherrschen möchtest. Eine Reglereinstellung ganz links besagt, dass du dich mit diesem Bereich überhaupt nicht beschäftigen möchtest.';

//interval goal planing
$string['interval_goal_planing_headline_part1'] = 'Was möchtest du bis zum ';
$string['interval_goal_planing_headline_part2'] = ' erriechen?';
$string['interval_goal_text_start_part1'] = 'Ich möchte bis zum ';
$string['interval_goal_text_start_part2'] = ' ';
$string['goal'] = 'Ziel';
$string['goal_label_planed_time_investment'] = 'Geplantes Zeitinvestment';
$string['goal_label_comment'] = 'Anmerkungen';
$string['link_add_interval_goal'] = 'Weiteres Ziel setzen';
$string['link_remove_interval_goal'] = 'Ziel entfernen';

//goal edit
$string['goal_edit_headline'] = 'Ziel bearbeiten';
$string['invested_time'] = 'Investierte Zeit';
$string['status'] = 'Status';
$string['status_open'] = 'in Bearbeitung';
$string['status_done'] = 'fertig';
$string['status_cancelled'] = 'verworfen';

//general
$string['modulenameplural'] = 'PeerLA instances';
$string['modulename_help'] = 'Setting of learning goals, management of time investment, LA';
$string['newmodulefieldset'] = 'Custom example fieldset';
$string['newmodulename'] = 'PeerLA name';
$string['newmodulename_help'] = 'This is the content of the help tooltip associated with the newmodulename field. Markdown syntax is supported.';
$string['newmodule'] = 'PeerLA';
$string['pluginadministration'] = 'PeerLA administration';
$string['pluginname'] = 'PeerLA';

$string['minutes'] = 'Minuten';
$string['hours'] = 'Stunden';
$string['hour'] = 'Stunde';

//buttons
$string['btn_next'] = 'weiter';
$string['btn_show_subtopics'] = 'Unterthemen zeigen';
$string['btn_hide_subtopics'] = 'Unterthemen ausblenden';

//errors
$string['error_course_not_found'] = 'Der Kurs konnte nicht gefunden werden.';
$string['error_participant_not_found'] = 'Sie sind nicht für diesen Kurs angemeldet.';
$string['error_no_interval_goal'] = 'Sie müssen mindestens ein Ziel hinzufügen.';
$string['error_default_error_found_msg'] = 'Es ist ein Fehler aufgetreten. Bitt überprüfen Sie die markierten Felder.';
$string['error_goal_not_found'] = 'Das Ziel konnte nicht gefunden werden';

$string['internalError'] = 'Interner Fehler';
$string['error_db'] = $string['internalError'].': konnte nicht in Datenbank gespeichert werden.';