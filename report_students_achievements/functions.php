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
 * This file is responsible for define all the functions for display and export reports
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_student_achievements
*/

//all require files
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->libdir . '/tablelib.php');

/**
 * Recursively scans the provided conditions and retrieves the corresponding dates based on conditions of the same level
 *
 * @param array $conditions : An array of objects representing conditions
 * @param int $studentId : The student ID
 *
 * @return array $dates : Contains the corresponding dates and conditions.
 */
function processConditions($conditions, $studentId) {
    $dates = [];

    foreach ($conditions as $condition) {
        if (isset($condition->type) && $condition->type === 'date' && isset($condition->d) && $condition->d === '>=') {
            $dates[] = [
                'date' => $condition->t,
                'conditions' => $conditions
            ];
        } elseif (isset($condition->c)) {
            $subDates = processConditions($condition->c, $studentId);
            $dates = array_merge($dates, $subDates);
        }
    }

    return $dates;
}

/**
 * Allows to obtain the corresponding date according to the conditions of the same level
 *
 * @param array $dates : An array with dates and conditions associated (same level on the JSON file)
 * @param int $studentId : The student ID
 *
 * @return string The first corresponding date or '-' if no conditions are met.
 */
function getMatchingDate($dates, $studentId) {
    foreach ($dates as $date) {

        $matchingCondition = null;

        foreach ($date['conditions'] as $condition) {

            if ($condition->type !== 'date') {
                if (evaluateCondition($condition, $studentId)) {
                    $matchingCondition = $condition;

                    //the first valid condition containing a date
                    break;
                }
            }
        }

        if ($matchingCondition !== null) {
	        //the date corresponding to the validated condition
          return date('d/m/Y', $date['date']);
        }
    }

    //no date to display on the html_table object
    return '-';
}

/**
 * Checks if the profile condition is met for a given student.
 *
 * @param int $studentId : The student ID
 * @param string $profileField : The profile field
 * @param string $operator : The comparison operator to use (here, it is by default equalto)
 * @param string $value : The value of the field to be tested
 *
 * @return boolean True if the condition is met, false otherwise.
 */
function checkProfileCondition($studentId, $profileField, $operator, $value) {

    global $DB;

    $sqlTest = "SELECT uif.*
    FROM {user_info_field} uif
    WHERE uif.shortname = :shortname;";

    $sqlTestParams = ['shortname' => $profileField];

    $test = $DB->get_records_sql($sqlTest, $sqlTestParams);

    //see if the profile field is in the user table or user_infos_xx tables
    $resultTest = (count($test) > 0) ? true : false;

    if ($resultTest) { //user_infos_xx tables

        $sqlUserInfos = "SELECT uid.*
        FROM {user_info_data} uid
        JOIN {user_info_field} uif ON uif.id = uid.fieldid
        WHERE uif.shortname = :shortname
        AND uid.userid = :userid
        AND uid.data = :data;";

        $sqlUserInfosParams = ['shortname' => $profileField, 'userid' => $studentId, 'data' => $value];

        $userInfos = $DB->get_records_sql($sqlUserInfos, $sqlUserInfosParams);

        //see if there is a match between the profile field and the student (at least one record)
        $result = (count($userInfos) > 0) ? true : false;
    }

    else { //user table

        $sqlUser = " SELECT u.*
        FROM {user} u
        WHERE u.id = :id
        AND u." . $profileField . " = :value;";

        $sqlUserParams = ['id' => $studentId, 'value' => $value];

        $user = $DB->get_records_sql($sqlUser, $sqlUserParams);

        //see if there is a match between the profile field and the student (at least one record)
        $result = (count($user) > 0) ? true : false;

    }

    return $result;
}

/**
 * Checks if the group condition is met for a given student.
 *
 * @param int $studentId : The student ID
 * @param int $groupId : The group field
 *
 * @return boolean True if the condition is met, false otherwise.
 */
function checkGroupCondition($studentId, $groupId) {

    global $DB;

    $sql = "SELECT gm.*
    FROM {groups_members} gm
    WHERE gm.groupid = :groupid
    AND gm.userid = :userid;";

    $sqlParams = ['groupid' => $groupId, 'userid' => $studentId];

    $resultGroup = $DB->get_records_sql($sql, $sqlParams);

    //see if there is a match between the group and the student (at least one record)
    $result = (count($resultGroup) > 0) ? true : false;

    return $result;
}

