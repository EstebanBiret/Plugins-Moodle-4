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
 * Return the trainings of selected cohort
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */


// Require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

$cohortId = $_POST['cohortId'];

// Get all the trainings of this cohort
$trainingRecords = $DB->get_records('local_training_architecture_cohort_to_training', ['cohortid' => $cohortId]);

$trainings = [];

// For all student cohorts, the cohort id and its name are stored
foreach ($trainingRecords as $trainingRecord) {
    $trainings[] = [
        'id' => $trainingRecord->trainingid,
        'fullname' => $DB->get_field('local_training_architecture_training', 'fullname', 
        ['id' => $trainingRecord->trainingid])
    ];
}

$message = get_string('choose_select', 'local_bulk_actions_restrict_access');
$error = get_string('no_trainings', 'local_bulk_actions_restrict_access');

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['trainings' => $trainings, 'message' => $message, 'error' => $error]);