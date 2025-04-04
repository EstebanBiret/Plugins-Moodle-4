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
 * Link cohort and training form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/cohort_training_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');

class cohort_to_training extends moodleform {

    function definition () {
        
        global $DB;

        $mform =$this->_form;

        $mform->addElement('header', 'cohortToTraining', get_string('cohortToTraining', 'local_training_architecture'));

        $trainings = $DB->get_records('local_training_architecture_training', [], 'fullname');

        $allTrainings = [''];
        foreach ($trainings as $training) {
            $allTrainings[$training->id] = $training->fullname . ' (' . $training->shortname . ')';
        }
        $options = ['multiple' => false];

        $mform->addElement('autocomplete','trainingId', get_string('training', 'local_training_architecture'), $allTrainings, $options);
        $mform->addRule('trainingId', get_string('required'), 'required');
        $mform->setType('trainingId', PARAM_INT);

        $cohorts = cohort_get_all_cohorts(0, -1);

        $allCohorts = [''];
        foreach ($cohorts['cohorts'] as $cohort) {
            $allCohorts[$cohort->id] = $cohort->name;
        }
        $options = ['multiple' => true];

        $mform->addElement('autocomplete','cohortId', get_string('cohort', 'local_training_architecture'), $allCohorts, $options);
        $mform->addRule('cohortId', get_string('required'), 'required');
        $mform->setType('cohortId', PARAM_INT);

        $tableId = 'cohort-to-training-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-cohort-to-training" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
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

        $cohortFunctions = new cohort_training_functions();
        $commonFunctions = new common_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['cohortId']) || (count($data['cohortId']) === 1 && $data['cohortId'][0] === '0')) {
            $errors['cohortId'] = get_string('required');
        }
        if (empty($data['trainingId'])) {
            $errors['trainingId'] = get_string('required');
        }

        // Handle duplicates values
        if(empty($errors)) {
            $cohortExists = '';

            foreach ($data['cohortId'] as $cohortId) {
                if($cohortId != 0) {
                    if ($DB->record_exists_select(
                        'local_training_architecture_cohort_to_training', 
                        'cohortid = ? AND trainingid = ?', 
                        [$cohortId, $data['trainingId']]
                    )) {
                        $cohortExists.= ' ' . $commonFunctions->getCohortName($cohortId);
                        $errors['cohortId'] = get_string('associationAlreadyExistsCohorts', 'local_training_architecture') . ' : ' . $cohortExists;
                        $errors['trainingId'] = get_string('associationAlreadyExists', 'local_training_architecture');
                    }
                }
            }

            if($errors) {
                return $errors;
            }

            $cohortFunctions->createLink($data);
        }

        return $errors;
    }
}
