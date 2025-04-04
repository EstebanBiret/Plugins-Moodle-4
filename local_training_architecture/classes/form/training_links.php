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
 * Links of a training
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/config.php');
require_once(dirname(__FILE__) . '/../functions/training_links_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');
$PAGE->requires->js('/local/training_architecture/amd/src/training_links.js');

class training_links extends moodleform {

    function definition () {

        global $DB;

        $mform =$this->_form;

        $mform->addElement('header', 'trainingLinks', get_string('trainingLinks', 'local_training_architecture'));

        //TRAINING
        $trainings = $DB->get_records('local_training_architecture_training', [], 'fullname');

        $allTrainings = [''];
        foreach ($trainings as $training) {
            $allTrainings[$training->id] = $training->fullname . ' (' . $training->shortname . ')';
        }
        $options = ['multiple' => false];

        $mform->addElement('autocomplete','trainingId2', get_string('training', 'local_training_architecture'), $allTrainings, $options);
        $mform->addRule('trainingId2', get_string('required'), 'required');
        $mform->setType('trainingId2', PARAM_INT);

        //LEVEL
        $levels = ['' => get_string('chooseOption', 'local_training_architecture'), '1' => '1', '2' => '2', '3' => '3'];

        $mform->addElement('select', 'level', get_string('level', 'local_training_architecture'), $levels);
        $mform->addHelpButton('level', 'granularityLevel', 'local_training_architecture');
        $mform->addRule('level', get_string('required'), 'required');

        // LU
        $lus = $DB->get_records('local_training_architecture_lu', [], 'fullname');

        $allLus = [''];
        foreach ($lus as $lu) {
            $allLus[$lu->id] = $lu->fullname;
        }
        $options = ['multiple' => true];

        $mform->addElement('autocomplete','luId', get_string('lu', 'local_training_architecture'), $allLus, $options);
        $mform->addRule('luId', get_string('required'), 'required');
        $mform->setType('luId', PARAM_INT);

        //COURSE
        $courses = get_courses();

        $allCourses = [''];
        foreach ($courses as $course) {
            $allCourses[$course->id] = $course->fullname;
        }
        $options = ['multiple' => true];

        $mform->addElement('autocomplete','courseId', get_string('course', 'local_training_architecture'), $allCourses, $options);
        $mform->addRule('courseId', get_string('required'), 'required');
        $mform->setType('courseId', PARAM_INT);

        //SEMESTER
        $allSemesters = ['' => get_string('selectSemester', 'local_training_architecture')];
        $semester = get_string('semester', 'local_training_architecture');
        $allSemesters[1] =  $semester . ' 1';
        $allSemesters[2] =  $semester . ' 2';
        $allSemesters[3] =  $semester . ' 3';
        $allSemesters[4] =  $semester . ' 4';
        $allSemesters[5] =  $semester . ' 5';
        $allSemesters[6] =  $semester . ' 6';

        $mform->addElement('select','semester', get_string('semester', 'local_training_architecture'), $allSemesters);
        $mform->addRule('semester', get_string('required'), 'required');
        $mform->setType('semester', PARAM_INT);

        $tableId = 'training-links-table-container';

        $mform->addElement('html', '<div class="custom-collapsible">
            <span class="show-hide-table-title">' . get_string('collapse', 'local_training_architecture') . '</span>
            <input type="text" class="search-input-training-architecture" id="search-input-training_links" data-table-id="' . $tableId . '" placeholder="' . get_string('search') . '">
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

        $training_links_functions = new training_links_functions();
        $common_functions = new common_functions();

        $errors = parent::validation($data, $files);

        // Empty required fields
        if (empty($data['level'])) {
            $errors['level'] = get_string('required');
        }

        if (empty($data['trainingId2'])) {
            $errors['trainingId2'] = get_string('required');
        }
        else {
            // 3 hidden fields, see if empty (and if they need to be filled)
            $isSemester = (int)$DB->get_field('local_training_architecture_training', 'issemester', ['id' => $data['trainingId2']]);
            $granularityLevel = (int)$DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $data['trainingId2']]);

            if (empty($data['semester']) && ($isSemester === 1) && ($granularityLevel + 1 === (int)$data['level']) && !empty($data['level'])){
                $errors['semester'] = get_string('required');
            }

            if ((empty($data['courseId']) || (count($data['courseId']) === 1 && $data['courseId'][0] === '0')) && ($granularityLevel + 1 === (int)$data['level']) && !empty($data['level'])){
                $errors['courseId'] = get_string('required');
            }

            if ((empty($data['luId']) || (count($data['luId']) === 1 && $data['luId'][0] === '0')) && ($granularityLevel + 1 !== (int)$data['level']) && !empty($data['level']) ) {
                $errors['luId'] = get_string('required');
            }

            if((int)$data['level'] > $granularityLevel + 1) {
                $errors['level'] = get_string('levelTooHigh', 'local_training_architecture');   
            }

        }

        // Check if record already exists, on the x displayed fields
        if(empty($errors)) {

            $course = 'false';
            $semester = 'false';

            $isSemester = (int)$DB->get_field('local_training_architecture_training', 'issemester', ['id' => $data['trainingId2']]);
            $granularityLevel = (int)$DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $data['trainingId2']]);

            // LU
            if($granularityLevel + 1 !== (int)$data['level']) {

                $luExists = '';

                foreach ($data['luId'] as $luId) {

                    if ($DB->record_exists_select(
                        'local_training_architecture_training_links', 
                        'trainingid = ? AND luid = ?', 
                        [$data['trainingId2'], $luId]
                    )) {
                        $luExists.= ' ' . $common_functions->getLuFullName($luId);
                        $errors['luId'] = get_string('associationAlreadyExists', 'local_training_architecture') . ' : ' . $luExists;
                    }
                }
            }

            // Course
            else if($granularityLevel + 1 === (int)$data['level']) {
                $course = 'true';
                $courseExists = '';

                // Course + semester
                if($isSemester === 1) {
                    $semester = 'true';

                    foreach ($data['courseId'] as $courseId) {
                        if ($DB->record_exists_select(
                            'local_training_architecture_training_links', 
                            'trainingid = ? AND level = ? AND courseid = ? AND semester = ?', 
                            [$data['trainingId2'], $data['level'], $courseId, $data['semester']]
                        )) {
                            $errors['trainingId2'] = get_string('associationAlreadyExists', 'local_training_architecture');
                            $errors['level'] = get_string('associationAlreadyExists', 'local_training_architecture');
                            $errors['semester'] = get_string('associationAlreadyExists', 'local_training_architecture');

                            $courseExists.= ' ' . $common_functions->getCourseFullName($courseId);
                            $errors['courseId'] = get_string('associationAlreadyExists', 'local_training_architecture') . ' : ' . $courseExists;
                        }
                    }
                }

                // Just course
                else {
                    foreach ($data['courseId'] as $courseId) {
                        if ($DB->record_exists_select(
                            'local_training_architecture_training_links', 
                            'trainingid = ? AND level = ? AND courseid = ?', 
                            [$data['trainingId2'], $data['level'], $courseId]
                        )) {
                            $courseExists.= ' ' . $common_functions->getCourseFullName($courseId);
                            $errors['courseId'] = get_string('associationAlreadyExists', 'local_training_architecture') . ' : ' . $courseExists;
                        }
                    }
                }
            }

            if($errors) {
                return $errors;
            }
        
            $data['isCourse'] = $course;
            $data['isSemester'] = $semester;            
            $training_links_functions->createLink($data);
        }
        return $errors;
    }

}
