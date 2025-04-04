<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category  string
 * @package   local_bulk_actions_restrict_access
 */

defined('MOODLE_INTERNAL') || die();

// Common
$string['pluginname'] = 'Bulk actions access retrictions';
$string['bulk_actions_restrict_access:view'] = 'See bulk actions access restrictions';
$string['show_hide'] = 'Show/Hide';
$string['choose_select'] = 'Choose...';
$string['equal'] = 'Is equal to';
$string['section'] = 'Section ';

$string['start_date'] = 'From ';
$string['end_date'] = 'Until ';

$string['dates_found'] = ' dates found';
$string['date_found'] = ' date found';
$string['no_date'] = 'No dates found';

$string['type'] = 'Type';
$string['date'] = 'Date';

$string['singular'] = ' selected item';
$string['plural'] = ' selected items';
$string['no_selected_activities'] = 'No selected item';
$string['uncheck_everything'] = 'Uncheck everything';

$string['course_link'] = 'Access the course';

$string['singular_number_of_section'] = ' section, ';
$string['plural_number_of_sections'] = ' sections, ';
$string['singular_number_of_activity'] = ' activity/resource';
$string['plural_number_of_activities'] = ' activities/resources';

$string['checkAll'] = 'Check all ';

// All cohorts
$string['cohort'] = 'All cohorts';

// Trainings of cohort
$string['trainings_of_this_cohort'] = 'Trainings of this cohort';

// LU
$string['lu1_of_training'] = 'Level 1 LU of this training';
$string['lu2_of_lu1'] = 'Level 2 LU of this LU';

// Course
$string['courses'] = 'Courses of this LU';

// Profile field
$string['profile_field'] = 'Profile field';
$string['no_data_field'] = 'No data for this cohort and profile field.';

// Filter by
$string['filter_by'] = 'Filter by';
$string['user_profile'] = 'User profile';
$string['group'] = 'Group';
$string['no_data_type'] = 'No data for this type of condition.';

// Group
$string['no_group'] = 'No group for this course.';

// Errors
$string['no_trainings'] = 'This cohort is not linked to any training.';
$string['no_lu'] = 'This training have no LU.';
$string['no_lu2'] = 'This LU1 have no LU2.';
$string['no_courses'] = 'This LU have no courses.';
$string['no_activities'] = 'This course have no activity or resource.';

// Notification messages
$string['notif_changes_saved'] = 'Changes saved !';
$string['notif_same_date'] = 'The two dates cannot be the same !';
$string['notif_end_before_start'] = 'The start date cannot be greater than the end date !';