/**
 * Checks if the individual condition is met for a given student, using the functions checkProfileCondition and checkGroupCondition
 *
 * @param object $condition : The condition
 * @param int $studentId : The student ID
 *
 * @return boolean True if the condition is met, false otherwise.
 */
function evaluateCondition($condition, $studentId) {

    if (isset($condition->op) && isset($condition->c)) {
        $operator = $condition->op;
        $subConditions = $condition->c;

        if ($operator === '|') {

            foreach ($subConditions as $subCondition) {
                if (evaluateCondition($subCondition, $studentId)) {
                    //at least one sub condition is satisfied ('|' operator)
                    return true;
                }
            }
            return false; //no sub conditions are met

        } elseif ($operator === '&') {

            foreach ($subConditions as $subCondition) {
                if (!evaluateCondition($subCondition, $studentId)) {
                    //a sub condition is not met ('&' operator)
                    return false;
                }
            }
            return true; //all the sub conditions are met
        }
    }

    //checks of individual conditions
    if (isset($condition->type)) {
        switch ($condition->type) {

            case 'profile':
                $profileField = '';

                //---------------------------- cf or sf
                if (isset($condition->cf)) {
                    $profileField = $condition->cf;
                } elseif (isset($condition->sf)) {
                    $profileField = $condition->sf;
                }
                //---------------------------

                $operator = isset($condition->op) ? $condition->op : '';
                $value = isset($condition->v) ? $condition->v : '';
                return checkProfileCondition($studentId, $profileField, $operator, $value);

            case 'group':
                $groupId = isset($condition->id) ? $condition->id : '';
                return checkGroupCondition($studentId, $groupId);

            case 'date': //do nothing

            default:
                //type of condition not yet considered
                return true;
        }
    }
    //if no types
    return true;
}

/**
 * Checks whether an activity can be added, according to its conditions
 * @param object $activity : The activity to check
 * @param int $studentId : The student ID
 *
 * @return boolean True if the conditions are met, false otherwise.
 */
function checkConditions($activity, $studentId) {

    if ($activity->availability == NULL) {
        //no conditions
        return true;
    }

    $availability = json_decode($activity->availability);

    if (isset($availability->op) && isset($availability->c) && count($availability->c) != 0) {
        $operator = $availability->op;
        $conditions = $availability->c;

        if ($operator === '|') {

            foreach ($conditions as $condition) {
                if (evaluateCondition($condition, $studentId)) {
                    //at least one sub condition is satisfied ('|' operator)
                    return true;
                }
            }
            //no sub conditions are met
            return false;

        } elseif ($operator === '&') {

            foreach ($conditions as $condition) {
                if (!evaluateCondition($condition, $studentId)) {
                    //a sub condition is not met ('&' operator)
                    return false;
                }
            }
            //all the sub conditions are met
        return true;
        }
    }

    //add the activity if its conditions JSON is empty or does not specify a logical operator
    return true;
    
}

/**
 * Retrieve the activities of a course for a given student.
 *
 * @param int $courseId
 * @param int $studentId
 * @return array
 */
