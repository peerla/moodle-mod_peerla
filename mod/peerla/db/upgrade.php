<?php

function xmldb_peerla_upgrade($oldversion=0){
	
	global $DB;
    $dbman = $DB->get_manager();
	
	if ($oldversion < 2015050701){
		upgrade_mod_savepoint(true, 2015050701, 'peerla');
	}
	
	if ($oldversion < 2015090101) {

        // Define field learning_days_planed to be added to interval_goal.
        $table = new xmldb_table('interval_goal');
        $field = new xmldb_field('learning_days_planed', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'invested_time');
		$field2 = new xmldb_field('learning_days', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'learning_days_planed');;

        // Conditionally launch add field learning_days_planed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
		
        // Conditionally launch add field learning_days_planed.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Peerla savepoint reached.
        upgrade_mod_savepoint(true, 2015090101, 'peerla');
    }
	
	return true;
}