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
 * Return the activities of selected course
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

// Require Moodle files
require_once(dirname(__FILE__) . '/../../../config.php');

// Get values
$courseId = $_POST['courseId'];

$sqlTypeOfModules = "SELECT DISTINCT m.*
FROM {modules} m
JOIN {course_modules} cm ON m.id = cm.module
JOIN {course} c ON cm.course = c.id
WHERE c.id = :id;";

$paramsTypeOfModules = ['id' => $courseId];
$typesOfModules = $DB->get_records_sql($sqlTypeOfModules, $paramsTypeOfModules);

$courseActivities = [];

// Retrieve the sequences for all sections
$sqlSections = "SELECT id, section, sequence
FROM {course_sections}
WHERE course = :course
ORDER BY section;";
$paramsSections = ['course' => $courseId];
$sections = $DB->get_records_sql($sqlSections, $paramsSections);

foreach ($sections as $section) {
    $sectionId = $section->id;
    $sectionName = $DB->get_field('course_sections', 'name', ['id' => $sectionId]);
    $sectionNumber = $section->section;
    if (!$sectionName) {
        $sectionName = get_string('section', 'local_bulk_actions_restrict_access') . $sectionNumber;
    }
    $sequence = explode(',', $section->sequence);
    $courseActivities[$sectionName] = ['sequence' => $sequence, 'activities' => []];
}

foreach ($typesOfModules as $typeOfModule) {
    $name = $typeOfModule->name;
    $moduleId = $typeOfModule->id;

    $sqlDetailsModules = "SELECT cm.id AS course_modules_id, t.name AS name, cm.section AS section_id
    FROM {course_modules} AS cm
    JOIN {". $name ."} AS t ON cm.instance = t.id
    WHERE cm.course = :course
    AND cm.module = :module;";
    
    $paramsDetailsModules = ['course' => $courseId, 'module' => $moduleId];
    $detailsModules = $DB->get_records_sql($sqlDetailsModules, $paramsDetailsModules);

    foreach ($detailsModules as $detailsModule) {
        $idCourseModule = $detailsModule->course_modules_id;
        $activityName = $detailsModule->name;
        $sectionId = $detailsModule->section_id;
        
        $sqlActivities = "SELECT cm.availability, cm.id, cm.section
        FROM {course_modules} AS cm
        WHERE cm.id = :id;";
        
        $paramsActivities = ['id' => $idCourseModule];
        $activities = $DB->get_records_sql($sqlActivities, $paramsActivities);
        
        foreach ($activities as $activity) {
            $activityId = $activity->id;
            $activityUrl = $CFG->wwwroot.'/mod/'.$name.'/view.php?id='.$activityId;
            $iconUrl = $CFG->wwwroot.'/mod/'.$name.'/pix/monologo.svg';
            $availability = $activity->availability;

            foreach ($courseActivities as $sectionName => &$sectionData) {
                if (in_array($activityId, $sectionData['sequence'])) {
                    $sectionData['activities'][$activityId] = [
                        'id' => $activityId,
                        'fullname' => $activityName,
                        'link' => $activityUrl,
                        'icon' => $iconUrl,
                        'type' => get_string('modulename', $name)
                    ];
                    break;
                }
            }
        }
    }
}

// Sort activities within each section by sequence order
foreach ($courseActivities as $sectionName => &$sectionData) {
    $sortedActivities = [];
    foreach ($sectionData['sequence'] as $activityId) {
        if (isset($sectionData['activities'][$activityId])) {
            $sortedActivities[] = $sectionData['activities'][$activityId];
        }
    }
    $sectionData['activities'] = $sortedActivities;
    unset($sectionData['sequence']); // Remove sequence after sorting
}

// Remove empty sections
$courseActivities = array_filter($courseActivities, function($sectionData) {
    return !empty($sectionData['activities']);
});

// Move section 0 to the beginning if it exists
$section0Name = get_string('section', 'local_bulk_actions_restrict_access') . '0';
if (isset($courseActivities[$section0Name])) {
    $section0 = $courseActivities[$section0Name];
    unset($courseActivities[$section0Name]);
    $courseActivities = array_merge([$section0Name => $section0], $courseActivities);
}

$error = get_string('no_activities', 'local_bulk_actions_restrict_access');
$show = $CFG->wwwroot.'/pix/t/add.svg'; 
$hide = $CFG->wwwroot.'/pix/t/less.svg'; 
$message = get_string('show_hide', 'local_bulk_actions_restrict_access');

$singular_section = get_string('singular_number_of_section', 'local_bulk_actions_restrict_access');
$plural_sections = get_string('plural_number_of_sections', 'local_bulk_actions_restrict_access');
$singular_activity = get_string('singular_number_of_activity', 'local_bulk_actions_restrict_access');
$plural_activities = get_string('plural_number_of_activities', 'local_bulk_actions_restrict_access');

// Send result as JSON response
header('Content-Type: application/json');
echo json_encode([
    'activities' => array_map(function($sectionData) {
        return $sectionData['activities'];
    }, $courseActivities),
    'error' => $error, 
    'show' => $show,
    'hide' => $hide,
    'show_hide_message' => $message,
    'singular_section' => $singular_section,
    'plural_sections' => $plural_sections,
    'singular_activity' => $singular_activity,
    'plural_activities' => $plural_activities
]);
