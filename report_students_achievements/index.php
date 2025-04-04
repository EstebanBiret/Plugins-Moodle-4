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
 * The Students Achievements report
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_student_achievements
 */

// Moodle and form files
require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');

require_once('classes/form/all_cohorts.php');
require_once('classes/form/trainings_of_cohort.php');
require_once('classes/form/students_of_training.php');

// Get the courseId
$id = required_param('id',PARAM_INT);
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

// Login, check capabilities and context
require_login($course);
$context = context_course::instance($course->id);
require_capability('report/students_achievements:view',$context);
$PAGE->set_context($context);

// Include css and js files
$PAGE->requires->css('/report/students_achievements/styles.css'); 
$PAGE->requires->js('/report/students_achievements/amd/src/all_cohorts.js'); 
$PAGE->requires->js('/report/students_achievements/amd/src/functions.js'); 
$PAGE->requires->js('/report/students_achievements/amd/src/students_of_training.js'); 
$PAGE->requires->js('/report/students_achievements/amd/src/trainings_of_cohort.js'); 
$PAGE->requires->js('/report/students_achievements/amd/src/view_details.js'); 
echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';

// Set url of the page
$url = new moodle_url('/report/students_achievements/index.php?id=1'); //add the id parameter (course) to avoid errors when purging caches within the plugin
$PAGE->set_url($url);

// Add the moodle header
echo $OUTPUT->header();

// Add the title and heading of the page
$PAGE->set_title(get_string('title', 'report_students_achievements'));
echo '<div id="plugin-title">' . $OUTPUT->heading(get_string('heading', 'report_students_achievements')) . '</div>';

// Forms
$cohortsForm = new all_cohorts_form();
$trainingForm = new trainings_of_cohort_form();
$studentsForm = new students_of_training_form();

// Display forms
echo '<div class="form-container">
        <div class="form-column">';
            $cohortsForm->display();
echo '</div>
        <div id="hidden-form">';
            $trainingForm->display();
echo '</div>
        <div id="hidden-form-2">';
            $studentsForm->display();
echo '</div></div>';

// Display not trainings found message
echo '<div id="no-trainings"></div>';

// Separation line
echo '<hr class="line"></hr>';

// Firstname and lastname of the selected student (or training name)
echo '<div id="infos-container"></div>';

// Display the number of students found
echo '<div id="number-students"></div>';

// HTML table that contains the student for the selected cohort
$table = new html_table();
$table->id = 'result-table';
echo html_writer::table($table);

// All the data of the student (drop down sections & tables)
echo '<div id="completion-student"></div>';

// Export forms
echo '<div class = "export-student">' . 
$OUTPUT->download_dataformat_selector('', '../../report/students_achievements/export/student.php', 'export') .
'</div>';

echo '<div class = "export-cohort">' . 
$OUTPUT->download_dataformat_selector('', '../../report/students_achievements/export/cohort.php', 'export') .
'</div>';

// Blocks and footer
echo $OUTPUT->blocks('side-post');
echo $OUTPUT->footer();