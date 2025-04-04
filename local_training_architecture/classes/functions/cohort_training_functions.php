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
 * Cohort - Training Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class cohort_training_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Creates links between cohorts and trainings in the database.
     *
     * @param array $data An associative array containing the data needed to create the links.
     *                    It must contain the following keys:
     *                    - 'trainingId': The ID of the training.
     *                    - 'cohortId': An array of cohort IDs to create the links with.
     * @return void
     */
    function createLink($data) {
        $this->record->trainingid = $data['trainingId'];

        foreach ($data['cohortId'] as $cohortId) {
            if($cohortId != 0) {
                $this->record->cohortid = $cohortId;
                $this->DB->insert_record('local_training_architecture_cohort_to_training', $this->record);
            }
        }
    }
    
    /**
     * Deletes a link between a cohort and a training from the database.
     *
     * @param int $linkId The ID of the link to delete.
     * @return void
     */
    function deleteLink($linkId) {
        $this->DB->delete_records('local_training_architecture_cohort_to_training', ['id' => $linkId]); 
    }

}