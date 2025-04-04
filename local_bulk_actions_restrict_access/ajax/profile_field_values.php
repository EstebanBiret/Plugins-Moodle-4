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
 * Return the profile field values of selected cohort and profile field
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require moodle file
require_once(dirname(__FILE__) . '/../../../config.php');

$cohortId = $_POST['cohortId'];
$profileFieldId = $_POST['profileFieldId'];
$profileFieldValues = [];
$addedNames = [];

$sqlStudents = "SELECT DISTINCT u.*
	FROM {user} u
	INNER JOIN {role_assignments} ra ON (ra.userid = u.id)
	INNER JOIN {cohort_members} cm ON (cm.userid = u.id)
	WHERE ra.roleid = :roleid
	AND cm.cohortid = :cohortid
	ORDER BY u.lastname ASC;";

// Array of parameters (5 corresponds to the student role)
$params = ['roleid' => 5, 'cohortid' => $cohortId]; 

$students = $DB->get_records_sql($sqlStudents, $params);

foreach ($students as $student) {
    $name = $DB->get_field('user_info_data', 'data', ['userid' => $student->id, 'fieldid' => $profileFieldId]);
    $shortname = $DB->get_field('user_info_field', 'shortname', ['id' => $profileFieldId]);
    
    if (!isset($addedNames[$name])) {
        $profileFieldValues[] = [
            'id' => $profileFieldId,
            'name' => $name,
            'shortname' => $shortname
        ];
        $addedNames[$name] = true;
    }
}

$message = get_string('choose_select', 'local_bulk_actions_restrict_access');
$error = get_string('no_data_field', 'local_bulk_actions_restrict_access');

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['profileFieldValues' => $profileFieldValues, 'message' => $message, 'error' => $error]);