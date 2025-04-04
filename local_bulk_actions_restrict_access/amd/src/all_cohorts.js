$(document).ready(function() {

    // Variables
    const error = document.getElementById('error');
    const errorFilters = document.getElementById('error-filters');
    const sectionsActivitiesNumber = document.getElementById('sections-activities-number');
    const activities = document.getElementById('activities');
    const numberOfDates = document.getElementById('dates-filters');
    const formLabel = document.getElementById('form-label');
    const checkboxCounter = document.getElementById('checkbox-counter');

    $('#id_cohort').on('change', function() { // Id of all_cohorts.php's form
  
      // Clean
      error.innerHTML = '';
      errorFilters.innerHTML = '';
      sectionsActivitiesNumber.innerHTML = '';
      activities.innerHTML = '';
  
      // Delete activity date
      resetActivitiesAndForm(activities, numberOfDates, formLabel, checkboxCounter);
      $('#dates-counter-button-container').removeClass('flex-container-dates-counter-button');

      // Hide forms
      $('#hidden-form').hide();
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

      // Get cohortid
      const cohortId = $(this).val();
  
      // Build the options of the form
      $.ajax({
        url: 'ajax/trainings_of_cohort.php',
        type: 'POST',
        data: { cohortId: cohortId },
        success: function(response) {

          if(response.trainings.length <= 0) {
            error.innerHTML = response.error;
          }
          else {
            buildSelectOptions(
              '#id_select_trainings_cohort', 
              response.message, 
              response.trainings, 
              function(training) { return training.id; }, 
              function(training) { return training.fullname; }
            );

            $('#hidden-form').show();
          }
          
        },
        
        error: handleAjaxError

      });
    });
  });