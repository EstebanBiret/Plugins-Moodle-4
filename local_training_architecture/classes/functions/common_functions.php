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
 * Common Functions
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */


require_once(dirname(__FILE__) . '/../../../../config.php');

class common_functions {

    protected $DB;
    protected $record;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->record = new stdClass();
    }

    /**
     * Retrieves the full name of a training given its ID.
     *
     * @param int $trainingId The ID of the training.
     * @return string The full name of the training.
     */
    function getTrainingFullName($trainingId) {
        return $this->DB->get_field('local_training_architecture_training', 'fullname', ['id' => $trainingId]);
    }    

    /**
     * Retrieves the name of a cohort given its ID.
     *
     * @param int $cohortId The ID of the cohort.
     * @return string The name of the cohort.
     */
    function getCohortName($cohortId) {
        return $this->DB->get_field('cohort', 'name', ['id' => $cohortId]);
    }

    /**
     * Retrieves the full name of a course given its ID.
     *
     * @param int $courseId The ID of the course.
     * @return string The full name of the course.
     */
    function getCourseFullName($courseId) {
        return $this->DB->get_field('course', 'fullname', ['id' => $courseId]);
    }

    /**
     * Retrieves the full name of an LU (Learning Unit)  given its ID.
     *
     * @param int $luId The ID of the LU.
     * @return string The full name of the LU.
     */
    function getLuFullName($luId) {
        return $this->DB->get_field('local_training_architecture_lu', 'fullname', ['id' => $luId]);
    }

    /**
     * Retrieves the full name of a level given its ID.
     *
     * @param int $levelId The ID of the level.
     * @return string The full name of the level.
     */
    function getLevelFullName($levelId) {
        return $this->DB->get_field('local_training_architecture_level_names', 'fullname', ['id' => $levelId]);
    }

    /**
     * Retrieves the level names associated with a training ID.
     *
     * @param int $training_id The ID of the training.
     * @return array An associative array of level names indexed by level.
     */
    function getLevelNamesByTrainingId($training_id) {
        $levels = $this->DB->get_records_sql('SELECT * FROM {local_training_architecture_level_names_to_training} WHERE trainingid = ? AND level <= 2', [$training_id]);
        $level_names_data = [];
    
        foreach ($levels as $level) {
            $levelName = $this->DB->get_record('local_training_architecture_level_names', ['id' => $level->levelnamesid], '*');
            $level_names_data[$level->level] = $levelName->fullname;
        }

        return $level_names_data;
    }

    /**
     * Retrieves the number of levels for a given training ID.
     *
     * @param int $trainingId The ID of the training.
     * @return int The number of levels.
     */
    function getNumberOfLevels($trainingId) {
        return $this->DB->get_field('local_training_architecture_training', 'granularitylevel', ['id' => $trainingId]);
    }
    
}

