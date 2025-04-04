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
 * Return the courses of selected training and LU
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

// Get values
$trainingId = $_POST['trainingId'];
$lu1Id = $_POST['lu1Id'];

$coursesRecords = $DB->get_records('local_training_architecture_lu_to_lu', ['trainingid' => $trainingId, 'luid1' => $lu1Id, 'isluid2course' => 'true']);
$courses = [];

// For all courses of this training and LU, the id and its name are stored
foreach ($coursesRecords as $coursesRecord) {
    $courses[] = [
        'id' => $coursesRecord->luid2,
        'fullname' => $DB->get_field('course', 'fullname', 
        ['id' => $coursesRecord->luid2])
    ];
}

$message = get_string('choose_select', 'local_bulk_actions_restrict_access');
$error = get_string('no_courses', 'local_bulk_actions_restrict_access');

//send result as JSON response
header('Content-Type: application/json');
echo json_encode(['courses' => $courses, 'message' => $message, 'error' => $error]);