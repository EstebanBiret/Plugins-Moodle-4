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
 * Sort LU page.
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/classes/functions/common_functions.php');

global $DB;
$commonFunctions = new common_functions();

$trainingid = optional_param('trainingid', 0, PARAM_INT);
$luid = optional_param('luid', 0, PARAM_INT);

$returnUrl = $CFG->wwwroot.'/local/training_architecture/index.php';

$url = new moodle_url('/local/training_architecture/sort.php');

// Checking if trainingid is provided and valid.
if($trainingid) {
    $url->param('trainingid', $trainingid);
    if (!$training = $DB->get_record('local_training_architecture_training', ['id' => $trainingid])) {
        throw new \moodle_exception('invalid_parameter_exception');
    }
    // Checking if luid and trainingid are provided and if the number of levels for the training is 1.
    if ($luid && $trainingid && $commonFunctions->getNumberOfLevels($trainingid) == "1") {
        throw new \moodle_exception('invalid_parameter_exception');
    }
}
else {
    redirect($returnUrl); // Redirecting if no id or invalid id format.
}

// Setting up page URL, permissions, and layout.
$PAGE->set_url($url);
require_login();
$context = context_system::instance();
require_capability('local/training_architecture:manage',$context);
$PAGE->set_context($context);
$PAGE->set_title(get_string('sortLuTitle', 'local_training_architecture'));
$PAGE->set_heading(get_string('sortLu', 'local_training_architecture') . $commonFunctions->getTrainingFullName($trainingid));
$PAGE->set_pagelayout('admin');
$PAGE->requires->js('/local/training_architecture/amd/src/sort.js');
$PAGE->requires->js('/local/training_architecture/amd/src/functions.js');
$PAGE->requires->jquery();

echo $OUTPUT->header();

// HTML table for LU1
$lu1_data = [];

$lu1Id = $DB->get_field('local_training_architecture_level_names_to_training', 'levelnamesid', ['trainingid' => $trainingid, 'level' => '1']); 
$lu1Name = $DB->get_field('local_training_architecture_level_names', 'fullname', ['id' => $lu1Id]); 

if ($training->granularitylevel == '1') {
    $lus = $DB->get_records_sql('SELECT DISTINCT luid1 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ?', [$trainingid]);
}
else {
    $lus = $DB->get_records_sql('SELECT DISTINCT luid1 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ? AND isluid2course = ?', [$trainingid, 'false']);
}

// Initializing variables and setting up HTML tables.
$active_lu_id = 0;
$found = false;
$iterator = 0;
$lu1_table = new html_table();
$lu1_table->id = 'lu1-table';
$lu1_table->head = [
    $lu1Name = !empty($lu1Name) ? $lu1Name : get_string('lu1', 'local_training_architecture'),    
    get_string('order', 'local_training_architecture'),
];

// Sorting LU1 data.
if ($lus) {

    $sorted_lus = [];

    foreach ($lus as $lu) {
        $sorted_lus[$lu->luid1] = $lu;
    }

    uasort($sorted_lus, function($a, $b) use ($trainingid, $DB) {
        $sortOrderA = $DB->get_field('local_training_architecture_order', 'sortorder', ['trainingid' => $trainingid, 'luid' => $a->luid1]);
        $sortOrderB = $DB->get_field('local_training_architecture_order', 'sortorder', ['trainingid' => $trainingid, 'luid' => $b->luid1]);
        return $sortOrderA - $sortOrderB;
    });

    // Generating table rows for LU1.
    foreach ($sorted_lus as $lu) {
        $line = [];

        // Adding lu name or link to the row.
        if($commonFunctions->getNumberOfLevels($trainingid) == "1") {
            $line[] = $commonFunctions->getluFullName($lu->luid1);

        }
        else {
            $lu2Url = new moodle_url('/local/training_architecture/sort.php', ['trainingid' => $trainingid, 'luid' => $lu->luid1]);
            $line[] = html_writer::link($lu2Url, $commonFunctions->getluFullName($lu->luid1));
        }

        // Adding sorting actions to the row.
        $actions = '';

        $sort_asc = html_writer::link($url, $OUTPUT->pix_icon('t/sort_asc', get_string('up')), ['class' => 'arrow-button-1 up', 'data-luid' => $lu->luid1, 'data-trainingid' => $trainingid, 'data-granularitylevel' => $training->granularitylevel]);
        $actions .= $sort_asc;
        
        $sort_desc = html_writer::link($url, $OUTPUT->pix_icon('t/sort_desc', get_string('down')), ['class' => 'arrow-button-1 down', 'data-luid' => $lu->luid1, 'data-trainingid' => $trainingid, 'data-granularitylevel' => $training->granularitylevel]);
        $actions .= $sort_desc;

        $line[] = $actions;
        $lu1_data[] = $line;

        // Adding row classes for styling.
        $lu1_table->rowclasses[$iterator] = 'listitem lu-row-' . $lu->luid1;

        // Tracking active lu.
        if($luid == $lu->luid1) {
            $found = true;
        }

        if(!$found) {
            $active_lu_id++;
        }
        $iterator++;
    }
}

