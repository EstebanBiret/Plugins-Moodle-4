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
 * Removes accents from a string by normalizing it and replacing accents with their base characters.
 *
 * @param {string} str - The input string containing accented characters.
 * @returns {string} The input string with accents removed.
 */
function removeAccents(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

// Handle show/hide on tables
document.addEventListener("DOMContentLoaded", function() {

    // AJAX request to get lang string
    $.ajax({
        url:'ajax/lang.php',
        type: 'POST',
        data: {},
        success: function(response) {
            
            var headers = document.querySelectorAll(".custom-collapsible span");
            headers.forEach(function(header) {
                header.addEventListener("click", function() {
                    this.classList.toggle("active");
                    var content = this.parentElement.querySelector('.table-container-training-architecture');

                    if (content.style.display === "block") {
                        content.style.display = "none";
                        this.textContent = response.expand;
                    } else {
                        content.style.display = "block";
                        this.textContent = response.collapse;
                    }
                });
            });

        },

        error: handleAjaxError

    });
});

// Handle search
document.addEventListener('DOMContentLoaded', function() {

    var searchInputs = document.querySelectorAll('.search-input-training-architecture');
    searchInputs.forEach(function(searchInput) {
        searchInput.addEventListener('keyup', function() {
            var filter = removeAccents(searchInput.value);
            var tableId = searchInput.dataset.tableId;
            var rows = document.querySelectorAll('#' + tableId + ' table tbody tr');

            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td');
                var textContent = '';

                cells.forEach(function(cell) {
                    textContent += removeAccents(cell.textContent) + ' ';
                });

                if (textContent.indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});