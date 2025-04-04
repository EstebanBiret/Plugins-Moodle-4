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
 * @package   local_training_architecture
 */

defined('MOODLE_INTERNAL') || die();

// Common.
$string['pluginname'] = 'Architecture de formation';
$string['training_architecture:manage'] = 'Modifier et gérer l\'architecture des formations';
$string['heading'] = 'Gérer l\'architecture de formation';
$string['title'] = 'Architecture de formation';
$string['fullName'] = 'Nom';
$string['shortName'] = 'Nom abrégé';
$string['IDNumber'] = 'Numéro d\'identification';
$string['description'] = 'Description';
$string['numberOfLevel'] = 'Nombre de niveaux de granularité';
$string['level'] = 'Niveau de granularité';
$string['level1'] = 'Nom du niveau de granularité 1';
$string['level2'] = 'Nom du niveau de granularité 2';
$string['confirmYes'] = 'Confirmer';
$string['confirmNo'] = 'Annuler';
$string['actions'] = 'Actions';
$string['selection'] = 'Sélection';
$string['deleteSelection'] = 'Supprimer les éléments sélectionnés';
$string['yes'] = 'Oui';
$string['no'] = 'Non';
$string['back'] = 'Retour';
$string['order'] = 'Ordre';
$string['up'] = 'Monter';
$string['down'] = 'Descendre';
$string['lu1'] = 'UA 1';
$string['lu2'] = 'UA 2';
$string['chooseOption'] = 'Choisir une option';
$string['course'] = 'Cours';
$string['lu'] = 'Unité d\'Apprentissage';
$string['allForms'] = 'Tous les formulaires : ';
$string['deleteMultipleTitle1'] = 'Supprimer ';
$string['expand'] = 'Tout déplier';
$string['collapse'] = 'Tout replier';

// Training.
$string['createTraining'] = 'Création d\'une formation';
$string['trainingLevel'] = 'Granularité de niveau ';
$string['editTrainingTitle'] = 'Modifier la formation';
$string['deleteTrainingTitle'] = 'Supprimer la formation';
$string['selectNumberOfLevel'] = 'Nombre de niveaux de granularité';
$string['semesterChoice'] = 'Architecture en semestre';

// Level name.
$string['createLevelTitle'] = 'Création d\'un niveau de granularité';
$string['editLevelTitle'] = 'Modifier le nom de niveau';
$string['deleteLevelTitle'] = 'Supprimer le nom de niveau';

// Training to level name.
$string['trainingToLevel'] = 'Association Formation - Noms des niveaux de granularité';
$string['deleteTrainingLevelTitle'] = 'Supprimer l\'association Formation - Nom de niveau de granularité';

// Cohort to training.
$string['cohortToTraining'] = 'Association Formation - Cohorte';
$string['cohort'] = 'Cohorte';
$string['training'] = 'Formation';
$string['deleteCohortTrainingTitle'] = 'Supprimer l\'association Formation - Cohorte';

// LU.
$string['createLuTitle'] = 'Création d\'une Unité d\'Apprentissage';
$string['editLuTitle'] = 'Modifier l\'Unité d\'Apprentissage';
$string['deleteLuTitle'] = 'Supprimer l\'Unité d\'Apprentissage';

// Not in architecture. 
$string['coursesNotInArchitectureTitle'] = 'Association des cours hors architecture';
$string['deleteNotArchitecture'] = 'Supprimer l\'association cours - formation';
$string['deleteMultipleCoursesNotInArchitectureTitle2'] = ' association(s) de cours - formation';

// Training links.
$string['trainingLinks'] = 'Association des caractéristiques d\'une formation';
$string['semester'] = 'Semestre';
$string['selectSemester'] = 'Choisir un semestre';
$string['deleteTrainingLinks'] = 'Supprimer les caractéristiques de la formation';
$string['deleteMultipleTrainingLinksTitle2'] = ' lien(s) de caractéristiques de la formation';

// LU to LU.
$string['luToLu'] = 'Association des Unités d\'Apprentissage';
$string['luLevel'] = 'Unité d\'Apprentissage de niveau ';
$string['deleteLuLuTitle'] = 'Supprimer l\'association d\'Unités d\'Apprentissage';
$string['deleteMultipleLuLuTitle2'] = ' association(s) d\'Unités d\'Apprentissage';