$lu1_table->data = $lu1_data;

// Adding style on the active LU.
if ($trainingid and $luid) {
    $lu1_table->rowclasses[$active_lu_id] .= ' active-lu';
}    

if ($trainingid and $luid) {

    $lu2_data = [];
    $lu2Id = $DB->get_field('local_training_architecture_level_names_to_training', 'levelnamesid', ['trainingid' => $trainingid, 'level' => '2']); 
    $lu2Name = $DB->get_field('local_training_architecture_level_names', 'fullname', ['id' => $lu2Id]); 

    if ($training->granularitylevel == '1') {
        $lus2 = $DB->get_records_sql('SELECT DISTINCT luid2 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ? AND luid1 = ?', [$trainingid, $luid]);
    }
    else {
        $lus2 = $DB->get_records_sql('SELECT DISTINCT luid2 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ? AND isluid2course = ? AND luid1 = ?', [$trainingid, 'false', $luid]);
    }    

    $iterator2 = 0;

    $lu2_table = new html_table();
    $lu2_table->head = [
        $lu2Name = !empty($lu2Name) ? $lu2Name : get_string('lu2', 'local_training_architecture'),    
        get_string('order', 'local_training_architecture'),
    ];

    // Sorting LU2 data.
    if ($lus2) {

        $sorted_lus2 = [];
    
        foreach ($lus2 as $lu2) {
            $sorted_lus2[$lu2->luid2] = $lu2;
        }
    
        uasort($sorted_lus2, function($a, $b) use ($trainingid, $DB) {
            $sortOrderA = $DB->get_field('local_training_architecture_order', 'sortorder', ['trainingid' => $trainingid, 'luid' => $a->luid2]);
            $sortOrderB = $DB->get_field('local_training_architecture_order', 'sortorder', ['trainingid' => $trainingid, 'luid' => $b->luid2]);
            return $sortOrderA - $sortOrderB;
        });
    
        // Generating table rows for LU2.
        foreach ($sorted_lus2 as $lu2) {
            $line = [];

            // Adding lu name to the row.
            $line[] = $commonFunctions->getluFullName($lu2->luid2);
    
            // Adding sorting actions to the row.
            $actions = '';

            $luUrl = new moodle_url('/local/training_architecture/sort.php', ['trainingid' => $trainingid, 'luid' => $luid]);
            $sort_asc = html_writer::link($luUrl, $OUTPUT->pix_icon('t/sort_asc', get_string('up')), ['class' => 'arrow-button-2 up', 'data-luid' => $lu2->luid2,'data-trainingid' => $trainingid, 'data-granularitylevel' => $training->granularitylevel]);
            $actions .= $sort_asc;
            
            $sort_desc = html_writer::link($luUrl, $OUTPUT->pix_icon('t/sort_desc', get_string('down')), ['class' => 'arrow-button-2 down', 'data-luid' => $lu2->luid2,'data-trainingid' => $trainingid, 'data-granularitylevel' => $training->granularitylevel]);
            $actions .= $sort_desc;
    
            $line[] = $actions;
            $lu2_data[] = $line;

            // Adding row classes for styling.
            $lu2_table->rowclasses[$iterator2] = 'lu-row-' . $lu2->luid2;

            $iterator2++;

        }
    }
    $lu2_table->data = $lu2_data;
}

// Displaying LU1 table if data exists.
if($lu1_data) {
    echo html_writer::start_tag('div', ['id' => 'course-category-listings', 'style' => 'width: 48%; float: left;']);
    echo html_writer::table($lu1_table);
    echo html_writer::end_tag('div');
}
else {
    echo get_string('noLus', 'local_training_architecture');
}

// Displaying LU2 table if trainingid and luid are provided.
if($trainingid and $luid) {
    echo html_writer::start_tag('div', ['style' => 'width: 48%; float: right;']);
    echo html_writer::table($lu2_table);
    echo html_writer::end_tag('div');
}

// Displaying order information and back button.
echo html_writer::start_tag('div', ['style' => 'clear: both;']);
echo get_string('orderInformations', 'local_training_architecture');
echo $OUTPUT->single_button(new moodle_url('/local/training_architecture/index.php'), get_string('back', 'local_training_architecture'), 'POST', ['style' => 'margin-top: 20px; margin-left: 20px;']);
echo html_writer::end_tag('div');
echo $OUTPUT->footer();