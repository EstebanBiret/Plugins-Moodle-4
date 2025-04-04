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
 * This file is responsible for define all the functions for display and export reports
 *
 * @copyright 2024 IFRASS
 * @author    2024 Esteban BIRET-TOSCANO <esteban.biret@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   report_student_achievements
*/

// All require files
require_once(dirname(__FILE__) . '/../../config.php');

/**
 * Retrieve activity dates based on profile field conditions.
 *
 * @param array $conditions The conditions to evaluate.
 * @param string $profileFieldValue The profile field value to match.
 * @param string $profileFieldShortName The profile field short name to match.
 * @return array|null An associative array with 'startDate' and 'endDate' or null if no dates are found.
 */
function getProfileActivityDate($conditions, $profileFieldValue, $profileFieldShortName) {
    foreach ($conditions as $condition) {
        if (isset($condition->type) && $condition->type === 'profile' && $condition->cf === $profileFieldShortName && $condition->v === $profileFieldValue) {
            
            // Initialize dates
            $startDate = null;
            $endDate = null;
            
            // Check for dates at the same level
            foreach ($conditions as $subCondition) {
                if (isset($subCondition->type) && $subCondition->type === 'date') {
                    $date = $subCondition->t;

                    if ($subCondition->d === '>=') {
                        if (!$startDate || $date < $startDate) {
                            $startDate = $date;
                        }
                    } elseif ($subCondition->d === '<') {
                        if (!$endDate || $date > $endDate) {
                            $endDate = $date;
                        }
                    }
                }
            }

            return [
                'startDate' => $startDate ? formatDate($startDate) : null,
                'endDate' => $endDate ? formatDate($endDate) : null,
            ];

        } elseif (isset($condition->c)) {
            
            // Recurse into nested conditions
            $date = getProfileActivityDate($condition->c, $profileFieldValue, $profileFieldShortName);
            if ($date) {
                return $date;
            }
        }
    }

    return null;
}

/**
 * Retrieve activity dates based on groups conditions.
 *
 * @param array $conditions The conditions to evaluate.
 * @param string $groupId The group id to match.
 * @return array|null An associative array with 'startDate' and 'endDate' or null if no dates are found.
 */
function getGroupActivityDate($conditions, $groupId) {
    foreach ($conditions as $condition) {

        if (isset($condition->type) && $condition->type === 'group' && $condition->id == $groupId) {

            // Initialize dates
            $startDate = null;
            $endDate = null;
            
            // Check for dates at the same level
            foreach ($conditions as $subCondition) {
                if (isset($subCondition->type) && $subCondition->type === 'date') {
                    $date = $subCondition->t;
                    
                    if ($subCondition->d === '>=') {
                        if (!$startDate || $date < $startDate) {
                            $startDate = $date;
                        }
                    } elseif ($subCondition->d === '<') {
                        if (!$endDate || $date > $endDate) {
                            $endDate = $date;
                        }
                    }
                }
            }

            return [
                'startDate' => $startDate ? formatDate($startDate) : null,
                'endDate' => $endDate ? formatDate($endDate) : null,
            ];

        } elseif (isset($condition->c)) {
            
            // Recurse into nested conditions
            $date = getGroupActivityDate($condition->c, $groupId);
            if ($date) {
                return $date;
            }
        }
    }

    return null;
}

/**
 * Get availability dates for a specific activity based on profile field conditions.
 *
 * @param int $activityId The ID of the activity.
 * @param string $profileFieldValue The profile field value to match.
 * @param string $profileFieldShortName The profile field short name to match.
 * @return array|null An associative array with 'startDate' and 'endDate' or null if no dates are found.
 */
