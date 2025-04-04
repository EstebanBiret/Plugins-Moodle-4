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
 * Delete multiple training links
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/../functions/training_links_functions.php');
require_once(dirname(__FILE__) . '/../functions/common_functions.php');

global $DB;
$commonFunctions = new common_functions();
$trainingLinksFunctions = new training_links_functions();

$ids = isset($_GET['id']) ? $_GET['id'] : [];
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$returnUrl = $CFG->wwwroot.'/local/training_architecture/index.php';
$url = '';

if (!empty($ids)) {
    if(!is_array($ids)) {
        throw new \moodle_exception('invalid_parameter_exception');
    }
    foreach ($ids as $id) {
        if (!$DB->get_record('local_training_architecture_training_links', ['id' => $id])) {
            throw new \moodle_exception('invalid_parameter_exception');
        }
    }

    $string = '';
    $count = count($ids);
    foreach ($ids as $key => $id) {
        $string .= 'id[]='.$id;
        if ($key < $count - 1) {
            $string .= '&';
        }
    }
    $url = $CFG->wwwroot . '/local/training_architecture/classes/multiple_delete/training_links.php?' . $string;
} else {
    redirect($returnUrl);
}

$PAGE->set_url($url);
require_login();
$context = context_system::instance();
require_capability('local/training_architecture:manage',$context);
$PAGE->set_context($context);
$PAGE->set_title(get_string('deleteTrainingLinks', 'local_training_architecture'));
$PAGE->set_heading(get_string('deleteTrainingLinks', 'local_training_architecture'));
$PAGE->set_pagelayout('admin');

// Delete
if ($ids) {

    // Links has references
    foreach ($ids as $id) {
        $link = $DB->get_record('local_training_architecture_training_links', ['id' => $id]);

        if ($trainingLinksFunctions->isLuUsed($link->luid, $link->courseid, $link->trainingid, )) {
            $PAGE->set_title(get_string('deleteMultipleTitle1', 'local_training_architecture') . 
            count($ids) . get_string('deleteMultipleTrainingLinksTitle2', 'local_training_architecture'));
    
            $PAGE->set_heading(get_string('deleteMultipleTitle1', 'local_training_architecture') . 
            count($ids) . get_string('deleteMultipleTrainingLinksTitle2', 'local_training_architecture'));
            
            echo $OUTPUT->header();        
            echo $OUTPUT->notification(get_string('notifyErrorMultipleTrainingLinks', 'local_training_architecture'), 'notifyproblem');
            echo $OUTPUT->continue_button(new moodle_url('/local/training_architecture/index.php'));
            echo $OUTPUT->footer();
            die;
        }
    }

    if (!$confirm) { // Cancel
        $PAGE->set_title(get_string('deleteMultipleTitle1', 'local_training_architecture') . 
        count($ids) . get_string('deleteMultipleTrainingLinksTitle2', 'local_training_architecture'));

        $PAGE->set_heading(get_string('deleteMultipleTitle1', 'local_training_architecture') . 
        count($ids) . get_string('deleteMultipleTrainingLinksTitle2', 'local_training_architecture'));

        echo $OUTPUT->header();

        $optionsYes = $url .= '&sesskey='.sesskey().'&confirm='.'1';
        $formcontinue = new single_button(new moodle_url($optionsYes), get_string('confirmYes', 'local_training_architecture'), 'get');
        
        $formcancel = new single_button(new moodle_url('/local/training_architecture/index.php'), get_string('confirmNo', 'local_training_architecture'), 'get');
        echo $OUTPUT->confirm(get_string('deleteMultipleWarning', 'local_training_architecture'), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;

    } else { // Confirm
        foreach ($ids as $id) {
            $trainingLinksFunctions->deleteLink($id);
        }
        redirect($returnUrl);
    }
}
echo $OUTPUT->header();
echo $OUTPUT->footer();