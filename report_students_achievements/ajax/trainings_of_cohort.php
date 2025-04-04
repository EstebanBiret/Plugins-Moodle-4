<?php

/* This file return the trainings of a specific cohort */

//require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

$cohortId = $_POST['cohortId'];

//get all the trainings of this cohort
$trainingRecords = $DB->get_records('local_training_architecture_cohort_to_training', ['cohortid' => $cohortId]);

$trainings = [];

//for all student cohorts, the cohort id and its name are stored
foreach ($trainingRecords as $trainingRecord) {
    $trainings[] = [
        'id' => $trainingRecord->trainingid,
        'fullname' => $DB->get_field('local_training_architecture_training', 'fullname', 
        ['id' => $trainingRecord->trainingid])
    ];
}

$message = get_string('select_training', 'report_students_achievements');

//send result as JSON response
header('Content-Type: application/json');
echo json_encode(['trainings' => $trainings, 'message' => $message]);