function availabilityDates($activityId, $restrictionType, $profileFieldValue, $profileFieldShortName, $groupId) {
    global $DB;

    $activity = $DB->get_record('course_modules', ['id' => $activityId], 'availability');

    if (isset($activity->availability)) {
        $availability = json_decode($activity->availability);

        if (isset($availability->c) && count($availability->c) > 0) {

            if($restrictionType == 'profile') {
                $date = getProfileActivityDate($availability->c, $profileFieldValue, $profileFieldShortName);
            }
            elseif($restrictionType == 'group') {
                $date = getGroupActivityDate($availability->c, $groupId);
            }
            return $date;
        }
    }
    return null;
}

/**
 * Format a date based on the current language.
 *
 * @param int $date The date to format as a timestamp.
 * @return string The formatted date.
 */
function formatDate($date) {

    $currentLang = current_language();

    // Set locale based on current language
    if ($currentLang == 'fr') {
        $locale = 'fr_FR';
    }
    else { // You can add your own language if you need in a else if statement
        $locale = 'en_US';
    }

    $formatter = new IntlDateFormatter(
        $locale,
        IntlDateFormatter::LONG, // Day, month and year
        IntlDateFormatter::SHORT, // Hours and minutes
        null,
        null
    );

    return $formatter->format($date);
}

/**
 * Update or create availability dates for an activity based on profile field conditions.
 *
 * @param int $activityId The ID of the activity.
 * @param string $profileFieldShortName The profile field short name to match.
 * @param string $profileFieldValue The profile field value to match.
 * @param int|null $startTimestampInSeconds The start date as a timestamp or null if not applicable.
 * @param int|null $endTimestampInSeconds The end date as a timestamp or null if not applicable.
 */
function updateOrCreateProfileDates($activityId, $profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds) {
    global $DB;

    $activity = $DB->get_record('course_modules', ['id' => $activityId], 'availability');

    if (isset($activity->availability)) {
        $availability = json_decode($activity->availability);

        // Update or create dates at the right place
        if (isset($availability->c) && count($availability->c) > 0) {
            $updated = updateDatesInProfileConditions($availability->c, $profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds);

            // Change the root operator to '|' if it's not already set
            if (isset($availability->op)) {
                $availability->op = '|';
            } else {
                $availability = (object)[
                    'op' => '|',
                    'c' => $availability->c
                ];
            }

            if (!$updated) {
                // If no dates were updated, create a new set of conditions at the root level
                $newCondition = createNewProfileCondition($profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds);
                $availability->c[] = $newCondition;
            }

            // Ensure show is false
            $availability->show = false;

            $DB->update_record('course_modules', (object)[
                'id' => $activityId,
                'availability' => json_encode($availability)
            ]);

        } else {
            // No conditions exist, create a new set
            $newCondition = createNewProfileCondition($profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds);
            $availability = (object)[
                'showc' => [false], // Ensure showc is false
                'c' => [$newCondition]
            ];

            $DB->update_record('course_modules', (object)[
                'id' => $activityId,
                'availability' => json_encode($availability)
            ]);
        }
    }

    else {
        // No conditions exist, create a new set
        $newCondition = createNewProfileCondition($profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds);
        $availability = (object)[
            'c' => [$newCondition],
            'show' => false // Ensure show is false
        ];
        $DB->update_record('course_modules', (object)[
            'id' => $activityId,
            'availability' => json_encode($availability)
        ]);
    }

}

/**
 * Update or create availability dates for an activity based on groups conditions.
 *
 * @param int $activityId The ID of the activity.
 * @param string $groupId The group id to match.
 * @param int|null $startTimestampInSeconds The start date as a timestamp or null if not applicable.
 * @param int|null $endTimestampInSeconds The end date as a timestamp or null if not applicable.
 */
