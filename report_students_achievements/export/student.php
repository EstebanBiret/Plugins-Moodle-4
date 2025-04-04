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
 * This file is responsible for producing the downloadable versions of student achievements (by student)
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_student_achievements
*/

// Require moodle files
require_once ("../../../config.php");
require_once($CFG->dirroot . '/cohort/lib.php');
require_once(dirname(__FILE__) . '/../functions.php');

// Get the ids from the GLOBAL variable (set in ajax files)
global $SESSION;
$cohortId = $SESSION->cohortId;
$trainingId = $SESSION->trainingId;
$studentId = $SESSION->studentId;

// Get student's infos
$studentFirstName = $DB->get_field('user', 'firstname', ['id' => $studentId]);
$studentLastName = $DB->get_field('user', 'lastname', ['id' => $studentId]);

// Main logic
$numberOfLevels = $DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $trainingId]);

$result = [];

if ($numberOfLevels == '2') {
    $result = getBlocks($trainingId, $studentId, true, $studentFirstName, $studentLastName);
} else { //1
    $result = getModules(null, $trainingId, $studentId, true);
}

$result = exportAddCoursesNotInArchitecture($result, $trainingId, $studentId, $studentFirstName, $studentLastName, $numberOfLevels, false);

//------DOWNLOAD PART------//

$columns = exportGetColumns($numberOfLevels, $trainingId);
$data = exportGetData($cohortId, $trainingId, $numberOfLevels, $result, 'student');

// Format of the file (csv, xlxs, ods, pdf, json)
$dataformat = optional_param('export', '', PARAM_ALPHA);

// Download file
\core\dataformat::download_data($studentFirstName . '_' . $studentLastName . '_' . $data['cohortName'] . '_' . $data['trainingName'] . '_' . $data['extractionDate'], $dataformat, $columns, $data['data']); // lib/classes/dataformat.php