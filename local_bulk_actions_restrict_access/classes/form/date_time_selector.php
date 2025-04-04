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
 * Select dates form
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

defined('MOODLE_INTERNAL') || die;

// Require moodle files
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/config.php');

class date_time_selector extends moodleform {

    public function definition() {
        // A reference to the form is stored in $this->form.
        $mform = $this->_form; // Don't forget the underscore!

        //  The step is for minutes, because if we select a minute that is not a multiple of 5, it will not appear on the activity access restriction configuration form, from the moodle core.
        $mform->addElement('date_time_selector', 'assesstimestart', get_string('start_date', 'local_bulk_actions_restrict_access'),
        ['step' => 5]);

        $mform->addElement('date_time_selector', 'assesstimeend', get_string('end_date', 'local_bulk_actions_restrict_access'), 
        ['step' => 5]);

        $mform->setAttributes(['id' => 'date-time-selector-form']);

        $this->add_action_buttons();
    }
    
}