function updateOrCreateGroupDates($activityId, $groupId, $startTimestampInSeconds, $endTimestampInSeconds) {
    global $DB;


    $activity = $DB->get_record('course_modules', ['id' => $activityId], 'availability');

    if (isset($activity->availability)) {
        $availability = json_decode($activity->availability);

        // Update or create dates at the right place
        if (isset($availability->c) && count($availability->c) > 0) {
            $updated = updateDatesInGroupConditions($availability->c, $groupId, $startTimestampInSeconds, $endTimestampInSeconds);

            // Change the root operator to '|' if it's not already set
            if (isset($availability->op)) {
                $availability->op = '|';
            } else {
                $availability = (object)[
                    'op' => '|',
                    'c' => $availability->c
                ];
            }

            if (!$updated) {

                // If no dates were updated, create a new set of conditions at the root level
                $newCondition = createNewGroupCondition($groupId, $startTimestampInSeconds, $endTimestampInSeconds);
                $availability->c[] = $newCondition;
            }

            // Ensure show is false
            $availability->show = false;

            $DB->update_record('course_modules', (object)[
                'id' => $activityId,
                'availability' => json_encode($availability)
            ]);

        } else {

            // No conditions exist, create a new set
            $newCondition = createNewGroupCondition($groupId, $startTimestampInSeconds, $endTimestampInSeconds);
            $availability = (object)[
                'showc' => [false], // Ensure showc is false
                'c' => [$newCondition]
            ];

            $DB->update_record('course_modules', (object)[
                'id' => $activityId,
                'availability' => json_encode($availability)
            ]);
        }
    }

    else {

        // No conditions exist, create a new set
        $newCondition = createNewGroupCondition($groupId, $startTimestampInSeconds, $endTimestampInSeconds);
        $availability = (object)[
            'c' => [$newCondition],
            'show' => false // Ensure show is false
        ];
        $DB->update_record('course_modules', (object)[
            'id' => $activityId,
            'availability' => json_encode($availability)
        ]);

    }
}

/**
 * Update existing date conditions or add new date conditions if they do not exist.
 *
 * @param array &$conditions The conditions to update.
 * @param string $profileFieldShortName The profile field short name to match.
 * @param string $profileFieldValue The profile field value to match.
 * @param int|null $startTimestampInSeconds The start date as a timestamp or null if not applicable.
 * @param int|null $endTimestampInSeconds The end date as a timestamp or null if not applicable.
 * @return bool True if conditions were updated, false otherwise.
 */
