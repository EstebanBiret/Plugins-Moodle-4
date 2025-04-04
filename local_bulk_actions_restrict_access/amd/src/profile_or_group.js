$(document).ready(function() {

    // Variables
    const error = document.getElementById('error');
    const errorFilters = document.getElementById('error-filters');
    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');

    $('#id_filter_by').on('change', function() { // Id of profile_or_group.php's form
  
        // Clean
        error.innerHTML = '';
        errorFilters.innerHTML = '';

        // Delete activity date
        resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
        $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');

        // Hide forms
        $('#hidden-form-6').hide();
        $('#hidden-form-7').hide();
        $('#hidden-form-8').hide();
        $('#hidden-form-date').hide();

        // Get values
        const type = $(this).val();
        
        if(type == 'profile_field') {
            $('#hidden-form-6').show();

            // Reset the select field to its first option
            $('#id_select_profile_field').prop('selectedIndex', 0);
        }   
        
        else if(type == 'group'){

            // Build the options of the form
            $.ajax({
                url: 'ajax/groups.php',
                type: 'POST',
                data: { courseId:  $('#id_select_course').val() },
                success: function(response) {
                    
                    if(response.groups.length <= 0) {
                        errorFilters.innerHTML = response.error;
                    }
                    else {
                        buildSelectOptions(
                            '#id_select_group', 
                            response.message, 
                            response.groups, 
                            function(groups) { return groups.id; }, 
                            function(groups) { return groups.name; },
                        );

                        $('#hidden-form-8').show();
                    }
                
                },
                
                error: handleAjaxError

            });

        }     
        else { // Get error message from AJAX lang file
            $.ajax({
                url: 'ajax/lang.php',
                type: 'POST',
                data: {},
                success: function(response) {
                    errorFilters.innerHTML = response.noDataType;
                },
                
                error: handleAjaxError
            });
        }
    });
});