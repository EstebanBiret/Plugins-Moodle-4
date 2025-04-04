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
 * Create architecture main file.
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

require_once(dirname(__FILE__) . '/../../config.php');

require_once(dirname(__FILE__) . '/classes/form/create_level.php');
require_once(dirname(__FILE__) . '/classes/form/create_training.php');
require_once(dirname(__FILE__) . '/classes/form/training_to_level.php');
require_once(dirname(__FILE__) . '/classes/form/cohort_to_training.php');
require_once(dirname(__FILE__) . '/classes/form/courses_not_in_architecture.php');
require_once(dirname(__FILE__) . '/classes/form/create_lu.php');
require_once(dirname(__FILE__) . '/classes/form/training_links.php');
require_once(dirname(__FILE__) . '/classes/form/lu_to_lu.php');

require_once(dirname(__FILE__) . '/classes/functions/common_functions.php');

// This section handles user authentication, page setup, and permission checks.
require_login();
$context = context_system::instance();
require_capability('local/training_architecture:manage',$context);
$PAGE->set_context($context);
$PAGE->set_url('/local/training_architecture/index.php');
$PAGE->set_title(get_string('title', 'local_training_architecture'));
$PAGE->requires->css('/local/training_architecture/styles.css');
$PAGE->requires->js('/local/training_architecture/amd/src/multiple_delete.js');
$PAGE->requires->js('/local/training_architecture/amd/src/functions.js');
$PAGE->set_heading(get_string('heading', 'local_training_architecture'));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();

// Initialize common objects and variables.
$returnurl = new moodle_url('/local/training_architecture/index.php');
$commonFunctions = new common_functions();

// Display anchor and links to different forms.
echo html_writer::start_tag('h3');
echo get_string('allForms', 'local_training_architecture');
echo html_writer::end_tag('h3');

echo html_writer::start_tag('ul');

$links = [
    '#id_createLevelcontainer' => get_string('createLevelTitle', 'local_training_architecture'),
    '#id_createTrainingcontainer' => get_string('createTraining', 'local_training_architecture'),
    '#id_createLucontainer' => get_string('createLuTitle', 'local_training_architecture'),
    '#id_trainingToLevelcontainer' => get_string('trainingToLevel', 'local_training_architecture'),
    '#id_cohortToTrainingcontainer' => get_string('cohortToTraining', 'local_training_architecture'),
    '#id_coursesNotInArchitectureTitlecontainer' => get_string('coursesNotInArchitectureTitle', 'local_training_architecture'),
    '#id_trainingLinkscontainer' => get_string('trainingLinks', 'local_training_architecture'),
    '#id_luToLucontainer' => get_string('luToLu', 'local_training_architecture')
];

foreach ($links as $href => $text) {
    echo html_writer::start_tag('li');
    echo '<span><a href="' . $href . '">' . $text . '</a></span>';
    echo html_writer::end_tag('li');
}

echo html_writer::end_tag('ul');

//-----------------------------------

// Create level section

$create_level_form = new create_level();

