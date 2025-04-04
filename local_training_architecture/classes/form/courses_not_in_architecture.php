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
 * Courses not in architecture form class
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/courses_not_in_architecture_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');

class courses_not_in_architecture extends moodleform {

    function definition () {

        global $DB;

        $mform =$this->_form;

        $mform->addElement('header', 'coursesNotInArchitectureTitle', get_string('coursesNotInArchitectureTitle', 'local_training_architecture'));

        // Trainings
        $trainings = $DB->get_records('local_training_architecture_training', [], 'fullname');

        $allTrainings = [''];
        foreach ($trainings as $training) {
            $allTrainings[$training->id] = $training->fullname . ' (' . $training->shortname . ')';
        }
        $options = ['multiple' => false];

        $mform->addElement('autocomplete','coursesNotInArchitectureTrainingId', get_string('training', 'local_training_architecture'), $allTrainings, $options);
        $mform->addRule('coursesNotInArchitectureTrainingId', get_string('required'), 'required');
        $mform->setType('coursesNotInArchitectureTrainingId', PARAM_INT);

        // Courses
        $courses = get_courses();

        $allCourses = [''];
        foreach ($courses as $course) {
            $allCourses[$course->id] = $course->fullname;
        }
        $options = ['multiple' => true];

        $mform->addElement('autocomplete','coursesNotInArchitectureCourseId', get_string('course', 'local_training_architecture'), $allCourses, $options);
        $mform->addRule('coursesNotInArchitectureCourseId', get_string('required'), 'required');
        $mform->setType('coursesNotInArchitectureCourseId', PARAM_INT);

        $tableId = 'courses-not-in-architecture-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-courses" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
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
        
        $coursesNotInArchitectureFunctions = new courses_not_in_architecture_functions();
        $commonFunctions = new common_functions();

        $errors = parent::validation($data, $files);
        
        // Empty required fields
        if (empty($data['coursesNotInArchitectureTrainingId'])) {
            $errors['coursesNotInArchitectureTrainingId'] = get_string('required');
        }

        if (empty($data['coursesNotInArchitectureCourseId']) || (count($data['coursesNotInArchitectureCourseId']) === 1 && $data['coursesNotInArchitectureCourseId'][0] === '0')) {
            $errors['coursesNotInArchitectureCourseId'] = get_string('required');
        }

        // Check if course is not already in architecture
        if(empty($errors)) {

            // Courses
            $coursesAlreadyInArchitecture = '';

            foreach ($data['coursesNotInArchitectureCourseId'] as $courseId) {
                if($courseId != 0) {
                    if ($DB->record_exists_select(
                        'local_training_architecture_lu_to_lu', 
                        'trainingid = ? AND luid2 = ? AND isluid2course = ?', 
                        [$data['coursesNotInArchitectureTrainingId'], $courseId, 'true']
                    )) {
                        $coursesAlreadyInArchitecture.= ' ' . $commonFunctions->getCourseFullName($courseId);
                        $errors['coursesNotInArchitectureCourseId'] = get_string('courseAlreadyInArchitecture', 'local_training_architecture') . ' : ' . $coursesAlreadyInArchitecture;
                    }
                }
            }
        }

        if(empty($errors)) {
            $coursesNotInArchitectureFunctions->createLink($data);
        }

        return $errors;
    }
    
}
