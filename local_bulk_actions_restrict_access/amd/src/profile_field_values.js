$(document).ready(function() {

    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');
    const uncheckAllButton = document.getElementById('uncheck-all-button');

    $('#id_select_profile_field_values').on('change', function() { // Id of profile_field_values.php's form

        // Reset and clean
        resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
        $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');
        $('#hidden-form-date').hide();
        uncheckAllButton.disabled = true;

        // Get values
        const value = $(this).val();
        const values = value.split('/');
        const profileFieldValue = values[0];
        const profileFieldShortName = values[1];
        const profileFieldId = $('#id_select_profile_field').val();

        if(profileFieldValue != -1) {
            handleProfileFieldChange(activities, profileFieldValue, profileFieldShortName, profileFieldId, uncheckAllButton, checkboxCounter);
            $('#dates-counter-button-container').addClass('flex-container-dates-counter-button');

            const form = document.getElementById('date-time-selector-form');

            // Handle form cancel 
            const cancelButton = form.querySelector('#id_cancel');

            // When cancel, hide form and unchecked all checked activities
            $(cancelButton).off('click').on('click', function(event) {

                // To avoid redirection
                event.preventDefault();

                // Hide form and uncheck activity checkbox
                cancelForm(activities, uncheckAllButton, checkboxCounter);
            });

            // Handle form submission 
            $('#date-time-selector-form').off('submit').on('submit', function(event) {

                // To avoid redirection
                event.preventDefault();

                const formData = $(this).serializeArray();
                const data = {};
                $.each(formData, function() {
                    data[this.name] = this.value;
                });
            
                // Extract date and time components for start time
                const startDay = data['assesstimestart[day]'];
                const startMonth = data['assesstimestart[month]'] - 1; // JS months are 0-based
                const startYear = data['assesstimestart[year]'];
                const startHour = data['assesstimestart[hour]'];
                const startMinute = data['assesstimestart[minute]'];

                // Create a Date object for start time
                const startDate = new Date(startYear, startMonth, startDay, startHour, startMinute);

                // Get timestamp in milliseconds and convert to seconds
                const startTimestampInSeconds = Math.floor(startDate.getTime() / 1000);

                // Extract date and time components for end time
                const endDay = data['assesstimeend[day]'];
                const endMonth = data['assesstimeend[month]'] - 1; // JS months are 0-based
                const endYear = data['assesstimeend[year]'];
                const endHour = data['assesstimeend[hour]'];
                const endMinute = data['assesstimeend[minute]'];

                // Create a Date object for end time
                const endDate = new Date(endYear, endMonth, endDay, endHour, endMinute);

                // Get timestamp in milliseconds and convert to seconds
                const endTimestampInSeconds = Math.floor(endDate.getTime() / 1000);

                // Get error messages
                $.ajax({
                    url: 'ajax/lang.php',
                    type: 'POST',
                    data: {},
                    success: function(message) {

                        // Check if timestamps are equal or start timestamp is greater than end timestamp
                        if (startTimestampInSeconds === endTimestampInSeconds) {
                            displayNotif(message.same, '#f8d7da', '#721c24', '#f5c6cb',2000);
                            return;
                        } else if (startTimestampInSeconds > endTimestampInSeconds) {
                            displayNotif(message.greater, '#f8d7da', '#721c24', '#f5c6cb',2000);
                            return;
                        }  

                        const activityItems = activities.querySelectorAll('li.activity-list-li');

                        activityItems.forEach(function(item) {

                            const activityId = item.getAttribute('data-activity-id');
                            const checkbox = item.querySelector('.activity-checkbox');

                            if (checkbox.checked) {

                                $.ajax({
                                    url: 'ajax/update_profile_date.php',
                                    type: 'POST',
                                    data: { activityId: activityId, startTimestampInSeconds: startTimestampInSeconds, endTimestampInSeconds : endTimestampInSeconds, profileFieldShortName: profileFieldShortName, profileFieldValue: profileFieldValue },
                                    success: function(message) {
                                                        
                                        // Display success notif
                                        displayNotif(message, '#d4edda', '#155724', '#c3e6cb', 1000);

                                        // Refresh dates
                                        refreshProfileDates(activities, profileFieldValue, profileFieldShortName);
                
                                    },
                                    error: handleAjaxError
                                });
                            }
                        });
                    },
                    error: handleAjaxError
                });
            });
        }
    });

    uncheckAllButton.addEventListener('click', function() {
        cancelForm(activities, uncheckAllButton, checkboxCounter);
    });

});