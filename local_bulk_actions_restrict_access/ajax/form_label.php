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
 * Return informations to construct the label of the form
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

if($_POST['type'] == 'profile') {
    $valueName = $DB->get_field('user_info_field', 'name', ['id' => $_POST['profileFieldId']]);
}
elseif($_POST['type'] == 'group') {
    $valueName = $DB->get_field('groups', 'name', ['id' => $_POST['groupId']]);
}

$typeMessage = get_string('type', 'local_bulk_actions_restrict_access');
$dateMessage = get_string('date', 'local_bulk_actions_restrict_access');
$group = get_string('group', 'local_bulk_actions_restrict_access');

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['typeMessage' => $typeMessage, 'dateMessage' => $dateMessage, 'valueName' => $valueName, 'group' => $group]);