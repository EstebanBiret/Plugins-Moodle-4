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
 * Edit - Delete Training
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../form/edit_training.php');
require_once(dirname(__FILE__) . '/../functions/training_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');

global $DB;
$trainingFunctions = new training_functions();
$commonFunctions = new common_functions();

$id = optional_param('id', 0, PARAM_INT);
$delete   = optional_param('delete', 0, PARAM_BOOL);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$returnUrl = $CFG->wwwroot.'/local/training_architecture/index.php';

$url = new moodle_url('/local/training_architecture/classes/edit_delete/training.php');

if($id) {
    $url->param('id', $id);
    if (!$training = $DB->get_record('local_training_architecture_training', ['id' => $id])) {
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
$PAGE->set_title(get_string('editTrainingTitle', 'local_training_architecture'));
$PAGE->set_heading(get_string('editTrainingTitle', 'local_training_architecture') . " " . $commonFunctions->getTrainingFullName($id));
$PAGE->set_pagelayout('admin');

// Delete
if ($id and $delete) {
    if (!$confirm) { // Cancel
        $PAGE->set_title(get_string('deleteTrainingTitle', 'local_training_architecture'));
        $PAGE->set_heading(get_string('deleteTrainingTitle', 'local_training_architecture') . " " . $commonFunctions->getTrainingFullName($id));
        echo $OUTPUT->header();
        $optionsYes = ['id' => $id, 'delete' => 1, 'sesskey' => sesskey(), 'confirm' => 1];
        $formcontinue = new single_button(new moodle_url('/local/training_architecture/classes/edit_delete/training.php', $optionsYes), get_string('confirmYes', 'local_training_architecture'), 'get');
        $formcancel = new single_button(new moodle_url('/local/training_architecture/index.php'), get_string('confirmNo', 'local_training_architecture'), 'get');
        echo $OUTPUT->confirm(get_string('deleteTrainingWarning', 'local_training_architecture'), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;

    } else { // Confirm
        $trainingFunctions->delete($id);
        redirect($returnUrl);
    }
}

// Edit
$trainingSemester = ($training->issemester == 1) ? 'yes' : 'no';

$trainingDescription = [
    'text' => $training->description
];

$default_data = [
    'trainingFullName' => $training->fullname,
    'trainingShortName' => $training->shortname,
    'trainingIDNumber' => $training->idnumber,
    'trainingDescription' => $trainingDescription,
    'trainingLevel' => $training->granularitylevel,
    'trainingSemester' => $trainingSemester,
    'id' => $training->id,
];

$edit_training_form = new edit_training();
$edit_training_form->set_data($default_data);

// Cancel or confirm
if ($edit_training_form->is_cancelled() || $data = $edit_training_form->get_data()) {
    redirect($CFG->wwwroot.'/local/training_architecture/index.php');

}

echo $OUTPUT->header();
$edit_training_form->display();
echo $OUTPUT->footer();