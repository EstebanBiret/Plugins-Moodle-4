// Functions to display informations

/**
 * Initialize the display by hiding export forms and clearing the completion, table, number of students, and information container.
 *
 * @param {HTMLElement} completion - The element where the completion details will be displayed.
 * @param {HTMLElement} table - The element where the table will be displayed.
 * @param {HTMLElement} numberOfStudents - The element where the number of students will be displayed.
 * @param {HTMLElement} infosContainer - The element where the information about the student will be displayed.
 */
function init(completion, table, numberOfStudents, infosContainer) {

    $('.export-student').hide();
    $('.export-cohort').hide();

    completion.innerHTML = '';
    table.innerHTML = '';
    numberOfStudents.innerHTML = '';
    infosContainer.innerHTML = '';
}

/**
 * Create a header element with specified text and class name.
 *
 * @param {string} text - The text content of the header.
 * @param {string} className - The class name to be applied to the header element.
 * @returns {HTMLElement} - The created header element.
 */
function createHeader(text, className) {

    var header = document.createElement('div');
    header.textContent = text;
    header.className = className;
    return header;
}
  
/**
 * Create a section for a course with its details and associated activities.
 *
 * @param {Object} course - The course object containing course details and activities.
 * @param {Object} response - The response object containing response details.
 * @returns {HTMLElement} - The created course section element.
 */
function createCourseSection(course, response) {

    var courseSection = document.createElement('details');
    courseSection.classList.add('course-section');

    var courseTitle = document.createElement('summary');
    courseTitle.classList.add('course-title');
    courseTitle.textContent = course.courseName;
    courseSection.appendChild(courseTitle);

    if (Array.isArray(course.activities) && course.activities.length > 0) {
        var table = document.createElement('table');
        table.classList.add('activity-table');
        var tableHeader = document.createElement('thead');
        var tableBody = document.createElement('tbody');

        var headerRow = document.createElement('tr');
        var nameHeader = document.createElement('th');
        nameHeader.textContent = response.activityName;
        var typeHeader = document.createElement('th');
        typeHeader.textContent = response.type;
        var completionHeader = document.createElement('th');
        completionHeader.textContent = response.completionStatus;
        var dateHeader = document.createElement('th');
        dateHeader.textContent = response.openingDate;

        headerRow.appendChild(nameHeader);
        headerRow.appendChild(typeHeader);
        headerRow.appendChild(completionHeader);
        headerRow.appendChild(dateHeader);
        tableHeader.appendChild(headerRow);
        table.appendChild(tableHeader);
        table.appendChild(tableBody);

        course.activities.forEach(function (activity) {
        var activityRow = document.createElement('tr');
        var nameCell = document.createElement('td');
        var nameLink = document.createElement('a');
        nameLink.href = activity.link;
        nameLink.target = '_blank';
        nameLink.textContent = activity.name;
        nameCell.appendChild(nameLink);

        var typeCell = document.createElement('td');
        typeCell.textContent = activity.type;
        var dateCell = document.createElement('td');
        dateCell.textContent = activity.date;
        var completionCell = document.createElement('td');
        var completionImage = document.createElement('img');
        completionImage.classList.add('img-completion');

        if (activity.completion) {
            completionImage.src = 'img/complete.png';
            completionImage.alt = response.completed;
            completionImage.title = response.completed;
        } else {
            completionImage.src = 'img/uncomplete.png';
            completionImage.alt = response.uncompleted;
            completionImage.title = response.uncompleted;
        }

        completionCell.appendChild(completionImage);
        activityRow.appendChild(nameCell);
        activityRow.appendChild(typeCell);
        activityRow.appendChild(completionCell);
        activityRow.appendChild(dateCell);
        tableBody.appendChild(activityRow);
        });

        courseSection.appendChild(table);
    } else {
        var noActivities = document.createElement('div');
        noActivities.className = 'no-architecture-txt';
        noActivities.textContent = response.noActivities;
        courseSection.appendChild(noActivities);
    }
    return courseSection;
}
  
