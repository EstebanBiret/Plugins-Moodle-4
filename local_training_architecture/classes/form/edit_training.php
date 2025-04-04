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
 * Edit training form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/training_functions.php');

class edit_training extends moodleform {

    function definition () {

        $mform =$this->_form;

        $mform->addElement('text','trainingFullName', get_string('fullName', 'local_training_architecture'),'maxlength="255" size="50"');
        $mform->addRule('trainingFullName', get_string('required'), 'required');
        $mform->setType('trainingFullName', PARAM_TEXT);

        $mform->addElement('text','trainingShortName', get_string('shortName', 'local_training_architecture'), 'maxlength="50" size="20"');
        $mform->addRule('trainingShortName', get_string('required'), 'required');
        $mform->setType('trainingShortName', PARAM_TEXT);

        $mform->addElement('text', 'trainingIDNumber', get_string('IDNumber', 'local_training_architecture'), 'maxlength="15" size="15"');
        $mform->addHelpButton('trainingIDNumber', 'IDNumber', 'local_training_architecture');
        $mform->setType('trainingIDNumber', PARAM_TEXT);

        $mform->addElement('editor','trainingDescription', get_string('description', 'local_training_architecture'));
        $mform->setType('trainingDescription', PARAM_RAW);

        $levels = ['' => get_string('chooseOption', 'local_training_architecture'), '1' => '1', '2' => '2'];
        $mform->addElement('select', 'trainingLevel', get_string('numberOfLevel', 'local_training_architecture'), $levels);
        $mform->addRule('trainingLevel', get_string('required'), 'required');

        $semesterChoice = [
            '' => get_string('chooseOption', 'local_training_architecture'),
            'yes' => get_string('yes', 'local_training_architecture'),
            'no' => get_string('no', 'local_training_architecture')
        ];
        
        $mform->addElement('select', 'trainingSemester', get_string('semesterChoice', 'local_training_architecture'), $semesterChoice);
        $mform->addRule('trainingSemester', get_string('required'), 'required');
        $mform->setType('trainingSemester', PARAM_ALPHA);

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

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

        $trainingFunctions = new training_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['trainingFullName'])) {
            $errors['trainingFullName'] = get_string('required');
        }
        if (empty($data['trainingShortName'])) {
            $errors['trainingShortName'] = get_string('required');
        }
        if (empty($data['trainingLevel'])) {
            $errors['trainingLevel'] = get_string('required');
        }
        if (empty($data['trainingSemester'])) {
            $errors['trainingSemester'] = get_string('required');
        }

        // Handle duplicates values
        if (!empty($data['trainingIDNumber'])) {
            if ($DB->record_exists_select('local_training_architecture_training', 'idnumber = ? AND id!= ?', [str_replace(' ', '', $data['trainingIDNumber']), $data['id']])) {
                $errors['trainingIDNumber'] = get_string('IDNumberAlreadyExists', 'local_training_architecture');
            }        
        }
        
        // Check if we can edit semester or level (check in training_links, level_names_to_training, lu_to_lu)
        if(empty($errors)) {

            $oldTraining = $DB->get_record('local_training_architecture_training', ['id' => $data['id']]);
            $semester = 0;

            if($data['trainingSemester'] == 'yes') {
                $semester = 1;
            }

            // Semester has changed
            if($oldTraining->issemester != $semester) {
                if ($DB->record_exists_select(
                    'local_training_architecture_training_links', 
                    'trainingid = ? ', 
                    [$oldTraining->id]
                )) {
                    $errors['trainingSemester'] = get_string('errorEditSemester', 'local_training_architecture');
                }
            }

            // Level has changed
            if ($oldTraining->granularitylevel != $data['trainingLevel']) {
                $referencesExist = $DB->record_exists_select(
                    'local_training_architecture_training_links', 
                    'trainingid = ?', 
                    [$oldTraining->id]) || 

                    $DB->record_exists_select(
                    'local_training_architecture_level_names_to_training', 
                    'trainingid = ?', 
                    [$oldTraining->id]) || 
                    
                    $DB->record_exists_select(
                    'local_training_architecture_lu_to_lu', 
                    'trainingid = ?', 
                    [$oldTraining->id]
                );

                if ($referencesExist) {
                    $errors['trainingLevel'] = get_string('errorEditLevel', 'local_training_architecture');
                }
            }
        }

        if(empty($errors)) {

            /*$sql = 'LOWER(fullname) = LOWER(?) AND id != ?';
            $params = [trim($data['trainingFullName']), $data['id']];
            if ($DB->record_exists_select('local_training_architecture_training', $sql, $params)) {
                $errors['trainingFullName'] = get_string('nameAlreadyExists', 'local_training_architecture');
            }*/

            $sql = 'LOWER(shortname) = LOWER(?) AND id != ?';
            $params = [trim($data['trainingShortName']), $data['id']];
            if ($DB->record_exists_select('local_training_architecture_training', $sql, $params)) {
                $errors['trainingShortName'] = get_string('shortNameAlreadyExists', 'local_training_architecture');
            }

            if ($errors) {
                return $errors;
            }

            $trainingFunctions->edit($data);
        }
        return $errors;
    }
}
