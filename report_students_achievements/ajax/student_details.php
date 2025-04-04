<?php

/* This file retrieves the informations of a given student*/

// Moodle config file
require_once(dirname(__FILE__) . '/../../../config.php');

// Get the values
$studentId = $_POST['studentId'];
$trainingId = $_POST['trainingId'];

// Get informations from the DB
$studentFirstName = $DB->get_field('user', 'firstname', ['id' => $studentId]);
$studentLastName = $DB->get_field('user', 'lastname', ['id' => $studentId]);
$trainingFullName = $DB->get_field('local_training_architecture_training', 'fullname', ['id' => $trainingId]);

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['firstName' => $studentFirstName, 
'lastName' => $studentLastName, 
'trainingFullName' => $trainingFullName]);
