/**
 * Handle AJAX error by logging the error details.
 *
 * @param {XMLHttpRequest} xhr - The XMLHttpRequest object.
 * @param {string} status - The status of the AJAX request.
 * @param {Error} error - The error object.
 */
function handleAjaxError(xhr, status, error) {
    console.error(xhr, status, error);
}
  
/**
 * Build options for a select element and update it.
 *
 * @param {string} selectId - The ID of the select element to be updated.
 * @param {string} firstOptionText - The text for the first (default) option.
 * @param {Array} items - An array of items to populate the select element.
 * @param {function} getValue - A function to get the value for each option from an item.
 * @param {function} getText - A function to get the text for each option from an item.
 * @param {string} [additionalValue=''] - Additional value to append to each option value (optional).
 * @param {string} [selectedValue=null] - The value of the option to be selected (optional).
 */
function buildSelectOptions(selectId, firstOptionText, items, getValue, getText, additionalValue = '', selectedValue = null) {
    const select = $(selectId);
    select.empty();

    // Helper function to process additionalValue
    function getAdditionalValue(item) {
        return typeof additionalValue === 'function' ? additionalValue(item) : additionalValue;
    }

    // First option
    select.append($('<option>', {
        value: -1 + (additionalValue ? '/' + additionalValue : ''),
        text: firstOptionText
    }));

    // All the items
    items.forEach(function(item) {
        select.append($('<option>', {
            value: getValue(item) + (additionalValue ? '/' + getAdditionalValue(item) : ''),
            text: getText(item)
        }));
    });

    // Select the actual value if provided
    if (selectedValue !== null) {
        select.val(selectedValue);
    }
}

/**
 * Displays a list of activites.
 *
 * @param {Object} response - The object containing activites details.
 * @param {HTMLElement} activitiesContainer - The HTML element to display activities in.
 * @param {HTMLElement} sectionsActivitiesNumber - The HTML element to display number of sections and activities of this course.
 */
