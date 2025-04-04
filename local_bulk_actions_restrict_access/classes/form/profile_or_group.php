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
 * Select form to choose if we want to use restrictions on profileId or group
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

class profile_or_group extends moodleform {

    public function definition() {
        global $DB;
        // A reference to the form is stored in $this->form.
        $mform = $this->_form; // Don't forget the underscore!

        // Here, we juste add those two types, it can evolve
        $types = [
            '' => get_string('choose_select', 'local_bulk_actions_restrict_access'),
            'profile_field' => get_string('profile_field', 'local_bulk_actions_restrict_access'),
            'group' => get_string('group', 'local_bulk_actions_restrict_access')
        ];

        // Add the field to the form
        $mform->addElement('select', 'filter_by', get_string('filter_by', 'local_bulk_actions_restrict_access'), $types);
        $mform->setType('filter_by', PARAM_INT);
    }
    
}