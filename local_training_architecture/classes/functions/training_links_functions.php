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
 * Training Links Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class training_links_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates a link between a training and another entity (either a course or a LU).
     *
     * @param array $data An associative array containing data:
     *                    - 'course' (string): Indicates whether the linked entity is a course ('true') or not ('false').
     *                    - 'semester' (string): Indicates whether the linked course has a semester associated ('true') or not ('false').
     *                    - 'trainingId2' (int): The ID of the training.
     *                    - 'level' (int): The level of the training association.
     *                    - If 'course' is 'true':
     *                      - 'courseId' (array): An array containing IDs of linked courses.
     *                      - 'semester' (int): The semester.
     *                    - If 'course' is 'false':
     *                      - 'luId' (int): The ID of the linked non-course entity (LU).
     */
    function createLink($data) {
        $course = $data['isCourse'];
        $isSemester = $data['isSemester'];
        
        $this->record->trainingid = $data['trainingId2'];
        $this->record->level = $data['level'];

        // Course
        if($course === 'true') {
            if($isSemester === 'true') {
                $this->record->semester = $data['semester'];
            }
            foreach ($data['courseId'] as $courseid) {
                if ($courseid != 0) {
                    $this->record->courseid = $courseid;
                    $this->DB->insert_record('local_training_architecture_training_links', $this->record);
                }
            }
        }
        // Not course
        else {
            foreach ($data['luId'] as $luId) {
                if ($luId != 0) {
                    $this->record->luid = $luId;
                    $this->DB->insert_record('local_training_architecture_training_links', $this->record);
                }
            }
        }

    }

    /**
     * Deletes a link between a training and another entity.
     *
     * @param int $linkId The ID of the link to be deleted.
     */
    function deleteLink($linkId) {
        $this->DB->delete_records('local_training_architecture_training_links', ['id' => $linkId]); 
    }

    /**
     * Checks if an lu is used in the context of a training link.
     *
     * @param int $luId The ID of the lu entity.
     * @param int $courseId The ID of the course entity.
     * @param int $trainingId The ID of the training.
     * @return bool True if the lu is used, false otherwise.
     */
    function isLuUsed($luId, $courseId, $trainingId) {
        if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'luid1 = ? AND trainingid = ?', [$luId, $trainingId])) 
        {
            return true;

        } else if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'luid2 = ? AND trainingid = ? AND isluid2course = ?', [$luId, $trainingId, 'false'])) 
        {
            return true;

        } else if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'luid2 = ? AND trainingid = ? AND isluid2course = ?', [$courseId, $trainingId, 'true'])) 
        {

        return true;

        }
        return false;
    }

}