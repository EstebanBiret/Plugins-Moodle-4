$(document).ready(function() {

    // Variables
    var completion = document.getElementById('completion-student');
    var table = document.getElementById('result-table');
    var numberOfStudents = document.getElementById('number-students');
    var infosContainer = document.getElementById('infos-container');

    $('#id_select_trainings_cohort').on('change', function() {

        // Clean
        init(completion, table, numberOfStudents, infosContainer)
        
        $('#hidden-form-2').hide();

        // Get the values
        var value = $(this).val();

        // Split trainingId and cohortId
        var values = value.split('/');
        var trainingId = values[0];
        var cohortId = values[1];

        localStorage.setItem('trainingId', trainingId);

        // Avoid useless ajax request
        if(trainingId != -1) {

            $.ajax({
                url: 'ajax/training_fullname.php',
                type: 'POST',
                data: { trainingId: trainingId },
                success: function(trainingFullName) {
                    infosContainer.innerHTML = trainingFullName;
                    displayStudents(cohortId, trainingId, numberOfStudents, table);
                },
                error: handleAjaxError
            });
        }
    });
});