/**
 * Create a section for a module with its details and associated courses.
 *
 * @param {Object} module - The module object containing module details and courses.
 * @param {Object} response - The response object containing response details.
 * @returns {HTMLElement} - The created module section element.
 */
function createModuleSection(module, response) {

    var moduleSection = document.createElement('details');
    moduleSection.classList.add('module-section');

    var moduleTitle = document.createElement('summary');
    moduleTitle.classList.add('module-title');
    moduleTitle.textContent = module.moduleName;
    moduleSection.appendChild(moduleTitle);

    var courseList = document.createElement('ul');
    courseList.classList.add('course-list');

    if (Array.isArray(module.courses) && module.courses.length > 0) {
        module.courses.forEach(function (course) {
        var courseSection = createCourseSection(course, response);
        courseList.appendChild(courseSection);
        });
    } else {
        var noCourses = document.createElement('div');
        noCourses.className = 'no-architecture-txt';
        noCourses.textContent = response.noCourses;
        moduleSection.appendChild(noCourses);
    }

    moduleSection.appendChild(courseList);
    return moduleSection;
}
  
/**
 * Create a section for a block with its details and associated modules.
 *
 * @param {Object} block - The block object containing block details and modules.
 * @param {Object} response - The response object containing response details.
 * @returns {HTMLElement} - The created block section element.
 */
function createBlockSection(block, response) {

    var blockSection = document.createElement('details');
    blockSection.classList.add('block-section');

    var blockTitle = document.createElement('summary');
    blockTitle.classList.add('block-title');
    blockTitle.textContent = block.blockName;
    blockSection.appendChild(blockTitle);

    var moduleList = document.createElement('ul');
    moduleList.classList.add('module-list');

    if (Array.isArray(block.modules) && block.modules.length > 0) {
        block.modules.forEach(function (module) {
        var moduleSection = createModuleSection(module, response);
        moduleList.appendChild(moduleSection);
        });
    } else {
        var noModules = document.createElement('div');
        noModules.className = 'no-architecture-txt';
        noModules.textContent = response.noModules;
        blockSection.appendChild(noModules);
    }

    blockSection.appendChild(moduleList);
    return blockSection;
}
  
/**
 * Handle AJAX error by logging the error details.
 *
 * @param {XMLHttpRequest} xhr - The XMLHttpRequest object.
 * @param {string} status - The status of the AJAX request.
 * @param {Error} error - The error object.
 */
function handleAjaxError(xhr, status, error) {
    console.error(xhr, status, error);
}
  
/**
 * Display courses not in architecture and then courses in architecture.
 *
 * @param {Object} response - The response object containing course details.
 * @param {HTMLElement} completion - The element where the completion details will be displayed.
 * @param {string} value - The value associated with the AJAX request.
 */
function handleCoursesDisplay(response, completion, value) {

    var header = createHeader(response.header, 'architecture-header');
    completion.appendChild(header);

    if (response.courses.length > 0) {
        response.courses.forEach(function (course) {
        var courseSection = createCourseSection(course, response);
        completion.appendChild(courseSection);
        });
    } else {
        var notHeader = createHeader(response.notHeader, 'margin-top');
        completion.appendChild(notHeader);
    }

    var hr = document.createElement('hr');
    hr.className = 'line';
    completion.appendChild(hr);

    // Then courses in architecture
    $.ajax({
        url: 'ajax/courses_in_architecture.php',
        type: 'POST',
        data: { value: value },
        success: function (response) {
        handleCoursesInArchitecture(response, completion);
        },
        error: handleAjaxError
    });
}
  
/**
 * Handle courses in architecture by displaying blocks or modules based on the response.
 *
 * @param {Object} response - The response object containing block or module details.
 * @param {HTMLElement} completion - The element where the completion details will be displayed.
 */
