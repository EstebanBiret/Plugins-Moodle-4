$(document).ready(function() {

    // Variables
    const error = document.getElementById('error');
    const errorFilters = document.getElementById('error-filters');
    const sectionsActivitiesNumber = document.getElementById('sections-activities-number');
    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');
    const courseButton = document.getElementById('redirect-to-course');
    let currentCourseListener = null;

    $('#id_select_course').on('change', function() { // Id of courses_of_lu.php's form
  
        // Clean
        error.innerHTML = '';
        errorFilters.innerHTML = '';
        sectionsActivitiesNumber.innerHTML = '';
        activities.innerHTML = '';

        // Delete activity date
        resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
        $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');

        // Hide form
        $('#hidden-form-5').hide();
        $('#hidden-form-6').hide();
        $('#hidden-form-7').hide();
        $('#hidden-form-8').hide();
        $('#hidden-form-date').hide();

        $('#first-line').hide();
        $('#second-line').hide();
        $('#redirect-to-course').show();

        // Get courseId
        const courseId = $(this).val();

        if(courseId != -1) {

            if (currentCourseListener !== null) {
                courseButton.removeEventListener('click', currentCourseListener);
            }
            currentCourseListener = handleCourseRedirect(courseId);
            courseButton.addEventListener('click', currentCourseListener);
        }
        else {
            $('#redirect-to-course').hide();
        }

        // Build the options of the form
        $.ajax({
            url: 'ajax/activities_of_course.php',
            type: 'POST',
            data: { courseId: courseId },
            success: function(response) {

                if (response.activities && Object.keys(response.activities).length > 0) {

                    displayActivities(response, activities, sectionsActivitiesNumber);

                    $('#hidden-form-5').show();
                    $('#first-line').show();
                    $('#second-line').show();

                    // Réinitialiser le select au premier élément
                    $('#hidden-form-5 select').each(function() {
                        $(this).val($(this).find('option:first').val());
                    });
                } 
                
                else {
                    error.innerHTML = response.error;
                }
            },
            error: handleAjaxError
    
        });
    });
});