// Sort order LU.
$string['sortLuTitle'] = 'Gérer l\'ordre des Unités d\'Apprentissage';
$string['sortLu'] = 'Gérer l\'ordre des Unités d\'Apprentissage de ';
$string['noLus'] = 'Cette formation n\'a pas encore d\'Unités d\'Apprentissage.';
$string['orderInformations'] = 'Cette page sert à ordonner les Unités d\'Apprentissage (UA) d\'une formation. 
Cliquez sur une UA de niveau 1 pour ordonner ses UA de niveau 2. 
(Cela aura uniquement un impact sur l\'ordre d\'affichage des UA d\'une formation liée au plugin bloc associé (block_training_architecture)).';

// Help.
$string['trainingLevel'] = 'Niveau de granularité ';
$string['trainingLevel_help'] = 'Le niveau de granularité correspond à un niveau de découpage de la formation.
Les formations proposent en général 2 ou 3 niveaux de granularité (hors activités/ressources).
Le dernier niveau des Unités d\'Apprentissage correspond ici au cours Moodle.

Exemples possibles : 

Unité d\'enseignement / Module / Cours
Bloc de compétence / Module / Cours
Unité de formation / Module / Cours
Module / Chapitre / Cours
...

Il n\'est pas possible d\'avoir deux niveaux de granularité de même nom.';

$string['fullName_help'] = ' Correspond au nom du niveau de granularité choisi.
Cela peut faire référence à un découpage correspondant à une approche par contenu-matière ou une approche par compétence.
Ce nom pourra être affiché à certains endroit du site en fonction d\'autres plugins dépendant associés.

Ce nom doit être unique.';

$string['shortName_help'] = ' Ce nom est un raccourci du nom du niveau de granularité.
Ce nom abrégé pourra être affiché à certains endroit du site en fonction d\'autres plugins dépendant associés.

Ce nom doit être unique.';

$string['IDNumber_help'] = ' Référence de l\'entité créée au sein du système d\'information.
Cela permet de faire référence, donc de lier l\'entité avec le reste du système d\'information.

Ce numéro doit être unique.';

$string['granularityLevel_help'] = 'Sélectionnez ici le niveau de granularité auquel vous souhaitez associer l\'Unité d\'Apprentissage pour une formation donnée.
Vous devez également associer chaque cours de votre plan de formation au bon niveau de granularité.
Les cours correspondent au dernier niveau de granularité des Unités d\'Apprentissage (hors activités/ressources) de votre plan de formation.

Exemple :
Si votre formation est en 1 niveau de granularité, le cours sera de niveau 2,
Si votre formation est en 2 niveaux de granularité, le cours sera de niveau 3.';

$string['createTrainingGranularityLevel_help'] = 'Choisissez le nombre de niveaux de granularité supérieurs au cours (ne pas compter le cours).

Exemple :
Un plan de formation à 1 niveau : Module > Cours.
Un plan de formation à 2 niveaux : Bloc > Module > Cours.';

// Errors.
$string['nameAlreadyExists'] = 'Ce nom est déjà pris.';
$string['shortNameAlreadyExists'] = 'Ce nom abrégé est déjà pris.';
$string['IDNumberAlreadyExists'] = 'Ce numéro d\'identification est déjà pris.';
$string['selectDifferentLevel'] = 'Ce niveau a déjà été choisi.';
$string['associationAlreadyExists'] = 'Cette association existe déjà.';
$string['trainingLevelAlreadyExists'] = 'Cette formation est déjà liée à des niveaux.';
$string['associationAlreadyExistsCohorts'] = 'Ces cohortes sont déjà liées à cette formation.';
$string['courseAlreadyInArchitecture'] = 'Ce cours est déjà dans l\'architecture pour cette formation.';
$string['courseAlreadyNotInArchitecture'] = 'Ce cours a déjà été associé hors de l\'architecture pour cette formation.';
$string['luDuplicate'] = 'Cette Unité d\'Apprentissage a déjà été choisie.';
$string['luNotRelated'] = 'Cette Unité d\'Apprentissage n\'est pas liée à cette formation.';
$string['courseNotRelated'] = 'Ce cours n\'est pas lié à cette formation.';
$string['levelTooHigh'] = 'Ce niveau de granularité est supérieur au niveau de granularité maximum de cette formation.';
$string['errorEditSemester'] = 'Vous ne pouvez pas modifier ce champ, car il y a des références aux semestres dans d\'autres tables.';
$string['errorEditLevel'] = 'Vous ne pouvez pas modifier ce champ, car il y a des références aux niveaux dans d\'autres tables.';
$string['lu1AlreadyAsLu2'] = 'Cette Unité d\'Apprentissage de niveau 1 est déjà impliquée dans une autre relation en tant que niveau 2, pour cette formation';
$string['lu2AlreadyAsLu1'] = 'Cette Unité d\'Apprentissage de niveau 2 est déjà impliquée dans une autre relation en tant que niveau 1, pour cette formation';

// Warnings.
$string['deleteLinkWarning'] = 'Êtes-vous sûr de vouloir supprimer cette association ?';
$string['deleteMultipleWarning'] = 'Êtes-vous sûr de vouloir supprimer cette/ces association(s) ?';
$string['deleteLuWarning'] = 'Êtes-vous sûr de vouloir supprimer cette Unité d\'Apprentissage ?';
$string['deleteLevelNameWarning'] = 'Êtes-vous sûr de vouloir supprimer ce nom de niveau ?';
$string['deleteTrainingWarning'] = 'Êtes-vous sûr de vouloir supprimer cette formation ? Cela entrainera la suppression de l\'ensemble des données liées à cette formation.';

// Notify errors.
$string['notifyErrorLuToLu'] = 'Vous ne pouvez pas supprimer cette association, une des Unités d\'Apprentissage est utilisée dans une ou plusieurs autres relations.';
$string['notifyErrorMultipleLuToLu'] = 'Vous ne pouvez pas supprimer cette/ces association(s), une des Unités d\'Apprentissage est utilisée dans une ou plusieurs autres relations.';
$string['notifyErrorLevel'] = 'Vous ne pouvez pas supprimer ce nom de niveau, il est associé à une ou plusieurs formations.';
$string['notifyErrorLu'] = 'Vous ne pouvez pas supprimer cette Unité d\'apprentissage, elle est utilisée dans une ou plusieurs autres relations.';
$string['notifyErrorMultipleTrainingLinks'] = 'Vous ne pouvez pas supprimer cette/ces association(s), une des Unités d\Apprentissage est impliquée dans une ou plusieurs relations pour cette formation dans le formulaire "Associations des Unités d\'Apprentissage".';

// Tasks.
$string['cohortTask'] = 'Synchroniser les données de cohort_to_training en fonction des cohortes existantes';
$string['courseTask'] = 'Synchroniser les données de lu_to_course en fonction des cours existants';