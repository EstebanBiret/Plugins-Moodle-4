<?php

/* This file recovers the data to be displayed for courses in architecture */

//require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once(dirname(__FILE__) . '/../functions.php');

//get the cohortId AND the studentId
$value = $_POST['value'];
$ids = explode('/', $value);
$cohortId = $ids[0];
$trainingId = $ids[1];
$studentId = $ids[2];

//store these variables in GLOBAL variables
global $SESSION;
$SESSION->studentId = $studentId;
$SESSION->cohortId = $cohortId;

// Main logic
$numberOfLevels = $DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $trainingId]);

$result = [];

if ($numberOfLevels == '2') {
  $result = getBlocks($trainingId, $studentId, false, '', '');
} 
else { //1
  $result = getModules(null, $trainingId, $studentId, false);
}

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['result' => $result, 
'levels' => $numberOfLevels, 
'architecture' => get_string('architecture', 'report_students_achievements'), 
'noArchitecture' => get_string('noArchitecture', 'report_students_achievements'),
'completed' => get_string('completed', 'report_students_achievements'), 
'uncompleted' => get_string('uncompleted', 'report_students_achievements'), 
'noCourses' => get_string('noCourses', 'report_students_achievements'), 
'noActivities' => get_string('noActivities', 'report_students_achievements'), 
'activityName' => get_string('activityName', 'report_students_achievements'), 
'type' => get_string('type', 'report_students_achievements'), 
'completionStatus' => get_string('completionStatus', 'report_students_achievements'), 
'openingDate' => get_string('openingDate', 'report_students_achievements')]);