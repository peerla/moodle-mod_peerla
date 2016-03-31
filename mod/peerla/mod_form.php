<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
 
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/peerla/lib.php');
 
class mod_peerla_mod_form extends moodleform_mod {
	
	protected function definition() {
		global $CFG, $DB, $OUTPUT;
		
		$mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('newmodulename', 'peerla'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'newmodulename', 'peerla');

        // Adding the standard "intro" and "introformat" fields.
        $this->add_intro_editor();
		
		/*
		 * Add the course topic form here?
		 */
		/*
		$this->_form->addElement('text', 'name', 'test', array('size'=>'64'));
		$this->_form->setType('name', PARAM_TEXT);
		$this->_form->addRule('name', null, 'required', null, 'client');
		*/
		$this->standard_coursemodule_elements();
		
		$this->add_action_buttons();
	}

}