function get_course_activities($courseId, $studentId, $export) {
    global $DB, $CFG;

    $sqlTypeOfModules = "SELECT DISTINCT m.*
    FROM {modules} m
    JOIN {course_modules} cm ON m.id = cm.module
    JOIN {course} c ON cm.course = c.id
    WHERE c.id = :id
    AND cm.completion > 0;";

    $paramsTypeOfModules = ['id' => $courseId];
    $typesOfModules = $DB->get_records_sql($sqlTypeOfModules, $paramsTypeOfModules);

    $courseActivities = [];

    foreach ($typesOfModules as $typeOfModule) {
      $name = $typeOfModule->name;
      $idModule = $typeOfModule->id;

      $sqlDetailsModules = "SELECT cm.id AS course_modules_id, t.name AS name
      FROM {course_modules} AS cm
      JOIN {". $name ."} AS t ON cm.instance = t.id
      WHERE cm.course = :course
      AND cm.module = :module;";

      $paramsDetailsModules = array('course' => $courseId, 'module' => $idModule);
      $detailsModules = $DB->get_records_sql($sqlDetailsModules, $paramsDetailsModules);

      foreach ($detailsModules as $detailsModule) {
        $idCourse_module = $detailsModule->course_modules_id;
        $activityName = $detailsModule->name;

        $sqlActivities = "SELECT cmc.completionstate, cm.availability, cm.id
        FROM {course_modules} AS cm
        LEFT JOIN {course_modules_completion} AS cmc ON cm.id = cmc.coursemoduleid AND cmc.userid = :userid
        WHERE cm.id = :id
        AND cm.completion > 0;";

        $paramsActivities = array('userid' => $studentId, 'id' => $idCourse_module);
        $activities = $DB->get_records_sql($sqlActivities, $paramsActivities);

        foreach ($activities as $activity) {
          $activityId = $activity->id;
          $date = '-';

          if (isset($activity->availability)) {
            $availability = json_decode($activity->availability);

            if (isset($availability->c) && count($availability->c) > 0) {
              $dates = processConditions($availability->c, $studentId);

              if (count($dates) === 1) {
                $date = date('d/m/Y', $dates[0]['date']);
              } elseif (count($dates) > 1) {
                $date = getMatchingDate($dates, $studentId);
              }
            }
          }

          if (checkConditions($activity, $studentId)) {

            if($export) {
              $completionStatus = ($activity->completionstate == 0) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements');
            }
            else {
              $completionStatus = ($activity->completionstate == 0) ? false : true;
            }
            $activityUrl = $CFG->wwwroot.'/mod/'.$name.'/view.php?id='.$activityId;
            $courseActivities[] = array(
              'name' => $activityName,
              'link' => $activityUrl,
              'type' => get_string('modulename', $name),
              'completion' => $completionStatus,
              'date' => $date
            );
          }
        }
      }
    }

    return $courseActivities;
}

/**
 * Retrieve the courses for a given module and student.
 *
 * @param int $trainingId
 * @param int $moduleId
 * @param int $studentId
 * @return array
 */
function getCourses($trainingId, $moduleId, $studentId) {
  global $DB;

  $courses = $DB->get_records('local_training_architecture_lu_to_lu', 
  ['trainingid' => $trainingId, 'isluid2course' => 'true', 'luid1' => $moduleId]);

  $coursesArray = [];

  foreach ($courses as $course) {
    $courseId = $course->luid2;
    $courseName = $DB->get_field('course', 'fullname', ['id' => $courseId]);

    $activitiesArray = get_course_activities($courseId, $studentId, false);

    $coursesArray[] = array(
      'courseName' => $courseName,
      'activities' => $activitiesArray
    );
  }
  return $coursesArray;
}
  
/**
 * Retrieve the modules for a given block or training.
 *
 * @param int|null $blockId
 * @param int $trainingId
 * @param int $studentId
 * @param bool $export
 * @return array
 */
function getModules($blockId, $trainingId, $studentId, $export) {
  global $DB;

  // Define SQL and parameters based on the presence of blockId
  if ($blockId) {
    $sql = "SELECT DISTINCT luid2 as moduleId
    FROM {local_training_architecture_lu_to_lu}
    WHERE trainingid = :trainingid AND 
    isluid2course = :isluid2course AND 
    luid1 = :luid";

    $params = [
      'trainingid' => $trainingId,
      'isluid2course' => 'false',
      'luid' => $blockId
    ];
  } else {
      $sql = "SELECT DISTINCT luid1 as moduleId
      FROM {local_training_architecture_lu_to_lu}
      WHERE trainingid = :trainingid AND 
      isluid2course = :isluid2course";

      $params = [
        'trainingid' => $trainingId,
        'isluid2course' => 'true'
      ];
  }
  
  // Fetch modules
  $modules = $DB->get_records_sql($sql, $params);
  $modulesArray = [];

  foreach ($modules as $module) {

    $moduleId = $module->moduleid;
    $moduleName = $DB->get_field('local_training_architecture_lu', 'fullname', ['id' => $moduleId]);

    $coursesArray = getCourses($trainingId, $moduleId, $studentId);

    if($export) {

      $studentFirstName = $DB->get_field('user', 'firstname', ['id' => $studentId]);
      $studentLastName = $DB->get_field('user', 'lastname', ['id' => $studentId]);

      $modulesArray[] = array(
        'studentFirstName' => $studentFirstName,
        'studentLastName' => $studentLastName,
        'moduleName' => $moduleName,
        'courses' => $coursesArray
      );
    }
    else {
      $modulesArray[] = array(
        'moduleName' => $moduleName,
        'courses' => $coursesArray
      );
    }
  }
  return $modulesArray;
}
  
/**
 * Retrieve the blocks for a given training.
 *
 * @param int $trainingId
 * @param int $studentId
 * @param bool $export
 * @param string $studentFirstName 
 * @param string $studentLastName
 * @return array
 */
