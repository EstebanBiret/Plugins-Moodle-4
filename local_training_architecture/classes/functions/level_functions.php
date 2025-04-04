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
 * Level Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class level_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates a new level.
     *
     * @param array $data An associative array containing level data:
     *                    - 'levelFullName' (string): The full name of the level.
     *                    - 'levelShortName' (string): The short name of the level.
     *                    - 'levelDescription' (string): The description of the level.
     */
    function create($data) {

        $this->record->fullname = $data['levelFullName'];
        $this->record->shortname = $data['levelShortName'];
        $this->record->description = $data['levelDescription'];

        $this->DB->insert_record('local_training_architecture_level_names', $this->record);
    }

    /**
     * Edits an existing level.
     *
     * @param array $data An associative array containing level data:
     *                    - 'id' (int): The ID of the level to edit.
     *                    - 'levelFullName' (string): The full name of the level.
     *                    - 'levelShortName' (string): The short name of the level.
     *                    - 'levelDescription' (string): The description of the level.
     */
    function edit($data) {
        $this->record->id = $data['id'];
        $this->record->fullname = $data['levelFullName'];
        $this->record->shortname = $data['levelShortName'];
        $this->record->description = $data['levelDescription'];

        $this->DB->update_record('local_training_architecture_level_names', $this->record);
    }

    /**
     * Deletes a level.
     *
     * @param int $id The ID of the level to delete.
     */
    function delete($id) {
        $trainings = $this->DB->get_records('local_training_architecture_level_names_to_training', ['levelnamesid' => $id]);
        foreach ($trainings as $training) {
            $trainingId = $training->trainingid;
            $this->DB->delete_records('local_training_architecture_level_names_to_training', ['trainingid' => $trainingId]); 

        }
        $this->DB->delete_records('local_training_architecture_level_names', ['id' => $id]); 
    }

    /**
     * Checks if a level is used.
     *
     * @param int $id The ID of the level to check.
     * @return bool True if the level is used, false otherwise.
     */
    function isLevelUsed($id) {
        if($this->DB->record_exists_select(
            'local_training_architecture_level_names_to_training', 
            'levelnamesid = ?', [$id])) 
        {
            return true;
        }
        return false;
    }
    
}