function updateDatesInProfileConditions(&$conditions, $profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds) {
    foreach ($conditions as &$condition) {

        if (isset($condition->type) && $condition->type === 'profile' && $condition->cf === $profileFieldShortName && $condition->v === $profileFieldValue) {
            $startDateUpdated = false;
            $endDateUpdated = false;

            // Check for existing date conditions at the same level
            foreach ($conditions as &$subCondition) {

                if (isset($subCondition->type) && $subCondition->type === 'date') {

                    if ($subCondition->d === '>=' && $startTimestampInSeconds !== null) {
                        $subCondition->t = $startTimestampInSeconds;
                        $startDateUpdated = true;

                    } elseif ($subCondition->d === '<' && $endTimestampInSeconds !== null) {
                        $subCondition->t = $endTimestampInSeconds;
                        $endDateUpdated = true;
                    }
                }
            }

            // If one of the dates wasn't updated, create it
            if (!$startDateUpdated && $startTimestampInSeconds !== null) {
                $conditions[] = (object)[
                    'type' => 'date',
                    'd' => '>=',
                    't' => $startTimestampInSeconds
                ];
            }

            if (!$endDateUpdated && $endTimestampInSeconds !== null) {
                $conditions[] = (object)[
                    'type' => 'date',
                    'd' => '<',
                    't' => $endTimestampInSeconds
                ];
            }

            return true;

        } elseif (isset($condition->c)) {
            // Recurse into nested conditions
            if (updateDatesInProfileConditions($condition->c, $profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Update existing date conditions or add new date conditions if they do not exist.
 *
 * @param array &$conditions The conditions to update.
 * @param string $groupId The group id to match.
 * @param int|null $startTimestampInSeconds The start date as a timestamp or null if not applicable.
 * @param int|null $endTimestampInSeconds The end date as a timestamp or null if not applicable.
 * @return bool True if conditions were updated, false otherwise.
 */
function updateDatesInGroupConditions(&$conditions, $groupId, $startTimestampInSeconds, $endTimestampInSeconds) {

    foreach ($conditions as &$condition) {

        if (isset($condition->type) && $condition->type === 'group' && $condition->id == $groupId) {
            $startDateUpdated = false;
            $endDateUpdated = false;

            // Check for existing date conditions at the same level
            foreach ($conditions as &$subCondition) {

                if (isset($subCondition->type) && $subCondition->type === 'date') {

                    if ($subCondition->d === '>=' && $startTimestampInSeconds !== null) {
                        $subCondition->t = $startTimestampInSeconds;
                        $startDateUpdated = true;

                    } elseif ($subCondition->d === '<' && $endTimestampInSeconds !== null) {
                        $subCondition->t = $endTimestampInSeconds;
                        $endDateUpdated = true;
                    }
                }
            }

            // If one of the dates wasn't updated, create it
            if (!$startDateUpdated && $startTimestampInSeconds != null) {
                $conditions[] = (object)[
                    'type' => 'date',
                    'd' => '>=',
                    't' => $startTimestampInSeconds
                ];
            }

            if (!$endDateUpdated && $endTimestampInSeconds != null) {
                $conditions[] = (object)[
                    'type' => 'date',
                    'd' => '<',
                    't' => $endTimestampInSeconds
                ];
            }

            return true;

        } elseif (isset($condition->c)) {
            // Recurse into nested conditions
            if (updateDatesInGroupConditions($condition->c, $groupId, $startTimestampInSeconds, $endTimestampInSeconds)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Create a new condition object based on profile field and date conditions.
 *
 * @param string $profileFieldShortName The profile field short name to match.
 * @param string $profileFieldValue The profile field value to match.
 * @param int|null $startTimestampInSeconds The start date as a timestamp or null if not applicable.
 * @param int|null $endTimestampInSeconds The end date as a timestamp or null if not applicable.
 * @return object The new condition object.
 */
function createNewProfileCondition($profileFieldShortName, $profileFieldValue, $startTimestampInSeconds, $endTimestampInSeconds) {
    $conditions = [
        (object)[
            'type' => 'profile',
            'cf' => $profileFieldShortName,
            'op' => 'isequalto',
            'v' => $profileFieldValue
        ]
    ];

    if ($startTimestampInSeconds !== null) {
        $conditions[] = (object)[
            'type' => 'date',
            'd' => '>=',
            't' => $startTimestampInSeconds
        ];
    }

    if ($endTimestampInSeconds !== null) {
        $conditions[] = (object)[
            'type' => 'date',
            'd' => '<',
            't' => $endTimestampInSeconds
        ];
    }

    return (object)['op' => '&', 'c' => $conditions];
}

/**
 * Create a new condition object based on groupId and date conditions.
 *
 * @param string $groupId The group id to match.
 * @param int|null $startTimestampInSeconds The start date as a timestamp or null if not applicable.
 * @param int|null $endTimestampInSeconds The end date as a timestamp or null if not applicable.
 * @return object The new condition object.
 */
function createNewGroupCondition($groupId, $startTimestampInSeconds, $endTimestampInSeconds) {

    $conditions = [
        (object)[
            'type' => 'group',
            'id' => $groupId
        ]
    ];

    if ($startTimestampInSeconds !== null) {
        $conditions[] = (object)[
            'type' => 'date',
            'd' => '>=',
            't' => $startTimestampInSeconds
        ];
    }

    if ($endTimestampInSeconds !== null) {
        $conditions[] = (object)[
            'type' => 'date',
            'd' => '<',
            't' => $endTimestampInSeconds
        ];
    }

    return (object)['op' => '&', 'c' => $conditions];
}