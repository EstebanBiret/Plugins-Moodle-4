let levels = localStorage.getItem('numberOfLevels');
let number_levels = 0;

if(levels) {
    number_levels = levels;
}

let new_iterator = (parseInt(number_levels) + 1);

// Hide fields
for (let i = new_iterator; i <= 2 ; i++) {
    document.getElementById('fitem_id_trainingToLevel' + i).style.display = 'none';
}

document.getElementById('id_trainingId3').addEventListener('change', function() {
    let trainingId = this.value;

    // Hide
    for (let i = 1; i <= 2 ; i++) {
        document.getElementById('fitem_id_trainingToLevel' + i).style.display = 'none';
    }

    // Get number of levels (fields)
    $.ajax({
        url:'ajax/training_level.php',
        type: 'POST',
        data: { trainingId: trainingId },
        success: function(response) {
            localStorage.setItem('numberOfLevels', response);

            for (let i = 1; i <= response ; i++) {
                document.getElementById('fitem_id_trainingToLevel' + i).style.display = '';
            }
        },

        error: handleAjaxError

      });

});

// Hide fields when cancel anny form
const cancelButtons = document.querySelectorAll('#id_cancel');
cancelButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        localStorage.removeItem('numberOfLevels');
        localStorage.removeItem('trainingCourse');
        localStorage.removeItem('trainingSemester');
        localStorage.removeItem('trainingLu');
        localStorage.removeItem('numberOfLus');
        localStorage.removeItem('luCourse');
    });
});