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
 * Plugin strings are defined here.
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category  string
 * @package   local_bulk_actions_restrict_access
 */

defined('MOODLE_INTERNAL') || die();

// Common
$string['pluginname'] = 'Actions par lots des restrictions d\'accès';
$string['bulk_actions_restrict_access:view'] = 'Voir les actions par lots des restrictions d\'accès';
$string['show_hide'] = 'Afficher/Cacher';
$string['choose_select'] = 'Choisir...';
$string['equal'] = 'Est égal à';
$string['section'] = 'Section ';

$string['start_date'] = 'À partir du ';
$string['end_date'] = 'Jusqu\'au ';

$string['dates_found'] = ' dates trouvées';
$string['date_found'] = ' date trouvée';
$string['no_date'] = 'Aucune date trouvée';

$string['type'] = 'Type';
$string['date'] = 'Date';

$string['singular'] = ' item sélectionné';
$string['plural'] = ' items sélectionnés';
$string['no_selected_activities'] = 'Aucun item sélectionné';
$string['uncheck_everything'] = 'Tout décocher';

$string['course_link'] = 'Accéder au cours';

$string['singular_number_of_section'] = ' section, ';
$string['plural_number_of_sections'] = ' sections, ';
$string['singular_number_of_activity'] = ' activité/ressource';
$string['plural_number_of_activities'] = ' activités/ressources';

$string['checkAll'] = 'Tout cocher ';

// All cohorts
$string['cohort'] = 'Toutes les cohortes';

// Trainings of cohort
$string['trainings_of_this_cohort'] = 'Formations de cette cohorte';

// LU
$string['lu1_of_training'] = 'UA de niveau 1 de cette formation';
$string['lu2_of_lu1'] = 'UA de niveau 2 de cette UA';

// Course
$string['courses'] = 'Cours de cette UA';

// Profile field
$string['profile_field'] = 'Champ de profil';
$string['no_data_field'] = 'Aucune donnée pour cette cohorte et ce champ de profil.';

// Filter by
$string['filter_by'] = 'Filtrer par';
$string['user_profile'] = 'Profil utilisateur';
$string['group'] = 'Groupe';
$string['no_data_type'] = 'Aucune donnée pour ce type de condition.';

// Group
$string['no_group'] = 'Aucun groupe pour ce cours.';

// Errors
$string['no_trainings'] = 'Cette cohorte n\'est liée à aucune formation.';
$string['no_lu'] = 'Cette formation n\'est associée à aucune UA.';
$string['no_lu2'] = 'Cette UA de niveau 1 n\'est associée à aucune UA de niveau 2.';
$string['no_courses'] = 'Cette UA n\'est associée à aucun cours.';
$string['no_activities'] = 'Ce cours n\'est associé à aucune activité ou ressource.';

// Notification messages
$string['notif_changes_saved'] = 'Modifications enregistrées !';
$string['notif_same_date'] = 'Les deux dates ne peuvent pas être identiques !';
$string['notif_end_before_start'] = 'La date de début ne peut pas être supérieure à la date de fin !';