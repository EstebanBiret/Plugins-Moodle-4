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
 *  Report Students Achievements capabilities are defined here.
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_student_achievements
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'report/students_achievements:view' => array(
            'riskbitmask' => RISK_PERSONAL,
            'captype' => 'read',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                    'editingteacher' => CAP_ALLOW,
                    'manager' => CAP_ALLOW //admin
            ),
            'clonepermissionsfrom' => 'moodle/site:viewreports',
    )
);


