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
 * LU (Learning Unit) Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class lu_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates a new LU.
     *
     * @param array $data An associative array containing LU data:
     *                    - 'luFullName' (string): The full name of the LU.
     *                    - 'luShortName' (string): The short name of the LU.
     *                    - 'luIDNumber' (string): The ID number of the LU.
     *                    - 'luDescription' (string): The description of the LU.
     */
    function create($data) {
        $this->record->fullname = $data['luFullName'];
        $this->record->shortname = $data['luShortName'];
        $this->record->idnumber = str_replace(' ', '', $data['luIDNumber']);
        $this->record->description = $data['luDescription']['text'];

        $this->DB->insert_record('local_training_architecture_lu', $this->record);
    }

    /**
     * Edits an existing LU.
     *
     * @param array $data An associative array containing LU data:
     *                    - 'id' (int): The ID of the LU to edit.
     *                    - 'luFullName' (string): The full name of the LU.
     *                    - 'luShortName' (string): The short name of the LU.
     *                    - 'luIDNumber' (string): The ID number of the LU.
     *                    - 'luDescription' (string): The description of the LU.
     */
    function edit($data) {
        $this->record->id = $data['id'];
        $this->record->fullname = $data['luFullName'];
        $this->record->shortname = $data['luShortName'];
        $this->record->idnumber = str_replace(' ', '', $data['luIDNumber']);
        $this->record->description = $data['luDescription']['text'];

        $this->DB->update_record('local_training_architecture_lu', $this->record);
    }

    /**
     * Deletes an existing LU.
     *
     * @param int $id The ID of the LU to delete.
     */
    function delete($id) {
        $this->DB->delete_records('local_training_architecture_lu', ['id' => $id]); 

        //DELETE CASCADE
        $this->DB->delete_records('local_training_architecture_training_links', ['luid' => $id]); 
        $this->DB->delete_records('local_training_architecture_order', ['luid' => $id]); 
        $this->DB->delete_records('local_training_architecture_lu_to_lu', ['luid1' => $id]); 

    }

    /**
     * Checks if an LU is used in any associations.
     *
     * @param int $luId The ID of the LU to check.
     * @return bool True if the LU is used, false otherwise.
     */
    function isLuUsed($luId) {
        $result = false;
        if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'luid1 = ?', [$luId])) 
        {
            $result = true;

        } else if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'luid2 = ? AND isluid2course = ?', [$luId, 'false'])) 
        {
            $result = true;

        } else if($this->DB->record_exists_select(
        'local_training_architecture_training_links', 
        'luid = ?', [$luId])) 
        {
        $result = true;

        }

        return $result;
    }
    
}