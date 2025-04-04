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
 * Create training form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/training_level_functions.php');
$PAGE->requires->js('/local/training_architecture/amd/src/training_level.js');

class training_level extends moodleform {

    function definition () {

        global $DB;

        $mform =$this->_form;

        $mform->addElement('header', 'trainingToLevel', get_string('trainingToLevel', 'local_training_architecture'));

        $trainings = $DB->get_records('local_training_architecture_training', [], 'fullname');

        $allTrainings = [''];
        foreach ($trainings as $training) {
            $allTrainings[$training->id] = $training->fullname . ' (' . $training->shortname . ')';
        }
        $options = ['multiple' => false];

        $mform->addElement('autocomplete','trainingId3', get_string('training', 'local_training_architecture'), $allTrainings, $options);
        $mform->addRule('trainingId3', get_string('required'), 'required');
        $mform->setType('trainingId3', PARAM_INT);

        $level_names = $DB->get_records('local_training_architecture_level_names', [], 'fullname');

        $allLevels = [''];
        foreach ($level_names as $level_name) {
            $allLevels[$level_name->id] = $level_name->fullname;
        }
        $options = ['multiple' => false];

        for ($i = 1; $i <= 2; $i++) {
            $mform->addElement('autocomplete', 'trainingToLevel' . $i, get_string('trainingLevel', 'local_training_architecture') . $i, $allLevels, $options);
            $mform->addRule('trainingToLevel' . $i, get_string('required'), 'required');
            $mform->addHelpButton('trainingToLevel' . $i, 'trainingLevel', 'local_training_architecture');
            $mform->setType('trainingToLevel' . $i, PARAM_INT);
        }

        $tableId = 'training-levels-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-training-to-level" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
            <div id="'. $tableId .'" class="table-container-training-architecture" style="display: block;"></div>
        </div>');
        
        $mform->addElement('html', '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>');

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

        $trainingLevelFunctions = new training_level_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['trainingId3'])) {
            $errors['trainingId3'] = get_string('required');
            return $errors;
        }

        // Handle duplicates values
        else { 
            $number_of_levels = $DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $data['trainingId3']]);
            $levelValues = [];
            
            for ($i = 1; $i <= $number_of_levels; $i++) {
                $str = 'trainingToLevel' . $i;

                if (empty($data[$str])) {
                    $errors[$str] = get_string('required');
                }
                else {
                    if (in_array($data[$str], $levelValues)) {
                        $errors[$str] = get_string('selectDifferentLevel', 'local_training_architecture');
                    } else {
                        $levelValues[] = $data[$str];
                    }
                }
            }

            if($errors) {
                return $errors;
            }
        }

        if(empty($errors)) {

            if ($DB->record_exists_select(
                'local_training_architecture_level_names_to_training', 
                'trainingid = ?', [$data['trainingId3']])) {

                $errors['trainingId3'] = get_string('trainingLevelAlreadyExists', 'local_training_architecture');
            }
            if($errors) {
                return $errors;
            }

            $data['numberOfLevels'] = $number_of_levels;
            $trainingLevelFunctions->createLink($data);
        }
        return $errors;
    }
    
}