function handleCoursesInArchitecture(response, completion) {

    var headerArchitecture = createHeader(response.architecture, 'architecture-header');
    completion.appendChild(headerArchitecture);

    if (response.levels == 2) {
        if (response.result.length > 0) {
        response.result.forEach(function (block) {
            var blockSection = createBlockSection(block, response);
            completion.appendChild(blockSection);
        });
        } else {
        var noArchitecture = createHeader(response.noArchitecture, 'margin-top');
        completion.appendChild(noArchitecture);
        }
    } else {
        if (response.result.length > 0) {
        response.result.forEach(function (module) {
            var moduleSection = createModuleSection(module, response);
            completion.appendChild(moduleSection);
        });
        } else {
        var noArchitecture = createHeader(response.noArchitecture, 'margin-top');
        completion.appendChild(noArchitecture);
        }
    }
}

/**
 * Retrieve student details and initiate the display of courses.
 *
 * @param {int} studentId - The ID of the student.
 * @param {int} trainingId - The ID of the training.
 * @param {string} value - The value associated with the AJAX request.
 * @param {HTMLElement} infosContainer - The element where the information about the student will be displayed.
 * @param {HTMLElement} completion - The element where the completion details will be displayed.
 */
function studentDetails(studentId, trainingId, value, infosContainer, completion) {

    $.ajax({
        url: 'ajax/student_details.php',
        type: 'POST',
        data: { studentId: studentId, trainingId: trainingId },
        success: function(student) {

            infosContainer.innerHTML =  student.firstName + ' ' + student.lastName + ' (' + student.trainingFullName + ')';

            // Begin to display informations
            $.ajax({
            url: 'ajax/courses_not_in_architecture.php',
            type: 'POST',
            data: { value: value },
            success: function (response) {
                handleCoursesDisplay(response, completion, value);
            },
            error: handleAjaxError
            });

            $('.export-student').show();
        },
        error: handleAjaxError
    });
}

/**
 * Build options for a select element and update it.
 *
 * @param {string} selectId - The ID of the select element to be updated.
 * @param {string} firstOptionText - The text for the first (default) option.
 * @param {Array} items - An array of items to populate the select element.
 * @param {function} getValue - A function to get the value for each option from an item.
 * @param {function} getText - A function to get the text for each option from an item.
 * @param {string} [additionalValue=''] - Additional value to append to each option value (optional).
 * @param {string} [selectedValue=null] - The value of the option to be selected (optional).
 */
function buildSelectOptions(selectId, firstOptionText, items, getValue, getText, additionalValue = '', selectedValue = null) {
    var select = $(selectId);
    select.empty();

    // First option
    select.append($('<option>', {
        value: -1 + (additionalValue ? '/' + additionalValue : ''),
        text: firstOptionText
    }));

    // All the items
    items.forEach(function(item) {
        select.append($('<option>', {
            value: getValue(item) + (additionalValue ? '/' + additionalValue : ''),
            text: getText(item)
        }));
    });

    // Select the actual value if provided
    if (selectedValue !== null) {
        select.val(selectedValue);
    }
}

/**
 * Fetches and displays a list of students for a given cohort and training.
 *
 * @param {int} cohortId - The ID of the cohort.
 * @param {int} trainingId - The ID of the training.
 * @param {HTMLElement} numberOfStudents - The HTML element to display the number of students.
 * @param {HTMLTableElement} table - The HTML table element to populate with student data.
 */
function displayStudents(cohortId, trainingId, numberOfStudents, table) {
    // Display the html_table that contains all the student in this training (firstname, lastname and span for view details)
    $.ajax({
        url: 'ajax/students_of_this_cohort.php',
        type: 'POST',
        data: { cohortId: cohortId, trainingId: trainingId },
        success: function(response) {

          var numberStudent = response.number;         

          // Browse all student's data
          for (var i = 0; i < response.students.length; i++) {
              var student = response.students[i];

              var firstName = student[0];
              var lastName = student[1];
              var detailsLink = student[3];

              var newRow = table.insertRow();

              newRow.insertCell().innerHTML = firstName;
              newRow.insertCell().innerHTML = lastName;
              newRow.insertCell().innerHTML = detailsLink;
          }

          if (numberStudent != 0) {
              numberOfStudents.innerHTML = numberStudent + response.message1;
              $('.export-cohort').show();
          }
          else {
              numberOfStudents.innerHTML = response.message2;
          }
        },
        error: handleAjaxError
    });
}