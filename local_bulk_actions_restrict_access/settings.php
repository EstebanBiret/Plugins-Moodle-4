<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category  admin
 * @package   local_bulk_actions_restrict_access
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig || has_capability('local/bulk_actions_restrict_access:view', context_system::instance())) {

    $ADMIN->add(
        'courses',
        new admin_externalpage(
            'local_bulk_actions_restrict_access', // Unique name.
            get_string('pluginname', 'local_bulk_actions_restrict_access'), // Human name.
            new moodle_url('/local/bulk_actions_restrict_access/index.php')
        )
    );

}