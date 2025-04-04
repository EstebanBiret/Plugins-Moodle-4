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
 * @package   local_training_architecture
 */

defined('MOODLE_INTERNAL') || die();

// Common.
$string['pluginname'] = 'Training Architecture';
$string['training_architecture:manage'] = 'Edit and manage trainings architecture';
$string['heading'] = 'Manage training architecture';
$string['title'] = 'Training\'s architecture';
$string['fullName'] = 'Fullname';
$string['shortName'] = 'Shortname';
$string['IDNumber'] = 'Identification number';
$string['description'] = 'Description';
$string['numberOfLevel'] = 'Number of granularity\'s level';
$string['level'] = 'Granularity\'s level';
$string['level1'] = 'Granularity of level 1\'s name';
$string['level2'] = 'Granularity of level 2\'s name';
$string['confirmYes'] = 'Confirm';
$string['confirmNo'] = ' Go back';
$string['actions'] = 'Actions';
$string['selection'] = 'Selection';
$string['deleteSelection'] = 'Delete the selected elements';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['back'] = 'Back';
$string['order'] = 'Order';
$string['up'] = 'Up';
$string['down'] = 'Down';
$string['lu1'] = 'Learning Unit 1';
$string['lu2'] = 'Learning Unit 2';
$string['chooseOption'] = 'Choose an option';
$string['course'] = 'Course';
$string['lu'] = 'Learning Unit';
$string['allForms'] = 'All forms : ';
$string['deleteMultipleTitle1'] = 'Delete ';
$string['expand'] = 'Expand all';
$string['collapse'] = 'Collapse all';

// Training.
$string['createTraining'] = 'New Training';
$string['trainingLevel'] = 'Granularity of level ';
$string['editTrainingTitle'] = 'Edit the training';
$string['deleteTrainingTitle'] = 'Delete the training';
$string['selectNumberOfLevel'] = 'Number of granularity\s level';
$string['semesterChoice'] = 'Semester architecture';

// Level name.
$string['createLevelTitle'] = 'New granularity\'s level';
$string['editLevelTitle'] = 'Edit the level name';
$string['deleteLevelTitle'] = 'Delete the level name';

// Training to level name.
$string['trainingToLevel'] = 'Association Traininig - Granularities levels names';
$string['deleteTrainingLevelTitle'] = 'Delete this Training - Granularity level name association';

// Cohort to training.
$string['cohortToTraining'] = 'Association Training - Cohort';
$string['cohort'] = 'Cohort';
$string['training'] = 'Training';
$string['deleteCohortTrainingTitle'] = 'Delete this Cohort Training - Cohort association';

// LU.
$string['createLuTitle'] = 'New Learning Unit';
$string['editLuTitle'] = 'Edit the Learning Unit';
$string['deleteLuTitle'] = 'Delete the Learning Unit';

// Not in architecture. 
$string['coursesNotInArchitectureTitle'] = 'Association of courses outside architecture';
$string['deleteNotArchitecture'] = 'Delete course - training association';
$string['deleteMultipleCoursesNotInArchitectureTitle2'] = ' course - training association(s)';

// Training links.
$string['trainingLinks'] = 'Association of training characteristics';
$string['semester'] = 'Semester';
$string['selectSemester'] = 'Select a semester';
$string['deleteTrainingLinks'] = 'Delete training characteristics';
$string['deleteMultipleTrainingLinksTitle2'] = ' link(s) of training characteristics';

// LU to LU.
$string['luToLu'] = 'Association of Learning Units';
$string['luLevel'] = 'Learning Unit of level ';
$string['deleteLuLuTitle'] = 'Delete Association of Learning Units';
$string['deleteMultipleLuLuTitle2'] = ' association(s) of Learning Units';

// Sort order LU.
$string['sortLuTitle'] = 'Manage Learning Units order';
$string['sortLu'] = 'Manage the Learning Units order of ';
$string['noLus'] = 'This training has no Learning Units yet.';
$string['orderInformations'] = 'This page is used to order the Learning Units (LU) of a training. 
Click on a level 1 LU to order its level 2 LU. 
(This will only have an impact on the display order of the LU of a training linked to the associated block plugin (block_training_architecture)).';

// Help.
$string['trainingLevel'] = 'Level of granularity ';
$string['trainingLevel_help'] = 'The level of granularity corresponds to a level of segmentation of the training. 
Trainings generally offer 2 or 3 levels of granularity. 
The last level of the Learning Units here corresponds to the Moodle course.

