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
 * AJAX for sort LU
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

require_once(dirname(__FILE__) . '/../../../config.php');

global $DB;

$luId = $_POST['luId'];
$luToMove = $_POST['luToMove'];
$trainingId = $_POST['trainingId'];
$granularityLevel = $_POST['granularityLevel'];
$level = $_POST['level'];

$record = new stdClass();
$record2 = new stdClass();

$actualLu = $DB->get_record('local_training_architecture_order', ['trainingid' => $trainingId, 'luid' => $luId]);
$luToMove = $DB->get_record('local_training_architecture_order', ['trainingid' => $trainingId, 'luid' => $luToMove]);

$old_lu_order = $actualLu->sortorder;

// Actual LU
$record->id = $actualLu->id;
$record->trainingid = $actualLu->trainingid;
$record->luid = $actualLu->luid;
$record->sortorder = $luToMove->sortorder;

// LU to move
$record2->id = $luToMove->id;
$record2->trainingid = $luToMove->trainingid;
$record2->luid = $luToMove->luid;
$record2->sortorder = $old_lu_order;


$DB->update_record('local_training_architecture_order', $record);
$DB->update_record('local_training_architecture_order', $record2);

if($level == "level1") {
    if ($granularityLevel == '1') {
        $newOrder = $DB->get_records_sql('SELECT DISTINCT luid1 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ?', [$trainingId]);
    }
    else {
        $newOrder = $DB->get_records_sql('SELECT DISTINCT luid1 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ? AND isluid2course = ?', [$trainingId, 'false']);
    }
}
else {
    if ($granularityLevel == '1') {
        $newOrder = $DB->get_records_sql('SELECT DISTINCT luid1 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ?', [$trainingId]);
    }
    else {
        $newOrder = $DB->get_records_sql('SELECT DISTINCT luid1 FROM {local_training_architecture_lu_to_lu} WHERE trainingid = ? AND isluid2course = ?', [$trainingId, 'true']);
    }
}

$result = [];

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode($result);
