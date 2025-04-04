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
 * Custom profile field form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

defined('MOODLE_INTERNAL') || die;

//require moodle files
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/config.php');

class profile_field extends moodleform {

    public function definition() {
        global $DB;
        // A reference to the form is stored in $this->form.
        $mform = $this->_form; // Don't forget the underscore!

        // All custom profile fields
        $fields = $DB->get_records('user_info_field', [], 'name');        

	    // For each profile field, add an option in the select, containing the profile field ID and displaying its name 
        $allFields[-1] = get_string('choose_select', 'local_bulk_actions_restrict_access');

        foreach ($fields as $field) {

            // Uncomment if you want to see all the profile fields, but initally, just the 4 profile fields below are implemented.
            // $allFields[$field->id] = $field->name; 

            // Specific to IFRASS, only keep filieremodalite, promotion, filiere and GAF
            if($field->id == 1 || $field->id == 3 || $field->id == 9 || $field->id == 10) {
                $allFields[$field->id] = $field->name;
            }
        }

        // Add the field to the form
        $mform->addElement('select', 'select_profile_field', get_string('profile_field', 'local_bulk_actions_restrict_access'), $allFields);
        $mform->setType('select_profile_field', PARAM_INT);
    }
    
}