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
 * Return the availability dates for profile
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/../functions.php');

$activityId = $_POST['activityId'];
$profileFieldValue = $_POST['profileFieldValue'];
$profileFieldShortName = $_POST['profileFieldShortName'];

$date = availabilityDates($activityId, 'profile', $profileFieldValue, $profileFieldShortName, null);

if($date) {
    $startDate = $date['startDate'];
    $endDate = $date['endDate'];
}
else {
    $startDate = null;
    $endDate = null;
}

$startDateMessage = get_string('start_date', 'local_bulk_actions_restrict_access');
$endDateMessage = get_string('end_date', 'local_bulk_actions_restrict_access');
$dates = get_string('dates_found', 'local_bulk_actions_restrict_access');
$date = get_string('date_found', 'local_bulk_actions_restrict_access');
$none = get_string('no_date', 'local_bulk_actions_restrict_access');

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['startDate' => $startDate, 
    'endDate' => $endDate, 
    'startDateMessage' => $startDateMessage, 
    'endDateMessage' => $endDateMessage, 
    'dates' => $dates, 
    'date' => $date, 
    'none' => $none
]);