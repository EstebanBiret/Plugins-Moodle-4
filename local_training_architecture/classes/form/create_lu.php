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
 * Create LU form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/lu_functions.php');

class create_lu extends moodleform {

    function definition () {

        $mform =$this->_form;

        $mform->addElement('header', 'createLu', get_string('createLuTitle', 'local_training_architecture'));

        $mform->addElement('text','luFullName', get_string('fullName', 'local_training_architecture'),'maxlength="255" size="50"');
        $mform->addRule('luFullName', get_string('required'), 'required');
        $mform->setType('luFullName', PARAM_TEXT);

        $mform->addElement('text','luShortName', get_string('shortName', 'local_training_architecture'), 'maxlength="50" size="20"');
        $mform->addRule('luShortName', get_string('required'), 'required');
        $mform->setType('luShortName', PARAM_TEXT);

        $mform->addElement('text', 'luIDNumber', get_string('IDNumber', 'local_training_architecture'), 'maxlength="15" size="15"');
        $mform->addHelpButton('luIDNumber', 'IDNumber', 'local_training_architecture');
        $mform->setType('luIDNumber', PARAM_TEXT);

        $mform->addElement('editor','luDescription', get_string('description', 'local_training_architecture'));
        $mform->setType('luDescription', PARAM_RAW);

        $tableId = 'lu-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-lu" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
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

        $luFunctions = new lu_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['luFullName'])) {
            $errors['luFullName'] = get_string('required');
        }
        if (empty($data['luShortName'])) {
            $errors['luShortName'] = get_string('required');
        }

        // Handle duplicates values
        if (!empty($data['luIDNumber'])) {
            if ($DB->record_exists_select('local_training_architecture_lu', 'idnumber = ?', [str_replace(' ', '', $data['luIDNumber'])])) {
                $errors['luIDNumber'] = get_string('IDNumberAlreadyExists', 'local_training_architecture');
            }        
        }

        if(empty($errors)) {

            /*if ($DB->record_exists_select('local_training_architecture_lu', 'LOWER(fullname) = LOWER(?)', [trim($data['luFullName'])])) {
                $errors['luFullName'] = get_string('nameAlreadyExists', 'local_training_architecture');
            }*/

            if ($DB->record_exists_select('local_training_architecture_lu', 'LOWER(shortname) = LOWER(?)', [trim($data['luShortName'])])) {
                $errors['luShortName'] = get_string('shortNameAlreadyExists', 'local_training_architecture');
            }

            if($errors) {
                return $errors;
            }

            $luFunctions->create($data);
        }
        return $errors;
    }

}
