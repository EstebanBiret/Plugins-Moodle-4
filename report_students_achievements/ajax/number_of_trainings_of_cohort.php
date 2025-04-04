<?php 

/* This file allows you to know the number of trainings in the cohort */

//require moodle file
require_once(dirname(__FILE__) . '/../../../config.php');

//get the cohortId
$cohortId = $_POST['cohortId'];

//store this variable in a GLOBAL variable (for export)
global $SESSION;
$SESSION->cohortId = $cohortId;

$trainings = $DB->get_records('local_training_architecture_cohort_to_training', ['cohortid' => $cohortId]);

//$numberOfTrainings = $DB->count_records('local_training_architecture_cohort_to_training', ['cohortid' => $cohortId]);
$numberOfTrainings = count($trainings);

$message = get_string('no_trainings', 'report_students_achievements');

if($numberOfTrainings == 1) {
    $recordId = key($trainings);
    $trainingFullName = $DB->get_field('local_training_architecture_training', 'fullname', ['id' => $trainings[$recordId]->trainingid]);
    $trainingId = $trainings[$recordId]->trainingid;
}

//send result as JSON response
header('Content-Type: application/json');
echo json_encode(['numberOfTrainings' => $numberOfTrainings, 'message' => $message, 'trainingFullName' => $trainingFullName ?? '', 'trainingId' => $trainingId ?? '']);
