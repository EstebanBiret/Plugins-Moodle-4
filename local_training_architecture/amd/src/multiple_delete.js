// Variables
const buttonLinks = document.getElementById('delete-selected-training-links');
const buttonLu = document.getElementById('delete-selected-lu-to-lu');
const buttonCourses = document.getElementById('delete-selected-courses-not-in-architecture');

var checkboxesLinks = document.querySelectorAll('input[name="checkbox-training-links"]:checked');
var checkboxesLu = document.querySelectorAll('input[name="checkbox-lu-to-lu"]:checked');
var checkboxesCourses = document.querySelectorAll('input[name="checkbox-courses-not-in-architecture"]:checked');

var allCheckboxesLinks = document.querySelectorAll('input[name="checkbox-training-links"]');
var allCheckboxesLu = document.querySelectorAll('input[name="checkbox-lu-to-lu"]');
var allCheckboxesCourses = document.querySelectorAll('input[name="checkbox-courses-not-in-architecture"]');

/**
 * Disable or enable a button based on the state of checkboxes.
 *
 * @param {HTMLButtonElement} button - The button to be enabled or disabled.
 * @param {NodeListOf<HTMLInputElement>} checkboxes - The list of checkboxes to check.
 */
function disableButton(button, checkboxes) {
  var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
  button.disabled = !atLeastOneChecked;
}

// Initial disable
disableButton(buttonLinks, allCheckboxesLinks);
disableButton(buttonLu, allCheckboxesLu);
disableButton(buttonCourses, allCheckboxesCourses);

// Disable or not the button depends on checkboxes's states
allCheckboxesLinks.forEach(function(allCheckboxes) {
  allCheckboxes.addEventListener('change', function() {
    disableButton(buttonLinks, allCheckboxesLinks);

    // Highlight selected links
    var parentTR = allCheckboxes.closest('tr');
    parentTR.classList.toggle('tr-selected-lu', allCheckboxes.checked);
  });
});

allCheckboxesLu.forEach(function(allCheckboxes) {
  allCheckboxes.addEventListener('change', function() {
    disableButton(buttonLu, allCheckboxesLu);
  });
});

allCheckboxesCourses.forEach(function(allCheckboxes) {
  allCheckboxes.addEventListener('change', function() {
    disableButton(buttonCourses, allCheckboxesCourses);

    // Highlight selected courses
    var parentTR = allCheckboxes.closest('tr');
    parentTR.classList.toggle('tr-selected-lu', allCheckboxes.checked);
  });
});

// Handle highlighted <tr> and link between selected LU
allCheckboxesLu.forEach(function(checkbox) {
  checkbox.addEventListener('change', function() {
    var parentTR = checkbox.closest('tr');
    parentTR.classList.toggle('tr-selected-lu', checkbox.checked);

    var trainingId = checkbox.getAttribute('data-trainingid');
    var luId2 = checkbox.getAttribute('data-luid2');
    var isLuId2Course = checkbox.getAttribute('data-isluid2course');

    // Check linked TR
    if(isLuId2Course == 'false') {

      if(checkbox.checked) { // Add links class to childs of selected checkbox
        allCheckboxesLu.forEach(function(checkbox) {

          if(checkbox.getAttribute('data-trainingid') == trainingId && 
          checkbox.getAttribute('data-luid1') == luId2 && 
          checkbox.getAttribute('data-isluid2course') == 'true') {

            var linkedTR = checkbox.closest('tr');
            linkedTR.classList.add('tr-linked-lu');

          }
        });
      }

      // Check if the unchecked checkbox have childs linked
      else {
        var links = false;
        allCheckboxesLu.forEach(function(checkbox) {

          if(checkbox.getAttribute('data-trainingid') == trainingId && 
          checkbox.getAttribute('data-luid2') == luId2 && 
          checkbox.getAttribute('data-isluid2course') == 'false' && checkbox.checked) {
            links = true;
          }
        });

        // Remove childs class linked
        if(!links) {
          allCheckboxesLu.forEach(function(checkbox) {

            if(checkbox.getAttribute('data-trainingid') == trainingId && 
            checkbox.getAttribute('data-luid1') == luId2 && 
            checkbox.getAttribute('data-isluid2course') == 'true') {
              
              var linkedTRChild = checkbox.closest('tr');
              linkedTRChild.classList.remove("tr-linked-lu");
            }
          });
        }
      }
    }
  });
});

// Buttons events
buttonLinks.addEventListener("click", function() {
    var selectedIds = [];
    document.querySelectorAll('input[name="checkbox-training-links"]:checked').forEach(function(checkbox) {
      selectedIds.push(checkbox.value);
    });

    if(selectedIds.length > 0) {

      $.ajax({
        url:'ajax/multiple_delete_training_links.php',
        type: 'POST',
        data: { selectedIds: selectedIds },
        success: function(url) {
            window.location.href = url;
        },

        error: handleAjaxError

      });

    }
});

buttonLu.addEventListener("click", function() {
  var selectedIds = [];
  document.querySelectorAll('input[name="checkbox-lu-to-lu"]:checked').forEach(function(checkbox) {
    selectedIds.push(checkbox.value);
  });

  if(selectedIds.length > 0) {

    $.ajax({
      url:'ajax/multiple_delete_lu_to_lu.php',
      type: 'POST',
      data: { selectedIds: selectedIds },
      success: function(url) {
          window.location.href = url;
      },

      error: handleAjaxError

    });

  }
});

buttonCourses.addEventListener("click", function() {
  var selectedIds = [];
  document.querySelectorAll('input[name="checkbox-courses-not-in-architecture"]:checked').forEach(function(checkbox) {
    selectedIds.push(checkbox.value);
  });

  if(selectedIds.length > 0) {

    $.ajax({
      url:'ajax/multiple_delete_courses_not_in_architecture.php',
      type: 'POST',
      data: { selectedIds: selectedIds },
      success: function(url) {
          window.location.href = url;
      },

      error: handleAjaxError

    });

  }
});