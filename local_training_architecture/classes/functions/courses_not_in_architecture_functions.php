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
 * Courses not in architecture Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class courses_not_in_architecture_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates links between courses not in architecture and trainings.
     *
     * @param array $data An associative array containing link data:
     *                    - 'coursesNotInArchitectureTrainingId' (int): The ID of the training.
     *                    - 'coursesNotInArchitectureCourseId' (array): An array of course IDs.
     */
    function createLink($data) {
        $trainingId = $data['coursesNotInArchitectureTrainingId'];
        $coursesId = $data['coursesNotInArchitectureCourseId'];
    
        foreach ($coursesId as $courseid) {
            if ($courseid != 0) {
                $this->record->trainingid = $trainingId;
                $this->record->courseid = $courseid;

                $where = 'trainingid = ? AND courseid = ?';
                $params = [$trainingId, $courseid];
                
                //if record doesn't exists
                if (!$this->DB->record_exists_select('local_training_architecture_courses_not_architecture', $where, $params)) {
                    $this->DB->insert_record('local_training_architecture_courses_not_architecture', $this->record);
                }
            }
        }
    }

    /**
     * Deletes a link between training and course(s) not in architecture.
     *
     * @param int $id The ID of the link to delete.
     */
    function deleteLink($id) {
        $this->DB->delete_records('local_training_architecture_courses_not_architecture', ['id' => $id]); 
    }
    
}