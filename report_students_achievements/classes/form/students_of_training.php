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
 * Students of training form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_students_achievements
 */

defined('MOODLE_INTERNAL') || die;

// Moodle files
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/config.php');

// The last form. Hidden by default, containing all the students of a selected training
class students_of_training_form extends moodleform {

    public function definition() {
        // A reference to the form is stored in $this->form.
        $mform = $this->_form; // Don't forget the underscore!

	    // It's in the javascript files that we will add the options of the form, according to the actual training
        $mform->setAttributes(['id' => 'students-training']);

        // Add the field to the form
        $mform->addElement('select', 'select_students_training', get_string('students_of_this_training', 'report_students_achievements'), '');
        $mform->setType('select_students_training', PARAM_INT);
    }
}
