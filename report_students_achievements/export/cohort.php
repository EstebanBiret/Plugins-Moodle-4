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
 * This file is responsible for producing the downloadable versions of student achievements (by cohort)
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_student_achievements
*/

//require moodle files
require_once ("../../../config.php");
require_once($CFG->dirroot . '/cohort/lib.php');
require_once(dirname(__FILE__) . '/../functions.php');

//get the cohort id from the GLOBAL variable (set in ajax files)
global $SESSION;
$cohortId = $SESSION->cohortId;
$trainingId = $SESSION->trainingId;

//all the cohort's students
$sqlusers = "SELECT DISTINCT u.*
        FROM {user} u
        INNER JOIN {role_assignments} ra ON (ra.userid = u.id)
        INNER JOIN {cohort_members} cm ON (cm.userid = u.id)
        WHERE ra.roleid = :roleid
        AND cm.cohortid = :cohortid
        ORDER BY u.lastname ASC;";
$params = ['roleid' => 5, 'cohortid' => $cohortId]; //array of parameters
$users = $DB->get_records_sql($sqlusers, $params);

// Main logic
$numberOfLevels = $DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $trainingId]);
$result = [];

// Process students
foreach ($users as $user) {

    $studentId = $user->id;
    $studentFirstName = $user->firstname;
    $studentLastName = $user->lastname;

    if($numberOfLevels == '2') {

        // Construction de la requête SQL
        $sql = "SELECT DISTINCT luid1
        FROM {local_training_architecture_lu_to_lu}
        WHERE trainingid = :trainingid AND isluid2course = :isluid2course";

        // Paramètres de la requête
        $params = [
            'trainingid' => $trainingId,
            'isluid2course' => 'false'
        ];

        // Exécution de la requête
        $blocks = $DB->get_records_sql($sql, $params);

        //browse all blocks
        foreach ($blocks as $block) {

            $blockId = $block->luid1;

            $blockName = $DB->get_field('local_training_architecture_lu', 'fullname', ['id' => $blockId]);

            // Construction de la requête SQL
            $sql = "SELECT DISTINCT luid2
                    FROM {local_training_architecture_lu_to_lu}
                    WHERE trainingid = :trainingid AND 
                    isluid2course = :isluid2course AND 
                    luid1 = :luid1";

            // Paramètres de la requête
            $params = [
                'trainingid' => $trainingId,
                'isluid2course' => 'false',
                'luid1' => $blockId
            ];

            // Exécution de la requête
            $modules = $DB->get_records_sql($sql, $params);

            //array for the modules of the block
            $modulesArray = [];

            // Avoid duplication of moduleId
            $seenModules = [];

            //browse all modules
            foreach ($modules as $module) {
                $moduleId = $module->luid2;

                $moduleName = $DB->get_field('local_training_architecture_lu', 'fullname', ['id' => $moduleId]);

                $courses = $DB->get_records('local_training_architecture_lu_to_lu', 
                ['trainingid' => $trainingId, 'isluid2course' => 'true', 'luid1' => $moduleId]);

                //array for the courses of the module
                $coursesArray = [];

                //browse all courses
                foreach ($courses as $course) {

                    $courseId = $course->luid2;
                    $courseName = $DB->get_field('course', 'fullname', ['id' => $courseId]);

                    $sqlTypeOfModules = "SELECT DISTINCT m.*
                    FROM {modules} m
                    JOIN {course_modules} cm ON m.id = cm.module
                    JOIN {course} c ON cm.course = c.id
                    WHERE c.id = :id;";

                    $paramsTypeOfModules = ['id' => $courseId];

                    $typesOfModules = $DB->get_records_sql($sqlTypeOfModules, $paramsTypeOfModules);

                    //array for the activities of the course
                    $activitiesArray = [];

                    //browse each type of modules in this course
                    foreach ($typesOfModules as $typeOfModule) {
                        $name = $typeOfModule->name; //get the activity type, to find out which table to look for at each new activity
                        $idModule = $typeOfModule->id;

                        $sqlDetailsModules = "SELECT cm.id AS course_modules_id, t.name AS name
                        FROM {course_modules} AS cm
                        JOIN {". $name ."} AS t ON cm.instance = t.id
                        WHERE cm.course = :course
                        AND cm.module = :module;";

                        $paramsDetailsModules =  ['course' => $courseId, 'module' => $idModule];

                        $detailsModules = $DB->get_records_sql($sqlDetailsModules, $paramsDetailsModules);

                        foreach ($detailsModules as $detailsModule) {

                            $idCourse_module = $detailsModule->course_modules_id;

                            $activityName = $detailsModule->name;

                            $sqlActivities = "SELECT cmc.completionstate, cm.availability, cm.id
                            FROM {course_modules} AS cm
                            LEFT JOIN {course_modules_completion} AS cmc ON cm.id = cmc.coursemoduleid AND cmc.userid = :userid
                            WHERE cm.id = :id
                            AND cm.completion > 0;";

                            $paramsActivities = ['userid' => $studentId, 'id' => $idCourse_module];

                            $activities = $DB->get_records_sql($sqlActivities, $paramsActivities);

                            //browse all activities of the course
                            foreach ($activities as $activity) {

                                $activityId = $activity->id;
                                $date = '-';

                                //managing the display of dates in the html_table
                                if (isset($activity->availability)) {

                                    $availability = json_decode($activity->availability);

                                    if (isset($availability->c) && count($availability->c) > 0) {

                                        $dates = processConditions($availability->c, $studentId);

                                        if (count($dates) === 1) {
                                            //if there is only one date, it is displayed
                                            $date = date('d/m/Y', $dates[0]['date']);

                                        } elseif (count($dates) > 1) {
                                            //otherwise, we look at which display according to access restrictions
                                            $date = getMatchingDate($dates, $studentId);
                                        }
                                    }
                                }
                                //-------------------------------------------------------------//

                                //check if the activity can be displayed (depending on access restrictions)
                                if (checkConditions($activity, $studentId)) {

                                    $completionStatus = ($activity->completionstate == 0) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements');

                                    //array of the activity
                                    $activitiesArray[] = [
                                        'name' => $activityName,
                                        'type' => get_string('modulename', $name),
                                        'completion' => $completionStatus,
                                        'date' => $date
                                    ];
                                }
                                //else, move on to the next activity
                            }
                        }
                    }

                    //add course name and array of activities
                    $coursesArray[] = [
                        'courseName' => $courseName,
                        'activities' => $activitiesArray
                    ];
                }

                //add module name and array of courses
                $modulesArray[] = [
                    'moduleName' => $moduleName,
                    'courses' => $coursesArray
                ];
            }

            //add the first and lastname of the student, and the blockname & array of modules
            $result[] = [
                'studentFirstName' => $studentFirstName,
                'studentLastName' => $studentLastName,
                'blockName' => $blockName,
                'modules' => $modulesArray
            ];
        }

    }
    else {
        // Construction de la requête SQL pour les modules
        $sql = "SELECT DISTINCT luid1 AS moduleid
        FROM {local_training_architecture_lu_to_lu}
        WHERE trainingid = :trainingid AND isluid2course = :isluid2course";

        // Paramètres de la requête
        $params = [
        'trainingid' => $trainingId,
        'isluid2course' => 'true'
        ];

        // Exécution de la requête
        $modules = $DB->get_records_sql($sql, $params);

        //array for the modules of the training
        $modulesArray = [];

        //browse all modules
        foreach ($modules as $module) {
            $moduleId = $module->moduleid;

            $moduleName = $DB->get_field('local_training_architecture_lu', 'fullname', ['id' => $moduleId]);

            $courses = $DB->get_records('local_training_architecture_lu_to_lu', 
            ['trainingid' => $trainingId, 'isluid2course' => 'true', 'luid1' => $moduleId]);

            //array for the courses of the module
            $coursesArray = [];

            //browse all courses
            foreach ($courses as $course) {

                $courseId = $course->luid2;
                $courseName = $DB->get_field('course', 'fullname', ['id' => $courseId]);

                $sqlTypeOfModules = "SELECT DISTINCT m.*
                FROM {modules} m
                JOIN {course_modules} cm ON m.id = cm.module
                JOIN {course} c ON cm.course = c.id
                WHERE c.id = :id;";

                $paramsTypeOfModules = ['id' => $courseId];

                $typesOfModules = $DB->get_records_sql($sqlTypeOfModules, $paramsTypeOfModules);

                //array for the activities of the course
                $activitiesArray = [];

                //browse each type of modules in this course
                foreach ($typesOfModules as $typeOfModule) {
                    $name = $typeOfModule->name; //get the activity type, to find out which table to look for at each new activity
                    $idModule = $typeOfModule->id;

                    $sqlDetailsModules = "SELECT cm.id AS course_modules_id, t.name AS name
                    FROM {course_modules} AS cm
                    JOIN {". $name ."} AS t ON cm.instance = t.id
                    WHERE cm.course = :course
                    AND cm.module = :module;";

                    $paramsDetailsModules =  ['course' => $courseId, 'module' => $idModule];

                    $detailsModules = $DB->get_records_sql($sqlDetailsModules, $paramsDetailsModules);

                    foreach ($detailsModules as $detailsModule) {

                        $idCourse_module = $detailsModule->course_modules_id;

                        $activityName = $detailsModule->name;

                        $sqlActivities = "SELECT cmc.completionstate, cm.availability, cm.id
                        FROM {course_modules} AS cm
                        LEFT JOIN {course_modules_completion} AS cmc ON cm.id = cmc.coursemoduleid AND cmc.userid = :userid
                        WHERE cm.id = :id
                        AND cm.completion > 0;";

                        $paramsActivities = ['userid' => $studentId, 'id' => $idCourse_module];

                        $activities = $DB->get_records_sql($sqlActivities, $paramsActivities);

                        //browse all activities of the course
                        foreach ($activities as $activity) {

                            $activityId = $activity->id;
                            $date = '-';

                            //managing the display of dates in the html_table
                            if (isset($activity->availability)) {

                                $availability = json_decode($activity->availability);

                                if (isset($availability->c) && count($availability->c) > 0) {

                                    $dates = processConditions($availability->c, $studentId);

                                    if (count($dates) === 1) {
                                        //if there is only one date, it is displayed
                                        $date = date('d/m/Y', $dates[0]['date']);

                                    } elseif (count($dates) > 1) {
                                        //otherwise, we look at which display according to access restrictions
                                        $date = getMatchingDate($dates, $studentId);
                                    }
                                }
                            }
                            //-------------------------------------------------------------//

                            //check if the activity can be displayed (depending on access restrictions)
                            if (checkConditions($activity, $studentId)) {

                                $completionStatus = ($activity->completionstate == 0) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements');

                                //array of the activity
                                $activitiesArray[] = [
                                    'name' => $activityName,
                                    'type' => get_string('modulename', $name),
                                    'completion' => $completionStatus,
                                    'date' => $date
                                ];
                            //else, move on to the next activity
                            }
                        }
                    }
                }
                //add course name and array of activities
                $coursesArray[] = [
                    'courseName' => $courseName,
                    'activities' => $activitiesArray
                ];
            }

            //add the first and last name of the student, and the array of modules
            $result[] = [
                'studentFirstName' => $studentFirstName,
                'studentLastName' => $studentLastName,
                'moduleName' => $moduleName,
                'courses' => $coursesArray
            ];
        }

    }

    $result = exportAddCoursesNotInArchitecture($result, $trainingId, $studentId, $studentFirstName, $studentLastName, $numberOfLevels, true);
}

//------DOWNLOAD PART------//

$columns = exportGetColumns($numberOfLevels, $trainingId);
$data = exportGetData($cohortId, $trainingId, $numberOfLevels, $result, 'cohort');

// Format of the file (csv, xlxs, ods, pdf, json)
$dataformat = optional_param('export', '', PARAM_ALPHA);

// Download file
\core\dataformat::download_data($data['cohortName'] . '_' . $data['trainingName'] . '_' . $data['extractionDate'], $dataformat, $columns, $data['data']); // lib/classes/dataformat.php