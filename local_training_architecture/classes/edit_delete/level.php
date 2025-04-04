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
 * Edit - Delete Level
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../form/edit_level.php');
require_once(dirname(__FILE__) . '/../functions/level_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');

global $DB;
$levelFunctions = new level_functions();
$commonFunctions = new common_functions();

$id = optional_param('id', 0, PARAM_INT);
$delete   = optional_param('delete', 0, PARAM_BOOL);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$returnUrl = $CFG->wwwroot.'/local/training_architecture/index.php';

$url = new moodle_url('/local/training_architecture/classes/edit_delete/level.php');

if($id) {
    $url->param('id', $id);
    if (!$level = $DB->get_record('local_training_architecture_level_names', ['id' => $id])) {
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
$PAGE->set_title(get_string('editLevelTitle', 'local_training_architecture'));
$PAGE->set_heading(get_string('editLevelTitle', 'local_training_architecture') . " " . $commonFunctions->getLevelFullName($id));
$PAGE->set_pagelayout('admin');

// Delete
if ($id and $delete) {

    // Level name has references
    if ($levelFunctions->isLevelUsed($id)) {
        $PAGE->set_title(get_string('deleteLevelTitle', 'local_training_architecture'));
        $PAGE->set_heading(get_string('deleteLevelTitle', 'local_training_architecture') . " " . $commonFunctions->getLevelFullName($id));
        echo $OUTPUT->header();        
        echo $OUTPUT->notification(get_string('notifyErrorLevel', 'local_training_architecture'), 'notifyproblem');
        echo $OUTPUT->continue_button(new moodle_url('/local/training_architecture/index.php'));
        echo $OUTPUT->footer();
        die;
    }

    if (!$confirm) { // Cancel
        $PAGE->set_title(get_string('deleteLevelTitle', 'local_training_architecture'));
        $PAGE->set_heading(get_string('deleteLevelTitle', 'local_training_architecture') . " " . $commonFunctions->getLevelFullName($id));
        echo $OUTPUT->header();
        $optionsYes = ['id' => $id, 'delete' => 1, 'sesskey' => sesskey(), 'confirm' => 1];
        $formcontinue = new single_button(new moodle_url('/local/training_architecture/classes/edit_delete/level.php', $optionsYes), get_string('confirmYes', 'local_training_architecture'), 'get');
        $formcancel = new single_button(new moodle_url('/local/training_architecture/index.php'), get_string('confirmNo', 'local_training_architecture'), 'get');
        echo $OUTPUT->confirm(get_string('deleteLevelNameWarning', 'local_training_architecture'), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;

    } else { // Confirm
        $levelFunctions->delete($id);
        redirect($CFG->wwwroot.'/local/training_architecture/index.php');
    }
}

// Edit
$default_data = [
    'levelFullName' => $level->fullname,
    'levelShortName' => $level->shortname,
    'levelDescription' => $level->description,
    'id' => $level->id,
];

$edit_level_form = new edit_level();
$edit_level_form->set_data($default_data);

// Cancel or confirm
if ($edit_level_form->is_cancelled() || $data = $edit_level_form->get_data()) {
    redirect($CFG->wwwroot.'/local/training_architecture/index.php');

}

echo $OUTPUT->header();
$edit_level_form->display();
echo $OUTPUT->footer();