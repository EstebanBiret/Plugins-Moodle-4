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
 * Training - Level Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class training_level_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates links between training and levels.
     *
     * @param array $data An associative array containing data:
     *                    - 'trainingId3' (int): The ID of the training course.
     *                    - 'numberOfLevels' (int): The number of levels to link.
     *                    - 'trainingToLevel1' (int): The ID of the first level.
     *                    - 'trainingToLevel2' (int): The ID of the second level.
     */
    function createLink($data) {
        $trainingId = $data['trainingId3'];
        $numberOfLevels = $data['numberOfLevels'];

        for ($i = 1; $i <= $numberOfLevels; $i++) {
            $levelNameId = $data['trainingToLevel' . $i];
    
            $this->record->trainingid = $trainingId;
            $this->record->levelnamesid = $levelNameId;
            $this->record->level = $i;

            $this->DB->insert_record('local_training_architecture_level_names_to_training', $this->record);
        }
    }

    /**
     * Deletes links between a training and its associated levels.
     *
     * @param int $trainingId The ID of the training.
     */
    function deleteLink($trainingId) {
        $this->DB->delete_records('local_training_architecture_level_names_to_training', ['trainingid' => $trainingId]); 
    }
    
}