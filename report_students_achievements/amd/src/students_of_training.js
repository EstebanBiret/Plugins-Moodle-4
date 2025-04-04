$(document).ready(function() {

  // Variables
  var completion = document.getElementById('completion-student');
  var table = document.getElementById('result-table');
  var numberOfStudents = document.getElementById('number-students');
  var infosContainer = document.getElementById('infos-container');

  $('#id_select_students_training').on('change', function() {

    init(completion, table, numberOfStudents, infosContainer);
    
    // Get the studentId
    var studentId = $(this).val();

    // Avoir useless code
    if(studentId != -1) {
      var cohortId = localStorage.getItem('cohortId') ?? '';
      var trainingId = localStorage.getItem('trainingId') ?? '';
      localStorage.setItem('studentId', studentId);

      // Values
      var value = cohortId + '/' + trainingId + '/' + studentId;

      // Start of the display
      studentDetails(studentId, trainingId, value, infosContainer, completion);
    }
  });
});