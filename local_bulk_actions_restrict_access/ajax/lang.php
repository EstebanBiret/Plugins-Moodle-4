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
 * Return the lang string of checked checkboxes informations
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

$plural = get_string('plural', 'local_bulk_actions_restrict_access');
$singular = get_string('singular', 'local_bulk_actions_restrict_access');
$none = get_string('no_selected_activities', 'local_bulk_actions_restrict_access');

$same = get_string('notif_same_date', 'local_bulk_actions_restrict_access');
$greater = get_string('notif_end_before_start', 'local_bulk_actions_restrict_access');

$checkAll = get_string('checkAll', 'local_bulk_actions_restrict_access');

$noDataType = get_string('no_data_type', 'local_bulk_actions_restrict_access');

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['plural' => $plural, 
    'singular' => $singular,
    'none' => $none,
    'same' => $same, 
    'greater' => $greater,
    'checkAll' => $checkAll,
    'noDataType' => $noDataType
]);