function displayActivities(response, activitiesContainer, sectionsActivitiesNumber) {

    let numberOfActivities = 0;

    for (const sectionName in response.activities) {

        if (response.activities.hasOwnProperty(sectionName)) {

            const sectionContainer = document.createElement('div');
            sectionContainer.classList.add('course-section');

            const sectionHeader = document.createElement('div');
            sectionHeader.classList.add('section-header');
            
            const sectionTitle = document.createElement('h3');
            sectionTitle.textContent = sectionName;

            // Section check
            const sectionCheck = document.createElement('div');
            sectionCheck.classList.add('section-check-container');

            const sectionCheckText = document.createElement('div');
            const sectionCheckCheckbox = document.createElement('input');
            sectionCheckCheckbox.type = "checkbox";
            sectionCheckCheckbox.disabled = true;
            sectionCheckCheckbox.classList.add('section-checkbox');
            sectionCheck.appendChild(sectionCheckText);
            sectionCheck.appendChild(sectionCheckCheckbox);

            const toggleIcon = document.createElement('img');
            toggleIcon.src = response.hide;
            toggleIcon.classList.add('toggle-icon');
            toggleIcon.alt = response.show_hide_message;
            toggleIcon.title = response.show_hide_message;

            // Add event listener to toggle visibility
            toggleIcon.addEventListener('click', function() {
                ul.classList.toggle('hidden');
                sectionCheck.classList.toggle('hidden');

                toggleIcon.src = ul.classList.contains('hidden') ? response.show : response.hide;
            });

            // Number of selected activities per section badge
            const notificationBadge = document.createElement('span');
            notificationBadge.classList.add('notification-badge');
            notificationBadge.style.display = 'none';

            sectionHeader.appendChild(toggleIcon);
            sectionHeader.appendChild(sectionTitle);

            const sectionContainer2 = document.createElement('div');
            sectionContainer2.classList.add('section-item', 'activities-container-inline');

            const ul = document.createElement('ul');
            ul.classList.add('activity-list');

            let activityInThisSection = 0;

            response.activities[sectionName].forEach(function(activity) {

                numberOfActivities++;
                activityInThisSection++;

                const li = document.createElement('li');
                li.classList.add('activity-list-li');
                li.setAttribute('data-activity-id', activity.id);

                const checkbox = document.createElement('input');
                checkbox.type = "checkbox";
                checkbox.value = activity.id;
                checkbox.disabled = true;
                checkbox.classList.add('activity-checkbox');

                const iconImg = document.createElement('img');
                iconImg.src = activity.icon;
                iconImg.alt = activity.type;
                iconImg.title = activity.type;
                iconImg.style.padding = "10px";

                const activityLink = document.createElement('a');
                activityLink.href = activity.link;
                activityLink.target = '_blank';
                activityLink.textContent = `${activity.fullname} (${activity.type})`;

                li.appendChild(checkbox);
                li.appendChild(iconImg);
                li.appendChild(activityLink);
                ul.appendChild(li);
            });

            // AJAX request to get lang string
            $.ajax({
                url: 'ajax/lang.php',
                type: 'POST',
                data: {},
                success: function(message) {
                    sectionCheckText.textContent = message.checkAll + '('  + activityInThisSection + ')';
                },
                error: handleAjaxError
            });

            sectionContainer2.appendChild(notificationBadge);
            sectionContainer2.appendChild(sectionHeader);
            sectionContainer2.appendChild(sectionCheck);
            sectionContainer2.appendChild(ul);
            sectionContainer.appendChild(sectionContainer2);
            activitiesContainer.appendChild(sectionContainer);
        }
    }

    // Calculate the number of sections and activities
    const numSections = Object.keys(response.activities).length;

    // Determine the appropriate strings for sections and activities
    const sectionStr = numSections > 1 ? response.plural_sections : response.singular_section;
    const activityStr = numberOfActivities > 1 ? response.plural_activities : response.singular_activity;

    // Construct the display text
    sectionsActivitiesNumber.textContent = `${numSections} ${sectionStr} ${numberOfActivities} ${activityStr}`;

    // Update badges initially
    updateBadges();

    // Add change event listener to update badges when checkboxes are checked/unchecked
    document.querySelectorAll('.activity-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', updateBadges);
    });
}

/**
 * Updates the notification badges based on checked activities.
 */
