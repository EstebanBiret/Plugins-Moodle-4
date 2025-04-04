$(document).ready(function() {

  // Variables
  var divNoTrainings = document.getElementById('no-trainings');
  var completion = document.getElementById('completion-student');
  var table = document.getElementById('result-table');
  var numberOfStudents = document.getElementById('number-students');
  var infosContainer = document.getElementById('infos-container');

  $('#id_cohort').on('change', function() { // Id of all_cohorts_form.php's form

    // Clean
    divNoTrainings.innerHTML = '';
    init(completion, table, numberOfStudents, infosContainer);

    // Hide forms
    $('#hidden-form').hide();
    $('#hidden-form-2').hide();

    // Get cohort id
    var cohortId = $(this).val();
    localStorage.setItem('cohortId', cohortId);

    // Check cohort's trainings
    $.ajax({
      url:'ajax/number_of_trainings_of_cohort.php',
      type: 'POST',
      data: { cohortId: cohortId },
      success: function(response) {

        if (response.numberOfTrainings <= 0) { // No trainings
          divNoTrainings.innerHTML = response.message;
        }

        else if (response.numberOfTrainings === 1) {
          infosContainer.innerHTML = response.trainingFullName;
          localStorage.setItem('trainingId', response.trainingId);
          displayStudents(cohortId, response.trainingId, numberOfStudents, table);
        }

        else { // More than 1 training
          $('#hidden-form').show();

          // Build the options of the form
          $.ajax({
            url: 'ajax/trainings_of_cohort.php',
            type: 'POST',
            data: { cohortId: cohortId },
            success: function(response) {
              buildSelectOptions(
                '#id_select_trainings_cohort', 
                response.message, 
                response.trainings, 
                function(training) { return training.id; }, 
                function(training) { return training.fullname; }, 
                cohortId,
              );
            },
            
            error: handleAjaxError

          });
        }
      },
      error: handleAjaxError

    });
  });
});