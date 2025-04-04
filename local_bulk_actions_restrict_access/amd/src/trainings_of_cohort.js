$(document).ready(function() {

    // Variables
    const error = document.getElementById('error');
    const errorFilters = document.getElementById('error-filters');
    const sectionsActivitiesNumber = document.getElementById('sections-activities-number');
    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');

    $('#id_select_trainings_cohort').on('change', function() { // Id of trainings_of_cohort.php's form
  
        // Clean
        error.innerHTML = '';
        errorFilters.innerHTML = '';
        sectionsActivitiesNumber.innerHTML = '';
        activities.innerHTML = '';
        
        // Delete activity date
        resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
        $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');

        // Hide forms
        $('#hidden-form-2').hide();
        $('#hidden-form-3').hide();
        $('#hidden-form-4').hide();
        $('#hidden-form-5').hide();
        $('#hidden-form-6').hide();
        $('#hidden-form-7').hide();
        $('#hidden-form-8').hide();
        $('#hidden-form-date').hide();

        $('#redirect-to-course').hide();
        $('#first-line').hide();
        $('#second-line').hide();

        // Get  trainingId
        const trainingId = $(this).val();

        // Build the options of the form
        $.ajax({
        url: 'ajax/lu1_of_training.php',
        type: 'POST',
        data: { trainingId: trainingId },
        success: function(response) {

            if(response.lu1.length <= 0) {
                error.innerHTML = response.error;
            }
            else {
                buildSelectOptions(
                    '#id_select_lu1_of_training', 
                    response.message, 
                    response.lu1, 
                    function(lu1) { return lu1.id; }, 
                    function(lu1) { return lu1.fullname; }, 
                    response.nextForm
                );
    
                $('#hidden-form-2').show();
            }
            
        },
        error: handleAjaxError

        });

    });
});