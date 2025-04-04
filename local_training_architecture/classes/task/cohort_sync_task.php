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
 * Scheduled task to sync records on cohort_to_training table
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   training_architecture
 */

namespace local_training_architecture\task;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

class cohort_sync_task extends \core\task\scheduled_task {
    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
         return get_string('cohortTask', 'local_training_architecture');
    }

    /**
     * Execute the task.
     *
     */
    public function execute() {
        global $DB;
        $DB->delete_records_select('local_training_architecture_cohort_to_training', 'cohortid NOT IN (SELECT id FROM {cohort})');
    }
    
}