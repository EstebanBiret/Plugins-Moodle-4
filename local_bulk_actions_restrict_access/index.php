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
 * The bulk actions restrict access plugin main file
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Moodle and form files
require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');

require_once('classes/form/all_cohorts.php');
require_once('classes/form/trainings_of_cohort.php');
require_once('classes/form/lu1_of_training.php');
require_once('classes/form/lu2_of_lu1.php');
require_once('classes/form/courses_of_lu.php');
require_once('classes/form/profile_or_group.php');
require_once('classes/form/profile_field.php');
require_once('classes/form/profile_field_values.php');
require_once('classes/form/groups.php');
require_once('classes/form/date_time_selector.php');

// Get the courseId
//$id = required_param('id',PARAM_INT);
//$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

// Login, check capabilities and context
//require_login($course);
require_login();

//$context = context_course::instance($course->id);
$context = context_system::instance();
require_capability('local/bulk_actions_restrict_access:view', $context);
//$PAGE->set_context($context);

// Set url of the page
$url = new moodle_url('/local/bulk_actions_restrict_access/index.php?id=1'); //add the id parameter (course) to avoid errors when purging caches within the plugin
$PAGE->set_url($url);

// Add the title and heading of the page
$PAGE->set_title(get_string('pluginname', 'local_bulk_actions_restrict_access'));
$PAGE->set_heading(get_string('pluginname', 'local_bulk_actions_restrict_access'));
$PAGE->set_pagelayout('admin');

// Include css and js files
$PAGE->requires->css('/local/bulk_actions_restrict_access/styles.css'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/functions.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/all_cohorts.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/trainings_of_cohort.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/lu1_of_training.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/lu2_of_lu1.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/courses_of_lu.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/profile_or_group.js');
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/profile_field.js');
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/profile_field_values.js'); 
$PAGE->requires->js('/local/bulk_actions_restrict_access/amd/src/groups.js');

echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';

// Add the moodle header
echo $OUTPUT->header();

// Forms
$allCohortsForm = new all_cohorts();
$trainingsOfCohortForm = new trainings_of_cohort();
$lu1OfTrainingForm = new lu1_of_training();
$lu2OfLu1Form = new lu2_of_lu1(); // If training have 2 levels
$coursesOfLuForm = new courses_of_lu();
$profileOrGroupForm = new profile_or_group();
$profileFieldForm = new profile_field();
$profileFieldValuesForm = new profile_field_values();
$groupForm = new group();
$dateTimeSelectorForm = new date_time_selector();

// Display forms
echo '<div class="form-container">
        <div class="form-column">';
            $allCohortsForm->display();
echo   '</div>
        <div id="hidden-form">';
            $trainingsOfCohortForm->display();
echo   '</div>
        <div id="hidden-form-2">';
            $lu1OfTrainingForm->display();
echo   '</div> <div class="break"></div> 
        <div id="hidden-form-3">';
            $lu2OfLu1Form->display();
echo   '</div> <div class="break"></div> 
        <div id="hidden-form-4">';
            $coursesOfLuForm->display(); 
echo   '</div>
        <button id="redirect-to-course" class="btn btn-primary"> ' . get_string('course_link', 'local_bulk_actions_restrict_access') . ' </button>
     </div>';

// Display error message
echo '<div id="error"></div>';

// Separation line
echo '<hr class="line" id="first-line"></hr>';

// Filters 
echo '<div class="form-container">
        <div id="hidden-form-5">';
            $profileOrGroupForm->display();
echo   '</div>
        <div id="hidden-form-6">';
            $profileFieldForm->display();
echo   '</div>
        <div id="hidden-form-7">';
            $profileFieldValuesForm->display();
echo   '</div>
        <div id="hidden-form-8">';
            $groupForm->display();
echo   '</div></div>';

// Display error filters message
echo '<div id="error-filters"></div>';

// Separation line
echo '<hr class="line" id="second-line"></hr>';

// Display number of sections & activities
echo '<div id="sections-activities-number"></div>';

// Number of dates found with the filters
echo '<div id="dates-counter-button-container">
        <div id="dates-filters"></div>
        <span id="checkbox-counter">' . get_string('no_selected_activities', 'local_bulk_actions_restrict_access') . '</span>
        <button id="uncheck-all-button" class="btn btn-danger" >' . get_string('uncheck_everything', 'local_bulk_actions_restrict_access') . '</button>
      </div>';

// Container for activities and form
echo '<div id="activities-form-container">';

// All course's activities
echo '<div id="activities"></div>';

// Activities form
echo '<div id="activities-form">
        <div id="form-label"></div>
        <div id="hidden-form-date">';
        $dateTimeSelectorForm->display();
echo '</div></div></div>';

// Success or error
echo '<div id="notification-container"></div>';

// Blocks and footer
echo $OUTPUT->blocks('side-post');
echo $OUTPUT->footer();