function getBlocks($trainingId, $studentId, $export, $studentFirstName = '', $studentLastName = '') {
  global $DB;

  // Avoid duplication
  $sql = "SELECT DISTINCT luid1
  FROM {local_training_architecture_lu_to_lu}
  WHERE trainingid = :trainingid AND isluid2course = :isluid2course";

  $params = [
    'trainingid' => $trainingId,
    'isluid2course' => 'false'
  ];

  $blocks = $DB->get_records_sql($sql, $params);

  $result = [];

  foreach ($blocks as $block) {
    $blockId = $block->luid1;
    $blockName = $DB->get_field('local_training_architecture_lu', 'fullname', ['id' => $blockId]);

    $modulesArray = getModules($blockId, $trainingId, $studentId, $export);

    $result[] = [
      'studentFirstName' => $studentFirstName,
      'studentLastName' => $studentLastName,
      'blockName' => $blockName,
      'modules' => $modulesArray   
    ];
  }
  return $result;
}

/**
 * Retrieve the columns for export based on the number of levels and training ID.
 *
 * @param int $numberOfLevels
 * @param int $trainingId
 * @return array
 */
function exportGetColumns($numberOfLevels, $trainingId) {
  global $DB;

  $level1Id = $DB->get_field('local_training_architecture_level_names_to_training', 'levelnamesid', ['trainingid' => $trainingId, 'level' => '1']);
  $lu1Name = $DB->get_field('local_training_architecture_level_names', 'fullname', ['id' => $level1Id]);

  $level2Id = $DB->get_field('local_training_architecture_level_names_to_training', 'levelnamesid', ['trainingid' => $trainingId, 'level' => '2']);
  $lu2Name = $DB->get_field('local_training_architecture_level_names', 'fullname', ['id' => $level2Id]);

  if($lu1Name == 0) {
    $lu1Name = get_string('lu1', 'report_students_achievements');
  }
  if($lu2Name == 0) {
    $lu2Name = get_string('lu2', 'report_students_achievements');
  }

  // Define headers of the column's file
  $columns = [
    get_string('firstname', 'report_students_achievements'),
    get_string('lastname', 'report_students_achievements')
  ];

  if ($numberOfLevels == '2') {
    $columns[] = $lu1Name;
    $columns[] = $lu2Name;
  } else { // 1 level
    $columns[] = $lu1Name;
  }

  $columns[] = get_string('course');
  $columns[] = get_string('activity', 'report_students_achievements');
  $columns[] = get_string('type', 'report_students_achievements');
  $columns[] = get_string('completion', 'report_students_achievements');
  $columns[] = get_string('opening_date', 'report_students_achievements');

  return $columns;
}

/**
 * Retrieve data for export based on cohort ID, training ID, number of levels, and result.
 *
 * @param int $cohortId
 * @param int $trainingId
 * @param int $numberOfLevels
 * @param array $result
 * @param string $exportMode
 * @return array
 */
