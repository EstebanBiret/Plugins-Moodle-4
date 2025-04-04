<?php

/* This file recovers the data of the courses not in architecture */

//require moodle files
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once(dirname(__FILE__) . '/../functions.php');

$result = [];

//get the cohortId AND the studentId
$value = $_POST['value'];
$ids = explode('/', $value);
$cohortId = $ids[0];
$trainingId = $ids[1];
$studentId = $ids[2];

//store these variables in GLOBAL variables
global $SESSION;
$SESSION->cohortId = $cohortId;
$SESSION->trainingId = $trainingId;
$SESSION->studentId = $studentId;

//courses not in architecture for this training
$courses = $DB->get_records('local_training_architecture_courses_not_architecture', ['trainingid' => $trainingId]);

//browse all courses
foreach ($courses as $course) {

    $courseId = $course->courseid;
    $courseName = $DB->get_field('course', 'fullname', ['id' => $courseId]);
    
    $courseActivities = get_course_activities($courseId, $studentId, false);

    //add course name and array of activities
    $result[] = array(
            'courseName' => $courseName,
            'activities' => $courseActivities
    );
}

//send result as JSON response
header('Content-Type: application/json');
echo json_encode(['courses' => $result, 
'completed' => get_string('completed', 'report_students_achievements'), 
'uncompleted' => get_string('uncompleted', 'report_students_achievements'), 
'header' => get_string('coursesOutsideOfArchitecture', 'report_students_achievements'), 
'notHeader' => get_string('noCoursesOutsideOfArchitecture', 'report_students_achievements'), 
'noActivities' => get_string('noActivities', 'report_students_achievements'), 
'activityName' => get_string('activityName', 'report_students_achievements'), 
'type' => get_string('type', 'report_students_achievements'), 
'completionStatus' => get_string('completionStatus', 'report_students_achievements'), 
'openingDate' => get_string('openingDate', 'report_students_achievements')]);