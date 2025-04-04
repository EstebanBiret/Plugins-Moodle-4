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
 * All cohorts form class
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

class all_cohorts extends moodleform {

    public function definition() {
        // A reference to the form is stored in $this->form.
        $mform = $this->_form; // Don't forget the underscore!

        //get all cohorts
        $cohorts = cohort_get_all_cohorts(0, -1);
        $allCohorts = [''];

	    //for each cohort, add an option in the select, containing the cohort ID and displaying its name 
        foreach ($cohorts['cohorts'] as $cohort) {
            $allCohorts[$cohort->id] = $cohort->name;
        }
        $options = ['multiple' => false];

        //add the field to the form
        $mform->addElement('autocomplete', 'cohort', get_string('cohort', 'local_bulk_actions_restrict_access'), $allCohorts, $options);
        $mform->setType('cohort', PARAM_INT);
    }
    
}