function exportGetData($cohortId, $trainingId, $numberOfLevels, $result, $exportMode) {
  global $DB;

  //$coursesNotInArchitecture = $DB->get_records('local_training_architecture_courses_not_architecture', ['trainingid' => $trainingId]);

  // Get cohort and training name for the file name
  $cohortName = $DB->get_field('cohort', 'name', array('id' => $cohortId));
  $trainingName = $DB->get_field('local_training_architecture_training', 'fullname', ['id' => $trainingId]);
  $extractionDate = date('Y-m-d', time());

  $data = [];

  // Tab the table to use in the API function
  if($numberOfLevels == '2') {

    foreach ($result as $block) {
      $studentFirstName = $block['studentFirstName'];
      $studentLastName = $block['studentLastName'];
      $blockName = isset($block['blockName']) ? $block['blockName'] : null;
      $modules = isset($block['modules']) ? $block['modules'] : null;

      foreach ($modules as $module) {
        $moduleName = $module['moduleName'];
        $courses = isset($module['courses']) ? $module['courses'] : null;

        foreach ($courses as $course) {
          $courseName = $course['courseName'];
          $activities = isset($course['activities']) ? $course['activities'] : null;

          foreach ($activities as $activity) {
            $activityName = $activity['name'];
            $type = $activity['type'];
            $completion = ($exportMode == 'student') ? (($activity['completion'] == false) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements')) : $activity['completion'];
            $date = $activity['date'];

            $data[] = [
              $studentFirstName,
              $studentLastName,
              $blockName,
              $moduleName,
              $courseName,
              $activityName,
              $type,
              $completion,
              $date
            ];
          }
        }
      }

      // Courses outside architecture
      if(!$blockName && !$modules) {
        $activitiesNotArchitecture = isset($block['activities']) ? $block['activities'] : null;
        $courseNameNotArchitecture = $block['courseName'];

        foreach ($activitiesNotArchitecture as $activityNotArchitecture) {
          $activityNameNotArchitecture = $activityNotArchitecture['name'];
          $typeNotArchitecture = $activityNotArchitecture['type'];
          $completionNotArchitecture = ($exportMode == 'student') ? (($activityNotArchitecture['completion'] == false) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements')) : $activityNotArchitecture['completion'];
          $dateNotArchitecture = $activityNotArchitecture['date'];

          $data[] = [
            $studentFirstName,
            $studentLastName,
            null,
            null,
            $courseNameNotArchitecture,
            $activityNameNotArchitecture,
            $typeNotArchitecture,
            $completionNotArchitecture,
            $dateNotArchitecture
          ];
        }
      } 
    }
  }

  else {

    foreach ($result as $module) {
      $studentFirstName = $module['studentFirstName'];
      $studentLastName = $module['studentLastName'];
      $moduleName = $module['moduleName'];
      $courses = isset($module['courses']) ? $module['courses'] : null;

      foreach ($courses as $course) {
        $courseName = $course['courseName'];              
        $activities = isset($course['activities']) ? $course['activities'] : null;

        foreach ($activities as $activity) {
          $activityName = $activity['name'];
          $type = $activity['type'];
          $completion = ($exportMode == 'student') ? (($activity['completion'] == false) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements')) : $activity['completion'];                 
          $date = $activity['date'];

          $data[] = [
            $studentFirstName,
            $studentLastName,
            $moduleName,
            $courseName,
            $activityName,
            $type,
            $completion,
            $date
          ];
        }
      }

      // Courses outside architecture
      if(!$moduleName) {
        $activitiesNotArchitecture = isset($module['activities']) ? $module['activities'] : null;
        $courseNameNotArchitecture = $module['courseName'];

        foreach ($activitiesNotArchitecture as $activityNotArchitecture) {
          $activityNameNotArchitecture = $activityNotArchitecture['name'];
          $typeNotArchitecture = $activityNotArchitecture['type'];
          $completionNotArchitecture = ($exportMode == 'student') ? (($activityNotArchitecture['completion'] == false) ? get_string('uncompleted', 'report_students_achievements') : get_string('completed', 'report_students_achievements')) : $activityNotArchitecture['completion'];
          $dateNotArchitecture = $activityNotArchitecture['date'];

          $data[] = [
            $studentFirstName,
            $studentLastName,
            null,
            $courseNameNotArchitecture,
            $activityNameNotArchitecture,
            $typeNotArchitecture,
            $completionNotArchitecture,
            $dateNotArchitecture
          ];
        }
      } 

    }
  }

  $result = [
    'data' => $data,
    'cohortName' => $cohortName,
    'trainingName' => $trainingName,
    'extractionDate' => $extractionDate,
  ];

  return $result;
}

function exportAddCoursesNotInArchitecture($result, $trainingId, $studentId, $studentFirstName, $studentLastName, $numberOfLevels, $exportCohort) {
  global $DB;

  // Courses not in architecture
  $coursesNotInArchitecture = $DB->get_records('local_training_architecture_courses_not_architecture', ['trainingid' => $trainingId]);
    
  foreach ($coursesNotInArchitecture as $courseNotInArchitecture) {    
    $courseId = $courseNotInArchitecture->courseid;
    $courseName = $DB->get_field('course', 'fullname', ['id' => $courseId]);
    $activitiesArray = get_course_activities($courseId, $studentId, $exportCohort);

    if ($numberOfLevels == '2') {
      $result[] = [
        'studentFirstName' => $studentFirstName,
        'studentLastName' => $studentLastName,
        'blockName' => null,
        'modules' => null,
        'courseName' => $courseName,
        'activities' => $activitiesArray
      ];
    }
    else {
      $result[] = [
        'studentFirstName' => $studentFirstName,
        'studentLastName' => $studentLastName,
        'modules' => null,
        'courseName' => $courseName,
        'activities' => $activitiesArray
      ];
    }
  }

  return $result;
}
