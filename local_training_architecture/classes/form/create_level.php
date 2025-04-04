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
 * Create level form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/level_functions.php');

class create_level extends moodleform {

    function definition () {
        
        $mform =$this->_form;

        $mform->addElement('header', 'createLevel', get_string('createLevelTitle', 'local_training_architecture'));

        $mform->addElement('text','levelFullName', get_string('fullName', 'local_training_architecture'),'maxlength="255" size="50"');
        $mform->addRule('levelFullName', get_string('required'), 'required');
        $mform->addHelpButton('levelFullName', 'fullName', 'local_training_architecture');
        $mform->setType('levelFullName', PARAM_TEXT);

        $mform->addElement('text','levelShortName', get_string('shortName', 'local_training_architecture'), 'maxlength="50" size="20"');
        $mform->addRule('levelShortName', get_string('required'), 'required');
        $mform->addHelpButton('levelShortName', 'shortName', 'local_training_architecture');
        $mform->setType('levelShortName', PARAM_TEXT);

        $mform->addElement('textarea', 'levelDescription', get_string('description', 'local_training_architecture'), 'maxlength="255"');
        $mform->setType('levelDescription', PARAM_TEXT);

        $tableId = 'levels-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-level" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
            <div id="'. $tableId .'" class="table-container-training-architecture" style="display: block;"></div>
        </div>');

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array $errors An array of errors
     */
    function validation($data, $files) {

        global $DB;

        $levelFunctions = new level_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['levelFullName'])) {
            $errors['levelFullName'] = get_string('required');
        }
        if (empty($data['levelShortName'])) {
            $errors['levelShortName'] = get_string('required');
        }

        // Handle duplicates values
        if(empty($errors)) {

            if ($DB->record_exists_select('local_training_architecture_level_names', 'LOWER(fullname) = LOWER(?)', [trim($data['levelFullName'])])) {
                $errors['levelFullName'] = get_string('nameAlreadyExists', 'local_training_architecture');
            }

            if ($DB->record_exists_select('local_training_architecture_level_names', 'LOWER(shortname) = LOWER(?)', [trim($data['levelShortName'])])) {
                $errors['levelShortName'] = get_string('shortNameAlreadyExists', 'local_training_architecture');
            }

            if($errors) {
                return $errors;
            }

            $levelFunctions->create($data);
        }
        return $errors;
    }

}