Possible examples: 

Teaching Unit / Module / Course 
Competency Block / Module / Course
Training Unit / Module / Course
Module / Chapter / Course 
...

It is not possible to have two levels of granularity with the same name.';

$string['fullName_help'] = 'Correspond to the name of the chosen level of granularity.
This may refer to a segmentation corresponding to a content-subject approach or a competency-based approach.
This name may be displayed in certain areas of the site depending on associated dependent plugins.

This name must be unique.';

$string['shortName_help'] = 'This name is an abbreviation of the granularity level name.
This abbreviated name may be displayed in certain areas of the site depending on associated dependent plugins.

This name must be unique.';

$string['IDNumber_help'] = 'Reference of the entity created within the information system.
This allows referencing and linking the entity with the rest of the information system.

This number must be unique.';

$string['granularityLevel_help'] = 'Select here the granularity level to which you want to associate the Learning Unit for a given training.
You must also associate each course in your training plan with the correct granularity level.
Courses correspond to the last granularity level of your Learning Unit (excluding activities/resources) of your training plan.

Example:
If your training has 1 level of granularity, the course will be at level 2,
If your training has 2 levels of granularity, the course will be at level 3.';

$string['createTrainingGranularityLevel_help'] = 'Choose the number of granularity levels above the course (do not count the course).

Example:
A training plan with 1 level : Module > Course.
A training plan with 2 levels : Block > Module > Course.';

// Errors.
$string['nameAlreadyExists'] = 'This name is already taken.';
$string['shortNameAlreadyExists'] = 'This shortname is already taken.';
$string['IDNumberAlreadyExists'] = 'This identification number is already taken.';
$string['selectDifferentLevel'] = 'This level has already been selected.';
$string['associationAlreadyExists'] = 'This association already exists.';
$string['trainingLevelAlreadyExists'] = 'This training is already linked wiht granularity\'s levels.';
$string['associationAlreadyExistsCohorts'] = 'These cohorts are already associated with this training';
$string['courseAlreadyInArchitecture'] = 'This course is already in the architecture for this training.';
$string['courseAlreadyNotInArchitecture'] = 'This course has already been associated outside of the architecture for this training.';
$string['luDuplicate'] = 'This Learning Unit has already been chosen.';
$string['luNotRelated'] = 'This Learning Unit is not linked to this training.';
$string['courseNotRelated'] = 'This course is not linked to this training.';
$string['levelTooHigh'] = 'Granularity level is higher than the maximum granularity level for this training.';
$string['errorEditSemester'] = 'You cannot edit this field because there are references to the semesters in other tables.';
$string['errorEditLevel'] = 'You cannot edit this field because there are references to the levels in other tables.';
$string['lu1AlreadyAsLu2'] = 'This level 1 Learning Unit is already involved in another relationship as level 2, for this training';
$string['lu2AlreadyAsLu1'] = 'This level 2 Learning Unit is already involved in another relationship as level 1, for this training';

// Warnings.
$string['deleteLinkWarning'] = 'Are you sure you want to remove this association ?';
$string['deleteMultipleWarning'] = 'Are you sure you want to remove this/these association(s) ?';
$string['deleteLuWarning'] = 'Are you sure you want to remove this Learning Unit ?';
$string['deleteLevelNameWarning'] = 'Are you sure you want to remove this level name ?';
$string['deleteTrainingWarning'] = 'Are you sure you want to remove this training ? This will result in the deletion of all data related to this training.';

// Notify errors.
$string['notifyErrorLuToLu'] = 'You cannot delete this association, one of the Learning Units is used in one or more other relationships.';
$string['notifyErrorMultipleLuToLu'] = 'You cannot delete this/these association(s), one of the Learning Units is used in one or more other relationships.';
$string['notifyErrorLevel'] = 'You cannot delete this level name, it is associated with one or more training.';
$string['notifyErrorLu'] = 'You cannot delete this Learning Unit, it is used in one or more other relationships.';
$string['notifyErrorMultipleTrainingLinks'] = 'You cannot delete this/these association(s), one of the Learning Units is involved in one or more relationships for this training in the "Learning Unit Associations" form .';

// Tasks.
$string['cohortTask'] = 'Synchronize cohort_to_training data based on existing cohorts';
$string['courseTask'] = 'Synchronize lu_to_course data based on existing courses';