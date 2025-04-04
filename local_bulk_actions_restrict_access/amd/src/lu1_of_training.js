$(document).ready(function() {

    // Variables
    const error = document.getElementById('error');
    const errorFilters = document.getElementById('error-filters');
    const sectionsActivitiesNumber = document.getElementById('sections-activities-number');
    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');

    $('#id_select_lu1_of_training').on('change', function() { // Id of lu1_of_training.php's form
  
        // Clean
        error.innerHTML = '';
        errorFilters.innerHTML = '';
        sectionsActivitiesNumber.innerHTML = '';
        activities.innerHTML = '';

        // Delete activity date
        resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
        $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');

        // Hide forms
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

        const value = $(this).val();

        // Split lu1Id and nextForm
        const values = value.split('/');
        const lu1Id = values[0];
        const nextForm = values[1];

        // Get trainingId
        const trainingId = $('#id_select_trainings_cohort').val();

        if(nextForm == 'course') {

            // Build the options of the form
            $.ajax({
                url: 'ajax/courses_of_lu.php',
                type: 'POST',
                data: { lu1Id: lu1Id, trainingId: trainingId },
                success: function(response) {

                    if(response.courses.length <= 0) {
                        error.innerHTML = response.error;
                    }
                    else {
                        buildSelectOptions(
                            '#id_select_course', 
                            response.message, 
                            response.courses, 
                            function(courses) { return courses.id; }, 
                            function(courses) { return courses.fullname; }, 
                        );
            
                        $('#hidden-form-4').show();
                    }
                    
                },
                error: handleAjaxError
        
            });
        }
        else {

            // Build the options of the form
            $.ajax({
                url: 'ajax/lu2_of_lu1.php',
                type: 'POST',
                data: { lu1Id: lu1Id, trainingId: trainingId },
                success: function(response) {
        
                    if(response.lu2.length <= 0) {
                        error.innerHTML = response.error;
                    }
                    else {
                        buildSelectOptions(
                            '#id_select_lu2_of_lu1', 
                            response.message, 
                            response.lu2, 
                            function(lu2) { return lu2.id; }, 
                            function(lu2) { return lu2.fullname; }, 
                        );
            
                        $('#hidden-form-3').show();
                    }
                    
                },
                error: handleAjaxError
        
            });
        }
        

    });
});