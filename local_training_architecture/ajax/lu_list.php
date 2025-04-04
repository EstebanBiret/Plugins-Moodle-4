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
 * AJAX for LU informations
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

require_once(dirname(__FILE__) . '/../../../config.php');

global $DB;

$trainingId = $_POST['trainingId'];

$links = $DB->get_records('local_training_architecture_training_links', ['trainingid' => $trainingId]);

$result = [];

foreach ($links as $link) {
    $lu_info = $DB->get_record('local_training_architecture_lu', ['id' => $link->luid]);
    
    if ($lu_info) {
        $lu_data = [
            'id' => $link->luid,
            'fullname' => $lu_info->fullname
        ];
        $result[] = $lu_data;
    }
}

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode($result);
