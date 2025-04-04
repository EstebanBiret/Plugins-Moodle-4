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
 * @author    2023 Jérémie Pilette <jerem.pilette@gmail.com>, 2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category  admin
 * @package   local_training_architecture
 */

defined('MOODLE_INTERNAL') || die();

if($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage('local_training_architecture', 
    get_string('pluginname', 'local_training_architecture'), new moodle_url('/local/training_architecture/index.php')));
}