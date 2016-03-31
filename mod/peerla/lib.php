<?php

//form variables when creating a new activity instance
function peerla_add_instance(stdClass $instaceData, mod_peerla_mod_form $mform = null){
	global $DB;

    $instaceData->timecreated = time();

    $instaceData->id = $DB->insert_record('peerla', $instaceData);

    return $instaceData->id;
}

//form variables when editing an existing activity instance
function peerla_update_instance($modFormVariables){
	//success or error?
	return true;
}

//deletion of an activity instance
function peerla_delete_instance($id){
	
	return true;
}