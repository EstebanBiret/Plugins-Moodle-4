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
 * LU to LU functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class lu_lu_functions {

    protected $DB;
    protected $record;
    protected $recordOrder;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
        $this->recordOrder = new stdClass();
    }

    /**
     * Creates links between LU.
     *
     * @param array $data An associative array containing link data:
     *                    - 'luToLuTrainingId' (int): The ID of the training.
     *                    - 'luToLuCourseId' (array): An array of course IDs.
     *                    - 'numberOfLu' (int): The number of LU.
     *                    - 'luToLuId1' (int): The ID of the first LU.
     *                    - 'luToLuId2' (int): The ID of the second LU.
     */
    function createLink($data) {
        $trainingId = $data['luToLuTrainingId'];
        $coursesId = $data['luToLuCourseId'];
        $numberOfLu = $data['numberOfLu'];
    
        foreach ($coursesId as $courseid) {
            if ($courseid != 0) {
                for ($i = 1; $i <= $numberOfLu; $i++) {
                    $this->record->trainingid = $trainingId;
                    $this->record->luid1 = $data['luToLuId' . $i];
                    
                    // Course
                    if ($i === $numberOfLu) {
                        $this->record->luid2 = $courseid;
                        $this->record->isluid2course = 'true'; 
                    } 
                    // Other link
                    else {
                        $this->record->luid2 = $data['luToLuId' . ($i + 1)];
                        $this->record->isluid2course = 'false';
                    }
    
                    $where = 'trainingid = ? AND luid1 = ? AND luid2 = ? AND isluid2course = ?';
                    $params = [$trainingId, $this->record->luid1, $this->record->luid2, $this->record->isluid2course];
                    
                    // If record doesn't exists
                    if (!$this->DB->record_exists_select('local_training_architecture_lu_to_lu', $where, $params)) {
                        $id = $this->DB->insert_record('local_training_architecture_lu_to_lu', $this->record);

                        if (!$this->DB->record_exists('local_training_architecture_order', ['trainingid' => $trainingId, 'luid' => $this->record->luid1])) {
                            $this->recordOrder->trainingid = $trainingId;
                            $this->recordOrder->luid = $this->record->luid1;
                            $this->recordOrder->sortorder = $id;

                            $this->DB->insert_record('local_training_architecture_order', $this->recordOrder);
                        }
                    }


                }
            }
        }
    }
    

    /**
     * Deletes a link between LU.
     *
     * @param int $id The ID of the link to delete.
     */
    function deleteLink($id) {
        $this->DB->delete_records('local_training_architecture_lu_to_lu', ['id' => $id]); 
    }

    /**
     * Checks if a link between LU is already used.
     *
     * @param int $id The ID of the link to check.
     * @return bool True if the link is already used, false otherwise.
     */
    function isLinkAlreadyUsed($id) {
        $link = $this->DB->get_record('local_training_architecture_lu_to_lu', ['id' => $id], '*');

        // No childs
        if($link->isluid2course === 'true') {
            return false;
        }

        // Childs
        else if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'trainingid = ? AND luid1 = ?', [$link->trainingid, $link->luid2])) 
        {
            return true;

        }

        return false; // In case

    }

    /**
     * Checks if a link between LU is already used (multiple edition).
     *
     * @param int $id The ID of the link to check.
     * @return bool True if the link is already used, false otherwise.
     */
    function isLinkAlreadyUsedMultiple($id, $ids) {
        $link = $this->DB->get_record('local_training_architecture_lu_to_lu', ['id' => $id], '*');

        // No childs
        if($link->isluid2course === 'true') {
            return false;
        }

        // Childs
        else if($this->DB->record_exists_select(
            'local_training_architecture_lu_to_lu', 
            'trainingid = ? AND luid1 = ? AND id NOT IN (' . implode(',', $ids) . ')',
            [$link->trainingid, $link->luid2]))        
            {
            return true;

        }

        return false; //in case

    }
    
}