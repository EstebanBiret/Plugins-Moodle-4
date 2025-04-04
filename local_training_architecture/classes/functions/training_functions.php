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
 * Training Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class training_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates a new training.
     *
     * @param array $data An associative array containing training data:
     *                    - 'trainingFullName' (string): The full name of the training.
     *                    - 'trainingShortName' (string): The short name of the training.
     *  of the training.
     *                    - 'trainingIDNumber' (string): The ID number of the training.
     *                    - 'trainingDescription' (string): The description of the training.
     *                    - 'trainingLevel' (int): The granularity level of the training.
     *                    - 'trainingSemester' (string): Indicates if the training is semester-based ('yes' or 'no').
     */
    function create($data) {
        $this->record->fullname = $data['trainingFullName'];
        $this->record->shortname = $data['trainingShortName'];
        $this->record->idnumber = str_replace(' ', '', $data['trainingIDNumber']);
        $this->record->description = $data['trainingDescription']['text'];
        $this->record->granularitylevel = $data['trainingLevel'];
        $this->record->issemester = ($data['trainingSemester'] == 'yes') ? 1 : 0;

        $this->DB->insert_record('local_training_architecture_training', $this->record);
    }

    /**
     * Edits an existing training.
     *
     * @param array $data An associative array containing training data:
     *                    - 'id' (int): The ID of the training to edit.
     *                    - 'trainingFullName' (string): The full name of the training.
     *                    - 'trainingShortName' (string): The short name of the training.
     *                    - 'trainingIDNumber' (string): The ID number of the training.
     *                    - 'trainingDescription' (string): The description of the training.
     *                    - 'trainingLevel' (int): The granularity level of the training.
     *                    - 'trainingSemester' (string): Indicates if the training is semester-based ('yes' or 'no').
     */
    function edit($data) {
        $this->record->id = $data['id'];
        $this->record->fullname = $data['trainingFullName'];
        $this->record->shortname = $data['trainingShortName'];
        $this->record->idnumber = str_replace(' ', '', $data['trainingIDNumber']);
        $this->record->description = $data['trainingDescription']['text'];
        $this->record->granularitylevel = $data['trainingLevel'];
        $this->record->issemester = ($data['trainingSemester'] == 'yes') ? 1 : 0;

        $this->DB->update_record('local_training_architecture_training', $this->record);
    }

    /**
     * Deletes a training.
     *
     * @param int $id The ID of the training to delete.
     */
    function delete($id) {
        $this->DB->delete_records('local_training_architecture_training', ['id' => $id]); 

        // Delete references
        $this->DB->delete_records('local_training_architecture_level_names_to_training', ['trainingid' => $id]); 
        $this->DB->delete_records('local_training_architecture_cohort_to_training', ['trainingid' => $id]); 
        $this->DB->delete_records('local_training_architecture_training_links', ['trainingid' => $id]); 
        $this->DB->delete_records('local_training_architecture_lu_to_lu', ['trainingid' => $id]); 
        $this->DB->delete_records('local_training_architecture_order', ['trainingid' => $id]); 
        $this->DB->delete_records('local_training_architecture_courses_not_architecture', ['trainingid' => $id]); 
    }
    
}