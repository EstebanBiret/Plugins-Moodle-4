$(document).ready(function() {

  // Variables
  var completion = document.getElementById('completion-student');
  var table = document.getElementById('result-table');
  var numberOfStudents = document.getElementById('number-students');
  var infosContainer = document.getElementById('infos-container');

  $(document).on('click', '.student-details', function(event) {

    init(completion, table, numberOfStudents, infosContainer);

    $('#hidden-form-2').show();
    event.preventDefault();
    
    // Get attributes
    var cohortId = $(this).attr('cohortId');
    var trainingId = $(this).attr('trainingId');
    var studentId = $(this).attr('studentId');

    var value = cohortId + '/' + trainingId + '/' + studentId;
    localStorage.setItem('studentId', studentId);

    // Build the options of the form
    $.ajax({
      url: 'ajax/students_of_this_cohort.php',
      type: 'POST',
      data: { cohortId: cohortId, trainingId: trainingId },
      success: function(response) {

        buildSelectOptions(
          '#id_select_students_training', 
          response.messageSelectStudent, 
          response.students, 
          function(student) { return student[2]; }, 
          function(student) { return student[0] + ' ' + student[1]; }, 
          '', 
          studentId
        );

      },
      error: handleAjaxError
    });

    // Start of the display
    studentDetails(studentId, trainingId, value, infosContainer, completion);
  });
});