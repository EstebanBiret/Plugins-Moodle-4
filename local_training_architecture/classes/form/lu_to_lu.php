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
require_once(dirname(__FILE__) . '/../functions/lu_lu_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');
$PAGE->requires->js('/local/training_architecture/amd/src/lu_lu.js');

class lu_to_lu extends moodleform {

    function definition () {

        global $DB;

        $mform =$this->_form;

        $mform->addElement('header', 'luToLu', get_string('luToLu', 'local_training_architecture'));

        //TRAINING
        $trainings = $DB->get_records('local_training_architecture_training', [], 'fullname');

        $allTrainings = [''];
        foreach ($trainings as $training) {
            $allTrainings[$training->id] = $training->fullname . ' (' . $training->shortname . ')';
        }
        $options = ['multiple' => false];

        $mform->addElement('autocomplete','luToLuTrainingId', get_string('training', 'local_training_architecture'), $allTrainings, $options);
        $mform->addRule('luToLuTrainingId', get_string('required'), 'required');
        $mform->setType('luToLuTrainingId', PARAM_INT);

        //2 LEVELS
        $lus = $DB->get_records('local_training_architecture_lu', [], 'fullname');

        $allLus = [''];
        foreach ($lus as $lu) {
            $allLus[$lu->id] = $lu->fullname;
        }
        $options = ['multiple' => false];

        for ($i = 1; $i <= 2; $i++) {
            $mform->addElement('autocomplete', 'luToLuId' . $i, get_string('luLevel', 'local_training_architecture') . $i, $allLus, $options);
            $mform->addRule('luToLuId' . $i, get_string('required'), 'required');
            $mform->setType('luToLuId' . $i, PARAM_INT);
        }

        //LAST LEVEL (COURSE)
        $courses = get_courses();

        $allCourses = [''];
        foreach ($courses as $course) {
            $allCourses[$course->id] = $course->fullname;
        }
        $options = ['multiple' => true];

        $mform->addElement('autocomplete','luToLuCourseId', get_string('course', 'local_training_architecture'), $allCourses, $options);
        $mform->addRule('luToLuCourseId', get_string('required'), 'required');
        $mform->setType('luToLuCourseId', PARAM_INT);

        $tableId = 'lu-to-lu-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-lu-to-lu" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
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
        
        $luFunctions = new lu_lu_functions();
        $commonFunctions = new common_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['luToLuTrainingId'])) {
            $errors['luToLuTrainingId'] = get_string('required');
        }
        else {
            $numberOfLu = (int)$DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $data['luToLuTrainingId']]);
            $luIds = []; 
            
            for ($i = 1; $i <= $numberOfLu; $i++) {
                $fieldName = 'luToLuId' . $i;
                $luId = $data[$fieldName];

                if (empty($luId)) {
                    $errors[$fieldName] = get_string('required');
                }
                elseif (in_array($luId, $luIds)) {
                    $errors[$fieldName] = get_string('luDuplicate', 'local_training_architecture');
                } else {
                    $luIds[] = $luId;
                }
            }
    
            if (empty($data['luToLuCourseId']) || (count($data['luToLuCourseId']) === 1 && $data['luToLuCourseId'][0] === '0')) {
                $errors['luToLuCourseId'] = get_string('required');
            }

            // Check if luid1 is not used as luid2 in other relationship (for this training), and if luid2 is not used as luid1 in other relationship. 
            if($numberOfLu == '2') {
                if ($DB->record_exists_select(
                    'local_training_architecture_lu_to_lu', 
                    'trainingid = ? AND luid2 = ? AND isluid2course = ?', 
                    [$data['luToLuTrainingId'], $data['luToLuId1'], 'false']
                )) {
                    $errors['luToLuId1'] = get_string('lu1AlreadyAsLu2', 'local_training_architecture');
                }

                if ($DB->record_exists_select(
                    'local_training_architecture_lu_to_lu', 
                    'trainingid = ? AND luid1 = ? AND isluid2course = ?', 
                    [$data['luToLuTrainingId'], $data['luToLuId2'], 'false']
                )) {
                    $errors['luToLuId2'] = get_string('lu2AlreadyAsLu1', 'local_training_architecture');
                }
            }
        }

        // Check if LU and course are related to this training
        if(empty($errors)) {

            // All LU
            for ($i = 1; $i <= $numberOfLu; $i++) {
                $fieldName = 'luToLuId' . $i;
                $luId = $data[$fieldName];

                if (!$DB->record_exists_select(
                    'local_training_architecture_training_links', 
                    'trainingid = ? AND luid = ?', 
                    [$data['luToLuTrainingId'], $luId]
                )) {
                    $errors[$fieldName] = get_string('luNotRelated', 'local_training_architecture');
                }
            }
            // Course
            $coursesNotLinked = '';
            $coursesAlreadyOutsideArchitecture = '';

            foreach ($data['luToLuCourseId'] as $courseId) {
                if($courseId != 0) {
                    if (!$DB->record_exists_select(
                        'local_training_architecture_training_links', 
                        'trainingid = ? AND courseid = ?', 
                        [$data['luToLuTrainingId'], $courseId]
                    )) {
                        $coursesNotLinked.= ' ' . $commonFunctions->getCourseFullName($courseId);
                        $errors['luToLuCourseId'] = get_string('courseNotRelated', 'local_training_architecture') . ' : ' . $coursesNotLinked;
                    }
                }
            }

            foreach ($data['luToLuCourseId'] as $courseId) {
                if($courseId != 0) {
                    if ($DB->record_exists_select(
                        'local_training_architecture_courses_not_architecture', 
                        'trainingid = ? AND courseid = ?', 
                        [$data['luToLuTrainingId'], $courseId]
                    )) {
                        $coursesAlreadyOutsideArchitecture.= ' ' . $commonFunctions->getCourseFullName($courseId);
                        $errors['luToLuCourseId'] = get_string('courseAlreadyNotInArchitecture', 'local_training_architecture') . ' : ' . $coursesAlreadyOutsideArchitecture;
                    }
                }
            }
        }

        if(empty($errors)) {
            $data['numberOfLu'] = $numberOfLu;
            $luFunctions->createLink($data);
        }
        
        return $errors;

    }
    
}