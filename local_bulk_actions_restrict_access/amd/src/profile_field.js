$(document).ready(function() {

    // Variables
    const error = document.getElementById('error');
    const errorFilters = document.getElementById('error-filters');
    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');

    $('#id_select_profile_field').on('change', function() { // Id of profile_field.php's form
  
        // Clean
        error.innerHTML = '';
        errorFilters.innerHTML = '';

        // Delete activity date
        resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
        $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');

        // Hide form
        $('#hidden-form-7').hide();
        $('#hidden-form-8').hide();
        $('#hidden-form-date').hide();

        // Get values
        const profileFieldId = $(this).val();
        const cohortId = $('#id_cohort').val();
        
        // Build the options of the form
        $.ajax({
            url: 'ajax/profile_field_values.php',
            type: 'POST',
            data: { cohortId: cohortId, profileFieldId: profileFieldId },
            success: function(response) {
                
                if(response.profileFieldValues.length <= 0 || profileFieldId == -1) {
                    errorFilters.innerHTML = response.error;
                }
                else {
                    buildSelectOptions(
                        '#id_select_profile_field_values', 
                        response.message, 
                        response.profileFieldValues, 
                        function(profileFieldValue) { return profileFieldValue.name; }, 
                        function(profileFieldValue) { return profileFieldValue.name; },
                        function(profileFieldValue) { return profileFieldValue.shortname; },
                    );

                    $('#hidden-form-7').show();
                }
            
            },
            
            error: handleAjaxError

        });
    });
});