function updateBadges() {
    document.querySelectorAll('.course-section').forEach(function(section) {
        const checkedCount = section.querySelectorAll('.activity-checkbox:checked').length;
        const badge = section.querySelector('.notification-badge');

        if (checkedCount > 0) {
            badge.textContent = checkedCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

/**
 * Handles profile field value change.
 *
 * @param {HTMLElement} activitiesContainer - The HTML element containing activities.
 * @param {string} profileFieldValue - The value of the profile field.
 * @param {string} profileFieldShortName - The short name of the profile field.
 * @param {string} profileFieldId - The ID of the profile field.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function handleProfileFieldChange(activitiesContainer, profileFieldValue, profileFieldShortName, profileFieldId, uncheckAllButton, checkboxCounter) {
    const activityItems = activitiesContainer.querySelectorAll('li.activity-list-li');
    const sectionCheckBox = activitiesContainer.querySelectorAll('.section-checkbox')
    const numberOfDatesText = document.getElementById('dates-filters');
    let numberOfDates = 0;

    // Enable all section checkbox
    sectionCheckBox.forEach(function(checkbox) {
        checkbox.disabled = false;
        addSectionCheckboxChangeListener(checkbox, activityItems, checkboxCounter);
    });

    activityItems.forEach(function(item) {

        // Enable checkbox
        const checkbox = item.querySelector('.activity-checkbox');
        checkbox.disabled = false;

        addCheckboxChangeListener(checkbox,  profileFieldId, profileFieldValue, activityItems, uncheckAllButton, checkboxCounter);

        const activityId = item.getAttribute('data-activity-id');
        $.ajax({
            url: 'ajax/profile_availability_dates.php',
            type: 'POST',
            data: { activityId: activityId, profileFieldValue: profileFieldValue, profileFieldShortName: profileFieldShortName },
            success: function(response) {

                if (response.startDate || response.endDate) {
                    let startDateText = '';
                    let endDateText = '';

                    if (response.startDate) {
                        startDateText = response.startDateMessage + response.startDate;
                        numberOfDates++;
                    }
                    if (response.endDate) {
                        endDateText = response.endDateMessage + response.endDate;
                        numberOfDates++;
                    }
    
                    let startDateElement = item.querySelector('.date-info.green');
                    let endDateElement = item.querySelector('.date-info.red');

                    if (!startDateElement) {
                        startDateElement = document.createElement('p');
                        startDateElement.classList.add('date-info');
                        startDateElement.classList.add('green');
                        item.appendChild(startDateElement);
                    }
                    startDateElement.textContent = startDateText;

                    if (!endDateElement) {
                        endDateElement = document.createElement('p');
                        endDateElement.classList.add('date-info');
                        endDateElement.classList.add('red');
                        item.appendChild(endDateElement);
                    }
                    endDateElement.textContent = endDateText;
                }

                numberOfDatesText.innerHTML = numberOfDates === 1
                ? numberOfDates + response.date
                : numberOfDates > 1
                ? numberOfDates + response.dates
                : response.none;

            },
            error: handleAjaxError
        });
    });
}

/**
 * Handles group value change.
 *
 * @param {HTMLElement} activitiesContainer - The HTML element containing activities.
 * @param {string} groupId - The ID of the group.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function handleGroupChange(activitiesContainer, groupId, uncheckAllButton, checkboxCounter) {
        
    const activityItems = activitiesContainer.querySelectorAll('li.activity-list-li');
    const sectionCheckBox = activitiesContainer.querySelectorAll('.section-checkbox')
    const numberOfDatesText = document.getElementById('dates-filters');
    let numberOfDates = 0;

    // Enable all section checkbox
    sectionCheckBox.forEach(function(checkbox) {
        checkbox.disabled = false;
        addSectionCheckboxChangeListener(checkbox, activityItems, checkboxCounter);
    });

    activityItems.forEach(function(item) {

        // Enable checkbox
        const checkbox = item.querySelector('.activity-checkbox');
        checkbox.disabled = false;

        //addCheckboxChangeListener(checkbox,  profileFieldId, profileFieldValue, activityItems, uncheckAllButton, checkboxCounter);
        addCheckboxChangeListener2(checkbox,  groupId, activityItems, uncheckAllButton, checkboxCounter);

        const activityId = item.getAttribute('data-activity-id');
        $.ajax({
            url: 'ajax/group_availability_dates.php',
            type: 'POST',
            data: { activityId: activityId, groupId: groupId },
            success: function(response) {

                if (response.startDate || response.endDate) {
                    let startDateText = '';
                    let endDateText = '';

                    if (response.startDate) {
                        startDateText = response.startDateMessage + response.startDate;
                        numberOfDates++;
                    }
                    if (response.endDate) {
                        endDateText = response.endDateMessage + response.endDate;
                        numberOfDates++;
                    }
    
                    let startDateElement = item.querySelector('.date-info.green');
                    let endDateElement = item.querySelector('.date-info.red');

                    if (!startDateElement) {
                        startDateElement = document.createElement('p');
                        startDateElement.classList.add('date-info');
                        startDateElement.classList.add('green');
                        item.appendChild(startDateElement);
                    }
                    startDateElement.textContent = startDateText;

                    if (!endDateElement) {
                        endDateElement = document.createElement('p');
                        endDateElement.classList.add('date-info');
                        endDateElement.classList.add('red');
                        item.appendChild(endDateElement);
                    }
                    endDateElement.textContent = endDateText;
                }

                numberOfDatesText.innerHTML = numberOfDates === 1
                ? numberOfDates + response.date
                : numberOfDates > 1
                ? numberOfDates + response.dates
                : response.none;

            },
            error: handleAjaxError
        });
    });
}

// Gestionnaire pour stocker les écouteurs d'événements
const eventListeners = new WeakMap();
const eventListenersSection = new WeakMap();

/**
 * Adds a change event listener to an activity checkbox.
 *
 * @param {HTMLInputElement} checkbox - The activity checkbox element.
 * @param {string} profileFieldId - The ID of the profile field.
 * @param {string} profileFieldValue - The value of the profile field.
 * @param {NodeList} activityItems - A list of activity items.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function addCheckboxChangeListener(checkbox, profileFieldId, profileFieldValue, activityItems, uncheckAllButton, checkboxCounter) {
    
    // Vérifier si l'écouteur d'événements est déjà attaché
    if (!eventListeners.has(checkbox)) {
        const listener = event => handleCheckboxChange(event, profileFieldId, profileFieldValue, activityItems, uncheckAllButton, checkboxCounter);
        checkbox.addEventListener('change', listener);

        // Stocker l'écouteur dans le gestionnaire
        eventListeners.set(checkbox, listener);
    }
}

/**
 * Adds a change event listener to an activity checkbox.
 *
 * @param {HTMLInputElement} checkbox - The activity checkbox element.
 * @param {string} groupId - The ID of the group.
 * @param {NodeList} activityItems - A list of activity items.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function addCheckboxChangeListener2(checkbox, groupId, activityItems, uncheckAllButton, checkboxCounter) {
    
    // Vérifier si l'écouteur d'événements est déjà attaché
    if (!eventListeners.has(checkbox)) {
        const listener = event => handleCheckboxChange2(event, groupId, activityItems, uncheckAllButton, checkboxCounter);
        checkbox.addEventListener('change', listener);

        // Stocker l'écouteur dans le gestionnaire
        eventListeners.set(checkbox, listener);
    }
}

/**
 * Removes the change event listener to an activity checkbox.
 *
 * @param {HTMLInputElement} checkbox - The activity checkbox element.
 */
function removeCheckboxChangeListener(checkbox) {

    // Récupérer et supprimer l'écouteur d'événements si attaché
    if (eventListeners.has(checkbox)) {
        const listener = eventListeners.get(checkbox);
        checkbox.removeEventListener('change', listener);

        // Supprimer l'écouteur du gestionnaire
        eventListeners.delete(checkbox);
    }
}

/**
 * Adds a change event listener to a section checkbox.
 *
 * @param {HTMLInputElement} checkbox - The section checkbox element.
 * @param {NodeList} activityItems - A list of activity items.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function addSectionCheckboxChangeListener(checkbox, activityItems, checkboxCounter) {
    
    // Vérifier si l'écouteur d'événements est déjà attaché
    if (!eventListenersSection.has(checkbox)) {
        const listener = event => handleSectionCheckboxChange(event, activityItems, checkboxCounter);
        checkbox.addEventListener('change', listener);

        // Stocker l'écouteur dans le gestionnaire
        eventListenersSection.set(checkbox, listener);
    }
}

/**
 * Removes the change event listener to a section checkbox.
 *
 * @param {HTMLInputElement} checkbox - The section checkbox element.
 */
function removeSectionCheckboxChangeListener(checkbox) {

    // Récupérer et supprimer l'écouteur d'événements si attaché
    if (eventListenersSection.has(checkbox)) {
        const listener = eventListenersSection.get(checkbox);
        checkbox.removeEventListener('change', listener);

        // Supprimer l'écouteur du gestionnaire
        eventListenersSection.delete(checkbox);
    }
}

/**
 * Handles the change event of an activity checkbox.
 * Updates the localStorage, checkbox counter, and form visibility based on checkbox states.
 *
 * @param {Event} event - The change event triggered by an activity checkbox.
 * @param {string} profileFieldId - The ID of the profile field.
 * @param {string} profileFieldValue - The value of the profile field.
 * @param {NodeList} activityItems - A list of activity items.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function handleCheckboxChange(event, profileFieldId, profileFieldValue, activityItems, uncheckAllButton, checkboxCounter) {
    const checkbox = event.target;
    let count = 0;

    if (checkbox.checked) {
        localStorage.setItem('activitiesAtLeastOneChecked', 'true');
    } else {
        // Check all other checkboxes, and hide form if all are unchecked
        let checked = false;
        activityItems.forEach(function(item2) {
            const checkbox2 = item2.querySelector('.activity-checkbox');
            if (checkbox2.checked) {
                checked = true;
            }
        });

        if (!checked) {
            localStorage.setItem('activitiesAtLeastOneChecked', 'false');
            $('#hidden-form-date').hide();
            uncheckAllButton.disabled = true;

            // Uncheck checkbox 'Check all'
            const section = checkbox.closest('.course-section');
            const sectionCheckbox = section.querySelector('.section-checkbox');
            sectionCheckbox.checked = false;

            // Remove form label
            removeFormLabel(document.getElementById('form-label'));
        }
    }

    // Update checkbox counter
    activityItems.forEach(function(checkbox) {
        if (checkbox.querySelector('.activity-checkbox').checked) {
            count++;
        }
    });
    updateCheckboxCounter(count, checkboxCounter);

    if (localStorage.getItem('activitiesAtLeastOneChecked') === 'true' && localStorage.getItem('activitiesAtLeastOneChecked') !== null) {
        // If form not already visible
        if (!$('#hidden-form-date').is(':visible')) {
            // Show form and set label
            $.ajax({
                url: 'ajax/form_label.php',
                type: 'POST',
                data: { profileFieldId: profileFieldId, type: 'profile' },
                success: function(response) {
                    // Show form
                    $('#hidden-form-date').show();
                    uncheckAllButton.disabled = false;

                    // Vérifier si les éléments existent déjà
                    let typeP = document.querySelector('#form-label p.type-message');
                    let valueP = document.querySelector('#form-label p.value-name');

                    // Si les éléments n'existent pas, les créer et les ajouter
                    if (!typeP) {
                        typeP = document.createElement('p');
                        typeP.textContent = response.typeMessage + ' : ' + response.dateMessage;
                        typeP.classList.add('type-message'); // Ajouter une classe pour une identification ultérieure
                        document.getElementById('form-label').appendChild(typeP);
                    }

                    if (!valueP) {
                        valueP = document.createElement('p');
                        valueP.textContent = response.valueName + ' : ' + profileFieldValue;
                        valueP.classList.add('value-name'); // Ajouter une classe pour une identification ultérieure
                        document.getElementById('form-label').appendChild(valueP);
                    }
                },
                error: handleAjaxError
            });
        }
    }
}

/**
 * Handles the change event of an activity checkbox.
 * Updates the localStorage, checkbox counter, and form visibility based on checkbox states.
 *
 * @param {Event} event - The change event triggered by an activity checkbox.
 * @param {string} groupId - The ID of the group.
 * @param {NodeList} activityItems - A list of activity items.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function handleCheckboxChange2(event, groupId, activityItems, uncheckAllButton, checkboxCounter) {
    const checkbox = event.target;
    let count = 0;

    if (checkbox.checked) {
        localStorage.setItem('activitiesAtLeastOneChecked', 'true');
    } else {
        // Check all other checkboxes, and hide form if all are unchecked
        let checked = false;
        activityItems.forEach(function(item2) {
            const checkbox2 = item2.querySelector('.activity-checkbox');
            if (checkbox2.checked) {
                checked = true;
            }
        });

        if (!checked) {
            localStorage.setItem('activitiesAtLeastOneChecked', 'false');
            $('#hidden-form-date').hide();
            uncheckAllButton.disabled = true;

            // Uncheck checkbox 'Check all'
            const section = checkbox.closest('.course-section');
            const sectionCheckbox = section.querySelector('.section-checkbox');
            sectionCheckbox.checked = false;

            // Remove form label
            removeFormLabel(document.getElementById('form-label'));
        }
    }

    // Update checkbox counter
    activityItems.forEach(function(checkbox) {
        if (checkbox.querySelector('.activity-checkbox').checked) {
            count++;
        }
    });
    updateCheckboxCounter(count, checkboxCounter);

    if (localStorage.getItem('activitiesAtLeastOneChecked') === 'true' && localStorage.getItem('activitiesAtLeastOneChecked') !== null) {
        // If form not already visible
        if (!$('#hidden-form-date').is(':visible')) {
            // Show form and set label
            $.ajax({
                url: 'ajax/form_label.php',
                type: 'POST',
                data: { groupId: groupId, type: 'group' },
                success: function(response) {
                    // Show form
                    $('#hidden-form-date').show();
                    uncheckAllButton.disabled = false;

                    // Vérifier si les éléments existent déjà
                    let typeP = document.querySelector('#form-label p.type-message');
                    let valueP = document.querySelector('#form-label p.value-name');

                    // Si les éléments n'existent pas, les créer et les ajouter
                    if (!typeP) {
                        typeP = document.createElement('p');
                        typeP.textContent = response.typeMessage + ' : ' + response.dateMessage;
                        typeP.classList.add('type-message'); // Ajouter une classe pour une identification ultérieure
                        document.getElementById('form-label').appendChild(typeP);
                    }

                    if (!valueP) {
                        valueP = document.createElement('p');
                        valueP.textContent = response.group + ' : ' + response.valueName;
                        valueP.classList.add('value-name'); // Ajouter une classe pour une identification ultérieure
                        document.getElementById('form-label').appendChild(valueP);
                    }
                },
                error: handleAjaxError
            });
        }
    }
}

/**
 * Handles the change event of a section checkbox.
 * Updates the states of activity checkboxes within the section and the checkbox counter.
 *
 * @param {Event} event - The change event triggered by a section checkbox.
 * @param {NodeList} activityItems - A list of activity items.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function handleSectionCheckboxChange(event, activityItems, checkboxCounter) {
    const checkbox = event.target;
    const section = checkbox.closest('.course-section');
    let count = 0;

    if (section) {
        const activityCheckboxes = section.querySelectorAll('.activity-checkbox');
        activityCheckboxes.forEach(function (activityCheckbox) {
            // Check or uncheck the checkbox
            activityCheckbox.checked = checkbox.checked;
            
            // Dispatch the change event to trigger any attached event listeners
            const changeEvent = new Event('change', { bubbles: true });
            activityCheckbox.dispatchEvent(changeEvent);
        });

        activityItems.forEach(function(item) {
            if (item.querySelector('.activity-checkbox').checked) {
                count++;
            }
        });
        updateCheckboxCounter(count, checkboxCounter);
    }
}

/**
 * Resets all activities and the form to their initial state.
 *
 * @param {HTMLElement} activitiesContainer - The container element for activities.
 * @param {HTMLElement} numberOfDates - The element displaying the number of dates.
 * @param {HTMLElement} formLabel - The element displaying the form label.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function resetActivitiesAndForm(activitiesContainer, numberOfDates, formLabel, checkboxCounter) {

    localStorage.setItem('activitiesAtLeastOneChecked', 'false');

    const activityItems = activitiesContainer.querySelectorAll('li.activity-list-li');
    const sectionCheckBox = activitiesContainer.querySelectorAll('.section-checkbox')

    // Enable all section checkbox
    sectionCheckBox.forEach(function(checkbox) {
        checkbox.disabled = true;
        checkbox.checked = false;
    });

    activityItems.forEach(function(item) {

        // Disable and uncheck checkbox
        let checkbox = item.querySelector('.activity-checkbox');
        checkbox.disabled = true;
        checkbox.checked = false;

        removeCheckboxChangeListener(checkbox);

        // Remove dates
        let startDateElement = item.querySelector('.green');
        let endDateElement = item.querySelector('.red');

        if (startDateElement) {
            startDateElement.remove();
        }
        if (endDateElement) {
            endDateElement.remove();
        }
    });

    // Remove number of dates
    numberOfDates.innerHTML = '';

    // Remove form label
    removeFormLabel(formLabel);

    // Reset badges and counter
    updateCheckboxCounter(0, checkboxCounter);
    updateBadges();
}

/**
 * Updates the checkbox counter element with the current count.
 *
 * @param {number} count - The current count of checked checkboxes.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function updateCheckboxCounter(count, checkboxCounter) {

    // AJAX request to get the lang string
    $.ajax({
        url: 'ajax/lang.php',
        type: 'POST',
        data: { },
        success: function(message) {
            
            if(count > 1) {
                checkboxCounter.textContent = count + message.plural;
            }
            else if (count == 0) {
                checkboxCounter.textContent = message.none;
            }
            else { //1
                checkboxCounter.textContent = count + message.singular;
            }
            
        },
        error: handleAjaxError
    });

}

/**
 * Handles the cancellation of the form, resetting all checkboxes and form elements.
 *
 * @param {HTMLElement} activities - The container element for activities.
 * @param {HTMLElement} uncheckAllButton - The button element to uncheck all activities.
 * @param {HTMLElement} checkboxCounter - The element displaying the checkbox counter.
 */
function cancelForm(activities, uncheckAllButton, checkboxCounter) {
    // Hide form
    $('#hidden-form-date').hide();
    uncheckAllButton.disabled = true;

    // Remove form label
    removeFormLabel(document.getElementById('form-label'));

    const sectionCheckBox = activities.querySelectorAll('.section-checkbox')

    // Unchecked all section checkbox
    sectionCheckBox.forEach(function(checkbox) {
        checkbox.checked = false;
    });

    // Unchecked all activity checkbox
    const activityItems = activities.querySelectorAll('li.activity-list-li');
    activityItems.forEach(function(item) {
        const checkbox = item.querySelector('.activity-checkbox');
        checkbox.checked = false;
    });

    // Update local storage item
    localStorage.setItem('activitiesAtLeastOneChecked', 'false');

    // Reset badges and counter
    updateCheckboxCounter(0, checkboxCounter);
    updateBadges();
}

/**
 * Displays a notification message with specified styling and duration.
 *
 * @param {string} message - The message to display.
 * @param {string} backgroundColor - The background color of the notification.
 * @param {string} color - The text color of the notification.
 * @param {string} border_color - The border color of the notification.
 * @param {number} duration - The duration to display the notification in milliseconds.
 */
function displayNotif(message, backgroundColor, color, border_color, duration) {

    const notificationContainer = document.getElementById('notification-container');

    // Check if a notification is already present
    if (notificationContainer.childElementCount > 0) {
        return; // Exit the function if there's already a notification
    }

    notificationContainer.style.backgroundColor = backgroundColor;
    notificationContainer.style.color = color;
    notificationContainer.style.borderColor = border_color;

    const notification = document.createElement('div');
    notification.textContent = message;

    notificationContainer.appendChild(notification);
    notificationContainer.style.bottom = '-4em';
    notificationContainer.style.opacity = '0';

    setTimeout(function () {
        notificationContainer.style.bottom = '2em';
        notificationContainer.style.opacity = '1';
    }, 10);

    setTimeout(function () {
        notificationContainer.style.bottom = '-4em';
        notificationContainer.style.opacity = '0';
        setTimeout(function () {
            notificationContainer.removeChild(notification);
        }, 500);
    }, duration);
}

/**
 * Removes all paragraph elements from the form label.
 *
 * @param {HTMLElement} formLabel - The element containing form labels.
 */
function removeFormLabel(formLabel) {
    let paragraphs = formLabel.querySelectorAll('p');

    paragraphs.forEach(function(paragraph) {
        paragraph.remove();
    });
}

/**
 * Redirects to a course page based on the course ID.
 *
 * @param {string} courseId - The ID of the course.
 */
function handleCourseRedirect(courseId) {
    return function() {
        
        // Get the course link
        $.ajax({
            url: 'ajax/course_link.php',
            type: 'POST',
            data: { courseId: courseId },
            success: function(link) {

                // Redirection
                window.open(link, '_blank');
            },
            error: handleAjaxError
        });
    };
}

/**
 * Refreshes the availability dates of activities based on profile field values.
 *
 * @param {HTMLElement} activitiesContainer - The container element for activities.
 * @param {string} profileFieldValue - The value of the profile field.
 * @param {string} profileFieldShortName - The short name of the profile field.
 */
function refreshProfileDates(activitiesContainer, profileFieldValue, profileFieldShortName) {
    const activityItems = activitiesContainer.querySelectorAll('li.activity-list-li');
    const numberOfDatesText = document.getElementById('dates-filters');
    let numberOfDates = 0;

    activityItems.forEach(function(item) {

        const activityId = item.getAttribute('data-activity-id');
        $.ajax({
            url: 'ajax/profile_availability_dates.php',
            type: 'POST',
            data: { activityId: activityId, profileFieldValue: profileFieldValue, profileFieldShortName: profileFieldShortName },
            success: function(response) {

                if (response.startDate || response.endDate) {
                    let startDateText = '';
                    let endDateText = '';

                    if (response.startDate) {
                        startDateText = response.startDateMessage + response.startDate;
                        numberOfDates++;
                    }
                    if (response.endDate) {
                        endDateText = response.endDateMessage + response.endDate;
                        numberOfDates++;
                    }
    
                    let startDateElement = item.querySelector('.date-info.green');
                    let endDateElement = item.querySelector('.date-info.red');

                    if (!startDateElement) {
                        startDateElement = document.createElement('p');
                        startDateElement.classList.add('date-info');
                        startDateElement.classList.add('green');
                        item.appendChild(startDateElement);
                    }
                    startDateElement.textContent = startDateText;

                    if (!endDateElement) {
                        endDateElement = document.createElement('p');
                        endDateElement.classList.add('date-info');
                        endDateElement.classList.add('red');
                        item.appendChild(endDateElement);
                    }
                    endDateElement.textContent = endDateText;
                }

                numberOfDatesText.innerHTML = numberOfDates === 1
                ? numberOfDates + response.date
                : numberOfDates > 1
                ? numberOfDates + response.dates
                : response.none;
            },
            error: handleAjaxError
        });
    });
}

/**
 * Refreshes the availability dates of activities based on group id.
 *
 * @param {HTMLElement} activitiesContainer - The container element for activities.
 * @param {string} groupId - The group id.
 */
function refreshGroupDates(activitiesContainer, groupId) {
    const activityItems = activitiesContainer.querySelectorAll('li.activity-list-li');
    const numberOfDatesText = document.getElementById('dates-filters');
    let numberOfDates = 0;

    activityItems.forEach(function(item) {

        const activityId = item.getAttribute('data-activity-id');
        $.ajax({
            url: 'ajax/group_availability_dates.php',
            type: 'POST',
            data: { activityId: activityId, groupId: groupId },
            success: function(response) {

                if (response.startDate || response.endDate) {
                    let startDateText = '';
                    let endDateText = '';

                    if (response.startDate) {
                        startDateText = response.startDateMessage + response.startDate;
                        numberOfDates++;
                    }
                    if (response.endDate) {
                        endDateText = response.endDateMessage + response.endDate;
                        numberOfDates++;
                    }
    
                    let startDateElement = item.querySelector('.date-info.green');
                    let endDateElement = item.querySelector('.date-info.red');

                    if (!startDateElement) {
                        startDateElement = document.createElement('p');
                        startDateElement.classList.add('date-info');
                        startDateElement.classList.add('green');
                        item.appendChild(startDateElement);
                    }
                    startDateElement.textContent = startDateText;

                    if (!endDateElement) {
                        endDateElement = document.createElement('p');
                        endDateElement.classList.add('date-info');
                        endDateElement.classList.add('red');
                        item.appendChild(endDateElement);
                    }
                    endDateElement.textContent = endDateText;
                }

                numberOfDatesText.innerHTML = numberOfDates === 1
                ? numberOfDates + response.date
                : numberOfDates > 1
                ? numberOfDates + response.dates
                : response.none;
            },
            error: handleAjaxError
        });
    });
}