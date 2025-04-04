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
 * Bulk actions restrict access definition navigation
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_bulk_actions_restrict_access
 */

defined('MOODLE_INTERNAL') || die();

function local_bulk_actions_restrict_access_extend_navigation_course($navigation, $course, $context) {

    if (has_capability('local/bulk_actions_restrict_access:view', $context)) {

        $name = get_string('pluginname', 'local_bulk_actions_restrict_access');
        $url = new moodle_url('/local/bulk_actions_restrict_access/index.php', ['id' => $course->id]);

        $navigation->add(
            $name,
            $url,
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/grading', '')
        );
    }
}