// Check if the create level form is submitted.
if ($create_level_form->is_submitted()) {
    $data = $create_level_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the create level form is cancelled.
if($create_level_form->is_cancelled()) {
    redirect('index.php');
}

$create_level_form->display();

// Generate HTML table for displaying levels.
$levelsData = [];
if ($levels = $DB->get_records('local_training_architecture_level_names', [], 'fullname')) {

    foreach ($levels as $level) {
        $line = [];
        $line[] = $level->fullname;
        $line[] = $level->shortname;
        //$line[] = $level->description;

        $buttons = '';
        
        // Edit button URL.
        $editUrl = new moodle_url('classes/edit_delete/level.php', ['id' => $level->id]);
        $editButton = html_writer::link($editUrl, $OUTPUT->pix_icon('t/edit', get_string('edit')));
        $buttons .= $editButton;
        
        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/level.php', ['id' => $level->id, 'delete' => 1]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;
        
        $levelsData[] = $line;
    }
}

// Create HTML table for displaying levels data.
$level_table = new html_table();
$level_table->head = [
    get_string('fullName', 'local_training_architecture'),
    get_string('shortName', 'local_training_architecture'),
    //get_string('description', 'local_training_architecture'),
    get_string('actions', 'local_training_architecture')
];

$level_table->data = $levelsData;

// Output JavaScript to replace content with levels table.
echo '<script>document.getElementById("levels-table-container").innerHTML = ' . json_encode(html_writer::table($level_table)) . ';</script>';

//--------------

// Create Training Section

$create_training_form = new create_training();

// Check if the create training form is submitted.
if ($create_training_form->is_submitted()) {
    $data = $create_training_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the create training form is cancelled.
if($create_training_form->is_cancelled()) {
    redirect('index.php');
}

$create_training_form->display();

// Generate HTML table for displaying trainings.
$trainingData = [];
if ($trainings = $DB->get_records('local_training_architecture_training', [], 'fullname')) {
    foreach ($trainings as $training) {
        $line = [];
        $line[] = $training->fullname;
        $line[] = $training->shortname;
        $line[] = $training->idnumber;
        //$line[] = $training->description;
        $line[] = $training->granularitylevel;
        $line[] = ($training->issemester == 0) ? get_string('no', 'local_training_architecture') : get_string('yes', 'local_training_architecture');
        $buttons = '';
        
        // Sort button URL.
        $sortUrl = new moodle_url('sort.php', ['trainingid' => $training->id]);
        $sortButton = html_writer::link($sortUrl, $OUTPUT->pix_icon('t/sort', get_string('sort')));
        $buttons .= $sortButton;

        // Edit button URL.
        $editUrl = new moodle_url('classes/edit_delete/training.php', ['id' => $training->id]);
        $editButton = html_writer::link($editUrl, $OUTPUT->pix_icon('t/edit', get_string('edit')));
        $buttons .= $editButton;
        
        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/training.php', ['id' => $training->id, 'delete' => 1]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;
        $trainingData[] = $line;
    }
}

// Create HTML table for displaying training data.
$training_table = new html_table();
$training_table->head = [
    get_string('fullName', 'local_training_architecture'),
    get_string('shortName', 'local_training_architecture'),
    get_string('IDNumber', 'local_training_architecture'),
    //get_string('description', 'local_training_architecture'),
    get_string('selectNumberOfLevel', 'local_training_architecture'),
    get_string('semesterChoice', 'local_training_architecture'),
    get_string('actions', 'local_training_architecture'),
];
$training_table->data = $trainingData;

// Output JavaScript to replace content with training table.
echo '<script>document.getElementById("training-table-container").innerHTML = ' . json_encode(html_writer::table($training_table)) . ';</script>';

//----------------------------------------------------------------------

// Create LU section

$create_lu_form = new create_lu();

// Check if the lu form is submitted.
if ($create_lu_form->is_submitted()) {
    $data = $create_lu_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the lu form is cancelled.
if($create_lu_form->is_cancelled()) {
    redirect('index.php');
}

$create_lu_form->display();

// Generate HTML table for displaying lu data.
$luData = [];
if ($lus = $DB->get_records('local_training_architecture_lu', [], 'fullname')) {
    foreach ($lus as $lu) {
        $line = [];

        // Display informations.
        $line[] = $lu->fullname;
        $line[] = $lu->shortname;
        $line[] = $lu->idnumber;
        //$line[] = $lu->description;

        $buttons = '';
        
        // Edit button URL.
        $editUrl = new moodle_url('classes/edit_delete/lu.php', ['id' => $lu->id]);
        $editButton = html_writer::link($editUrl, $OUTPUT->pix_icon('t/edit', get_string('edit')));
        $buttons .= $editButton;
        
        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/lu.php', ['id' => $lu->id, 'delete' => 1]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;
        $luData[] = $line;
    }
}

// Create HTML table for displaying lu data.
$lu_table = new html_table();
$lu_table->head = [
    get_string('fullName', 'local_training_architecture'),
    get_string('shortName', 'local_training_architecture'),
    get_string('IDNumber', 'local_training_architecture'),
    //get_string('description', 'local_training_architecture'),
    get_string('actions', 'local_training_architecture')
];
$lu_table->data = $luData;

// Output JavaScript to replace content with lu table.
echo '<script>document.getElementById("lu-table-container").innerHTML = ' . json_encode(html_writer::table($lu_table)) . ';</script>';

//--------------------------------------------------------------

// Training to level names Section

$training_to_level_form = new training_level();

// Check if the training to level form is submitted.
if ($training_to_level_form->is_submitted()) {
    $data = $training_to_level_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the training to level form is cancelled.
if($training_to_level_form->is_cancelled()) {
    redirect('index.php');
}

$training_to_level_form->display();

// Generate HTML table for displaying training to level data.
$trainingToLevelData = [];
$encounteredIds = [];

if ($levelsTrainings = $DB->get_records('local_training_architecture_level_names_to_training')) {
    foreach ($levelsTrainings as $levelTraining) {
        $trainingId = $levelTraining->trainingid;

        // Avoid duplication.
        if (!in_array($trainingId, $encounteredIds)) {
            $encounteredIds[] = $trainingId;

            $levelsName = $commonFunctions->getLevelNamesByTrainingId($trainingId);

            $line = [];

            // Display informations.
            $line[] = $commonFunctions->getTrainingFullName($trainingId);
            $line[] = !empty($levelsName[1]) ? $levelsName[1] : '';
            $line[] = !empty($levelsName[2]) ? $levelsName[2] : '';

            $buttons = '';

            // Delete button URL.
            $deleteUrl = new moodle_url('classes/edit_delete/training_to_level.php', ['trainingid' => $trainingId]);
            $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
            $buttons .= $deleteButton;

            $line[] = $buttons;
            $trainingToLevelData[] = $line;
        }
    }
}

// Order by level_names->fullname asc
usort($trainingToLevelData, function($a, $b) {
    return strcmp($a[0], $b[0]);
});

// Create HTML table for displaying training to level data.
$training_to_level_table = new html_table();
$training_to_level_table->head = [
    get_string('training', 'local_training_architecture'),
    get_string('level1', 'local_training_architecture'),
    get_string('level2', 'local_training_architecture'),    
    get_string('actions', 'local_training_architecture')
];
$training_to_level_table->data = $trainingToLevelData;

// Output JavaScript to replace content with training to level table.
echo '<script>document.getElementById("training-levels-table-container").innerHTML = ' . json_encode(html_writer::table($training_to_level_table)) . ';</script>';

//----------

// Cohort to training section

$cohort_to_training_form = new cohort_to_training();

// Check if the cohort to training form is submitted.
if ($cohort_to_training_form->is_submitted()) {
    $data = $cohort_to_training_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the cohort to training form is cancelled.
if($cohort_to_training_form->is_cancelled()) {
    redirect('index.php');
}

$cohort_to_training_form->display();

// Generate HTML table for displaying cohort to training data.
$cohortToTrainingData = [];
if ($cohortsTrainings = $DB->get_records('local_training_architecture_cohort_to_training')) {
    foreach ($cohortsTrainings as $cohortTraining) {
        $line = [];

        // Display informations.
        $line[] = $commonFunctions->getTrainingFullName($cohortTraining->trainingid);
        $line[] = $commonFunctions->getCohortName($cohortTraining->cohortid);

        $buttons = '';
        
        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/cohort_to_training.php', ['id' => $cohortTraining->id]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;
        $cohortToTrainingData[] = $line;
    }
}

// Sort.
usort($cohortToTrainingData, function($a, $b) {
    $numFields = count($a);
    for ($i = 0; $i < $numFields; $i++) {
        $compare = strcmp($a[$i], $b[$i]);
        if ($compare !== 0) {
            return $compare;
        }
    }
    return 0;
});

// Create HTML table for displaying cohort to training data.
$cohort_to_training_table = new html_table();
$cohort_to_training_table->head = [
    get_string('training', 'local_training_architecture'),
    get_string('cohort', 'local_training_architecture'),
    get_string('actions', 'local_training_architecture')
];
$cohort_to_training_table->data = $cohortToTrainingData;

// Output JavaScript to replace content with cohort to training table.
echo '<script>document.getElementById("cohort-to-training-table-container").innerHTML = ' . json_encode(html_writer::table($cohort_to_training_table)) . ';</script>';

//---------------------------------------------------------

// Courses not in architecture section

$courses_not_in_architecture_form = new courses_not_in_architecture();

// Check if the form is submitted.
if ($courses_not_in_architecture_form->is_submitted()) {
    $data = $courses_not_in_architecture_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the form is cancelled.
if($courses_not_in_architecture_form->is_cancelled()) {
    redirect('index.php');
}

$courses_not_in_architecture_form->display();

// Generate HTML table for displaying data.
$coursesNotInArchitectureFormData = [];

if ($coursesNotInArchitecture = $DB->get_records('local_training_architecture_courses_not_architecture')) {
    foreach ($coursesNotInArchitecture as $courseNotInArchitecture) {
        $line = [];

        // Display informations.
        $line[] = $commonFunctions->getTrainingFullName($courseNotInArchitecture->trainingid);
        $line[] = $commonFunctions->getCourseFullName($courseNotInArchitecture->courseid);

        $buttons = '';

        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/courses_not_in_architecture.php', ['id' => $courseNotInArchitecture->id, 'delete' => 1]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;

        // Multiple selection.
        $line[] = '<input type="checkbox" name="checkbox-courses-not-in-architecture" value="' . $courseNotInArchitecture->id . '">';

        $coursesNotInArchitectureFormData[] = $line;
    }
}

// Sort.
usort($coursesNotInArchitectureFormData, function($a, $b) {
    $numFields = count($a);
    for ($i = 0; $i < $numFields; $i++) {
        $compare = strcmp($a[$i], $b[$i]);
        if ($compare !== 0) {
            return $compare;
        }
    }
    return 0;
});

// Create HTML table for displaying lu to lu data.
$coursesNotInArchitectureTable = new html_table();
$coursesNotInArchitectureTable->head = [
    get_string('training', 'local_training_architecture'),
    get_string('course', 'local_training_architecture'),
    get_string('actions', 'local_training_architecture'),
    get_string('selection', 'local_training_architecture')
];
$coursesNotInArchitectureTable->data = $coursesNotInArchitectureFormData;

echo html_writer::start_tag('div', ['class' => 'btn-container']);
echo '<button id="delete-selected-courses-not-in-architecture" class="delete-selection btn">' . get_string('deleteSelection', 'local_training_architecture') . '</button>';
echo html_writer::end_tag('div');

// Output JavaScript to replace content with table.
echo '<script>document.getElementById("courses-not-in-architecture-table-container").innerHTML = ' . json_encode(html_writer::table($coursesNotInArchitectureTable)) . ';</script>';

//--------------------------------------------------------------

// Training links section

$training_links_form = new training_links();

// Check if the training lniks form is submitted.
if ($training_links_form->is_submitted()) {
    $data = $training_links_form->get_data();
    if($data) {
        redirect('index.php');
    }
}
// Check if the training lniks form is cancelled.
if($training_links_form->is_cancelled()) {
    redirect('index.php');
}

$training_links_form->display();

// Generate HTML table for displaying training to level data.
$trainingLinksData = [];

if ($trainingsLinks = $DB->get_records('local_training_architecture_training_links')) {
    foreach ($trainingsLinks as $trainingLink) {
        $line = [];

        // Display informations.
        $line[] = $commonFunctions->getTrainingFullName($trainingLink->trainingid);

        // Checks if the entity is a lu or a course.
        if(!$trainingLink->luid) {
            $line[] = $commonFunctions->getCourseFullName($trainingLink->courseid) . ' (' . get_string('course', 'local_training_architecture') . ')';
        }
        else {
            $line[] = $commonFunctions->getluFullName($trainingLink->luid);
        }

        $line[] = $trainingLink->level;

        if($trainingLink->semester) {
            $line[] = get_string('semester', 'local_training_architecture')  . ' ' . $trainingLink->semester;
        }
        else {
            $line[] = '';
        }
        
        $buttons = '';

        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/training_links.php', ['id' => $trainingLink->id, 'delete' => 1]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;

        // Multiple selection.
        $line[] = '<input type="checkbox" name="checkbox-training-links" value="' . $trainingLink->id . '">';

        $trainingLinksData[] = $line;
    }
}

// Sort.
usort($trainingLinksData, function($a, $b) {
    $numFields = count($a);
    for ($i = 0; $i < $numFields; $i++) {
        $compare = strcmp($a[$i], $b[$i]);
        if ($compare !== 0) {
            return $compare;
        }
    }
    return 0;
});

// Create HTML table for displaying training links data.
$training_links_table = new html_table();
$training_links_table->head = [
    get_string('training', 'local_training_architecture'),
    get_string('lu', 'local_training_architecture'),
    get_string('level', 'local_training_architecture'),
    get_string('semester', 'local_training_architecture'),
    get_string('actions', 'local_training_architecture'),
    get_string('selection', 'local_training_architecture')
];
$training_links_table->data = $trainingLinksData;

echo html_writer::start_tag('div', ['class' => 'btn-container']);
echo '<button id="delete-selected-training-links" class="delete-selection btn">' . get_string('deleteSelection', 'local_training_architecture') . '</button>';
echo html_writer::end_tag('div');

// Output JavaScript to replace content with training links table.
echo '<script>document.getElementById("training-links-table-container").innerHTML = ' . json_encode(html_writer::table($training_links_table)) . ';</script>';

//-----------------

// LU to LU section

$lu_to_lu_form = new lu_to_lu();

// Check if the lu to lu form is submitted.
if ($lu_to_lu_form->is_submitted()) {
    $data = $lu_to_lu_form->get_data();
    if($data) {
        redirect('index.php');
    }
}

// Check if the LU to LU form is cancelled.
if($lu_to_lu_form->is_cancelled()) {
    redirect('index.php');
}

$lu_to_lu_form->display();

// Generate HTML table for displaying LU to LU data.
$luToluData = [];

if ($luslus = $DB->get_records('local_training_architecture_lu_to_lu')) {
    foreach ($luslus as $lu) {
        $line = [];

        // Display informations.
        $line[] = $commonFunctions->getTrainingFullName($lu->trainingid);
        $line[] = $commonFunctions->getluFullName($lu->luid1);

        // Checks if the entity is a lu or a course.
        if($lu->isluid2course === 'true') {
            $line[] = $commonFunctions->getCourseFullName($lu->luid2) . ' (' . get_string('course', 'local_training_architecture') . ')';
        }
        else {
            $line[] = $commonFunctions->getluFullName($lu->luid2);
        }

        $buttons = '';

        // Delete button URL.
        $deleteUrl = new moodle_url('classes/edit_delete/lu_to_lu.php', ['id' => $lu->id, 'delete' => 1]);
        $deleteButton = html_writer::link($deleteUrl, $OUTPUT->pix_icon('t/delete', get_string('delete')));
        $buttons .= $deleteButton;

        $line[] = $buttons;

        // Multiple selection.
        $line[] = '<input type="checkbox" name="checkbox-lu-to-lu" value="' . $lu->id . '" 
        data-trainingid="' . $lu->trainingid . '" 
        data-luid1="' . $lu->luid1 . '" 
        data-luid2="' . $lu->luid2 . '" 
        data-isluid2course="' . $lu->isluid2course . '" >';

        $luToluData[] = $line;
    }
}

// Sort.
usort($luToluData, function($a, $b) {
    $numFields = count($a);
    for ($i = 0; $i < $numFields; $i++) {
        $compare = strcmp($a[$i], $b[$i]);
        if ($compare !== 0) {
            return $compare;
        }
    }
    return 0;
});

// Create HTML table for displaying LU to LU data.
$lu_to_lu_table = new html_table();
$lu_to_lu_table->head = [
    get_string('training', 'local_training_architecture'),
    get_string('lu', 'local_training_architecture') . ' ' . 1,
    get_string('lu', 'local_training_architecture') . ' ' . 2,
    get_string('actions', 'local_training_architecture'),
    get_string('selection', 'local_training_architecture')
];
$lu_to_lu_table->data = $luToluData;

echo html_writer::start_tag('div', ['class' => 'btn-container']);
echo '<button id="delete-selected-lu-to-lu" class="delete-selection btn">' . get_string('deleteSelection', 'local_training_architecture') . '</button>';
echo html_writer::end_tag('div');

// Output JavaScript to replace content with LU to LU table.
echo '<script>document.getElementById("lu-to-lu-table-container").innerHTML = ' . json_encode(html_writer::table($lu_to_lu_table)) . ';</script>';

echo $OUTPUT->footer();