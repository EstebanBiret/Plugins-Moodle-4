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
 * Edit - delete LU 
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../form/edit_lu.php');
require_once(dirname(__FILE__) . '/../functions/lu_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');

global $DB;
$luFunctions = new lu_functions();
$commonFunctions = new common_functions();

$id = optional_param('id', 0, PARAM_INT);
$delete   = optional_param('delete', 0, PARAM_BOOL);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$returnUrl = $CFG->wwwroot.'/local/training_architecture/index.php';

$url = new moodle_url('/local/training_architecture/classes/edit_delete/lu.php');

if($id) {
    $url->param('id', $id);
    if (!$lu = $DB->get_record('local_training_architecture_lu', ['id' => $id])) {
        throw new \moodle_exception('invalid_parameter_exception');
    }
}
else {
    redirect($returnUrl); // No id, or invalid id format
}

$PAGE->set_url($url);
require_login();
$context = context_system::instance();
require_capability('local/training_architecture:manage',$context);
$PAGE->set_context($context);
$PAGE->set_title(get_string('editLuTitle', 'local_training_architecture'));
$PAGE->set_heading(get_string('editLuTitle', 'local_training_architecture') . " " . $commonFunctions->getLuFullName($id));
$PAGE->set_pagelayout('admin');

// Delete
if ($id and $delete) {

    // LU has references
    if ($luFunctions->isLuUsed($id)) {
        $PAGE->set_title(get_string('deleteLuTitle', 'local_training_architecture'));
        $PAGE->set_heading(get_string('deleteLuTitle', 'local_training_architecture') . " " . $commonFunctions->getLuFullName($id));
        echo $OUTPUT->header();        
        echo $OUTPUT->notification(get_string('notifyErrorLu', 'local_training_architecture'), 'notifyproblem');
        echo $OUTPUT->continue_button(new moodle_url('/local/training_architecture/index.php'));
        echo $OUTPUT->footer();
        die;
    }

    if (!$confirm) { // Cancel
        $PAGE->set_title(get_string('deleteLuTitle', 'local_training_architecture'));
        $PAGE->set_heading(get_string('deleteLuTitle', 'local_training_architecture') . " " . $commonFunctions->getLuFullName($id));
        echo $OUTPUT->header();
        $optionsYes = ['id' => $id, 'delete' => 1, 'sesskey' => sesskey(), 'confirm' => 1];
        $formcontinue = new single_button(new moodle_url('/local/training_architecture/classes/edit_delete/lu.php', $optionsYes), get_string('confirmYes', 'local_training_architecture'), 'get');
        $formcancel = new single_button(new moodle_url('/local/training_architecture/index.php'), get_string('confirmNo', 'local_training_architecture'), 'get');
        echo $OUTPUT->confirm(get_string('deleteLuWarning', 'local_training_architecture'), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;

    } else { // Confirm
        $luFunctions->delete($id);
        redirect($returnUrl);
    }
}

// Edit
$luDescription = [
    'text' => $lu->description
];

$default_data = [
    'luFullName' => $lu->fullname,
    'luShortName' => $lu->shortname,
    'luIDNumber' => $lu->idnumber,
    'luDescription' => $luDescription,
    'id' => $lu->id,
];

$edit_lu_form = new edit_lu();
$edit_lu_form->set_data($default_data);

// Cancel or confirm
if ($edit_lu_form->is_cancelled() || $data = $edit_lu_form->get_data()) {
    redirect($CFG->wwwroot.'/local/training_architecture/index.php');

}

echo $OUTPUT->header();
$edit_lu_form->display();
echo $OUTPUT->footer();