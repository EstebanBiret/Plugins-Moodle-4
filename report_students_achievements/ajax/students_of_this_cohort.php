<?php

/* This file retrieves the data to be entered in the html_table (lastname, firstname and link for each student in the selected cohort */

// Moodle files
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/tablelib.php');

// Get the values
$cohortId = $_POST['cohortId'];
$trainingId = $_POST['trainingId'];

//store these variables in GLOBAL variables
global $SESSION;
$SESSION->trainingId = $trainingId;
$SESSION->cohortId = $cohortId;

// SQL
$sqlStudents = "SELECT DISTINCT u.*
	FROM {user} u
	INNER JOIN {role_assignments} ra ON (ra.userid = u.id)
	INNER JOIN {cohort_members} cm ON (cm.userid = u.id)
	WHERE ra.roleid = :roleid
	AND cm.cohortid = :cohortid
	ORDER BY u.lastname ASC;";

// Array of parameters (5 corresponds to the student role)
$params = array('roleid' => 5, 'cohortid' => $cohortId);

$students = $DB->get_records_sql($sqlStudents, $params);
$result = [];

// To display the number of students in the cohort
$count_students = count($students);

// Display students in the html_table (defined in index.php) if found
if (!empty($students)) {

    $message1 = get_string('students_found', 'report_students_achievements');

    foreach ($students as $student) {

        $row = [
            $student->firstname,
            $student->lastname,
            $student->id,
            html_writer::tag('span', get_string('viewdetails', 'report_students_achievements'), ['class' => 'student-details', 'cohortId' => $cohortId, 'trainingId' => $trainingId, 'studentId' => $student->id])
        ];
            
        $result[] = $row;
    }

    $trainingFullName = $DB->get_field('local_training_architecture_training', 'fullname', ['id' => $trainingId]);

} else {
    // Handle case when no students found
    $message2 = get_string('students_not_found', 'report_students_achievements');
}

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode(['students' => $result, 'number' => $count_students, 'message1' => $message1 ?? '', 
'message2' => $message2 ?? '', 'trainingFullName' => $trainingFullName ?? '', 
'messageSelectStudent' => get_string('select_student', 'report_students_achievements')]);