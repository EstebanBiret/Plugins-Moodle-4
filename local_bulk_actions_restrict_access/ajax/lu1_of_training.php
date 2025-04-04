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
 * Return the LU1 of selected training
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

$trainingId = $_POST['trainingId'];
$addedLu1 = [];

// Get the number of levels of the training
$levels = $DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $trainingId]);

if($levels == '1') {
    $nextForm = 'course';
    $lu1Records = $DB->get_records('local_training_architecture_lu_to_lu', ['trainingid' => $trainingId, 'isluid2course' => 'true']);
}
else {
    $nextForm = 'lu2';
    $lu1Records = $DB->get_records('local_training_architecture_lu_to_lu', ['trainingid' => $trainingId, 'isluid2course' => 'false']);
}

$lu1 = [];

// For all LU1 of this training, the id and its name are stored
foreach ($lu1Records as $lu1Record) {
    $id = $lu1Record->luid1;

    if (!isset($addedLu1[$id])) {
        $lu1[] = [
            'id' => $id,
            'fullname' => $DB->get_field('local_training_architecture_lu', 'fullname', 
            ['id' => $id])
        ];
        $addedLu1[$id] = true;
    }
}

$message = get_string('choose_select', 'local_bulk_actions_restrict_access');
$error = get_string('no_lu', 'local_bulk_actions_restrict_access');

//send result as JSON response
header('Content-Type: application/json');
echo json_encode(['lu1' => $lu1, 'message' => $message, 'error' => $error, 'levels' => $levels, 'nextForm' => $nextForm]);