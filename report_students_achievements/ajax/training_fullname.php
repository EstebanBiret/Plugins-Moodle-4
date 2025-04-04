<?php 

/* This file allows you to know the fullname of a given training */

//require moodle file
require_once(dirname(__FILE__) . '/../../../config.php');

//get the trainingId
$trainingId = $_POST['trainingId'];

//store this variable in a GLOBAL variable (for export)
global $SESSION;
$SESSION->trainingId = $trainingId;

$trainingFullName = $DB->get_field('local_training_architecture_training', 'fullname', ['id' => $trainingId]);
 
//send result as JSON response
header('Content-Type: application/json');
echo json_encode($trainingFullName);
