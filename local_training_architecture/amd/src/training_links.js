const courseField = document.getElementById('fitem_id_courseId');
const semesterField = document.getElementById('fitem_id_semester');
const luField = document.getElementById('fitem_id_luId');

// Hide fields
courseField.style.display = 'none';
semesterField.style.display = 'none';
luField.style.display = 'none';

let course = localStorage.getItem('trainingCourse');
let semester = localStorage.getItem('trainingSemester');
let lu = localStorage.getItem('trainingLu');

if (course === 'true') {
    courseField.style.display = '';
}

if (semester === 'true') {
    semesterField.style.display = '';
}

if (lu === 'true') {
    luField.style.display = '';
}

let trainingId = document.getElementById('id_trainingId2').value;
let level = document.getElementById('id_level').value;

// Reset fields
document.getElementById('id_trainingId2').addEventListener('change', function () {
    trainingId = this.value;
    courseField.style.display = 'none';
    semesterField.style.display = 'none';
    luField.style.display = 'none';

    document.getElementById('id_semester').value = '';
    document.getElementById('id_level').value = '';
});

document.getElementById('id_level').addEventListener('change', function () {
    level = this.value;

    // Get training's informations
    $.ajax({
        url: 'ajax/training_links.php',
        type: 'POST',
        data: { trainingId: trainingId, level: level },
        success: function (response) {

            if(response['course'] === true) {
                courseField.style.display = '';
                localStorage.setItem('trainingCourse', 'true');
                luField.style.display = 'none';
                localStorage.setItem('trainingLu', 'false');
            }
            else {
                courseField.style.display = 'none';
                localStorage.setItem('trainingCourse', 'false');
                luField.style.display = '';
                localStorage.setItem('trainingLu', 'true');
            }
            if(response['semester'] === true) {
                semesterField.style.display = '';
                localStorage.setItem('trainingSemester', 'true');
            }
            else {
                semesterField.style.display = 'none';
                localStorage.setItem('trainingSemester', 'false');
            }
    
        },
    
        error: handleAjaxError

    });

});