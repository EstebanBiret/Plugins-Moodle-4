document.addEventListener('DOMContentLoaded', function() {

    // Active element
    document.querySelectorAll('#lu1-table tbody tr').forEach((el) => {
        if (el.classList.contains('active-lu')) {
            el.setAttribute('data-selected', '1');
        }
    });

    // Select all arrow buttons
    var arrowButtons1 = document.querySelectorAll('.arrow-button-1');
    var arrowButtons2 = document.querySelectorAll('.arrow-button-2');

    // Add event listener to each arrow button
    arrowButtons1.forEach(function(button) {
        button.addEventListener('click', function(event) {

            // Prevent default button behavior
            event.preventDefault();

            // Get the LU ID associated with the row
            var luId = button.dataset.luid;
            var trainingId = button.dataset.trainingid;
            var granularityLevel = button.dataset.granularitylevel;

            // Determine if user wants to move up or down
            var direction = button.classList.contains('up') ? 'up' : 'down';

            // Get the parent row (tr) of the clicked element
            var row = button.closest('tr');

            // Find previous and next rows relative to the current row
            var previousRow = row.previousElementSibling;
            var nextRow = row.nextElementSibling;

            // Get LU IDs of previous and next rows
            var previousLuId = previousRow ? previousRow.querySelector('.arrow-button-1').dataset.luid : null;
            var nextLuId = nextRow ? nextRow.querySelector('.arrow-button-1').dataset.luid : null;

            // Send Ajax request to update the order
            updateOrder(luId, direction, previousLuId, nextLuId, trainingId, granularityLevel, 'level1');
        });
    });

    arrowButtons2.forEach(function(button) {
        button.addEventListener('click', function(event) {

            // Prevent default button behavior
            event.preventDefault();

            // Get the LU ID associated with the row
            var luId = button.dataset.luid;
            var trainingId = button.dataset.trainingid;
            var granularityLevel = button.dataset.granularitylevel;

            // Determine if user wants to move up or down
            var direction = button.classList.contains('up') ? 'up' : 'down';

            // Get the parent row (tr) of the clicked element
            var row = button.closest('tr');

            // Find previous and next rows relative to the current row
            var previousRow = row.previousElementSibling;
            var nextRow = row.nextElementSibling;

            // Get LU IDs of previous and next rows
            var previousLuId = previousRow ? previousRow.querySelector('.arrow-button-2').dataset.luid : null;
            var nextLuId = nextRow ? nextRow.querySelector('.arrow-button-2').dataset.luid : null;

            // Send Ajax request to update the order
            updateOrder(luId, direction, previousLuId, nextLuId, trainingId, granularityLevel, 'level2');
        });
    });

    /**
     * Updates the order of learning units by sending an AJAX request to the server.
     * Moves a learning unit up or down and calls `swapRows` to rearrange rows in the DOM.
     *
     * @param {number} luId - The ID of the learning unit to move.
     * @param {string} direction - The direction of movement ('up' or 'down').
     * @param {number} previousLuId - The ID of the previous learning unit.
     * @param {number} nextLuId - The ID of the next learning unit.
     * @param {number} trainingId - The ID of the training.
     * @param {string} granularityLevel - The granularity level.
     * @param {number} level - The current level of the learning unit.
     */

    function updateOrder(luId, direction, previousLuId, nextLuId, trainingId, granularityLevel, level) {
        var luToMove = null;
        if(direction == 'up') {
            luToMove = previousLuId;
        }
        else {
            luToMove = nextLuId;
        }

        if(luToMove) {

            $.ajax({
                url:'ajax/sort.php',
                type: 'POST',
                data: { luId: luId, luToMove: luToMove, trainingId : trainingId, granularityLevel : granularityLevel, level : level },
                success: function(response) {
                    swapRows('lu-row-' + luId, 'lu-row-' + luToMove, direction);
                },

                error: handleAjaxError

              });
        }
    }

    /**
     * Swaps rows in the DOM based on the specified direction.
     * Moves a row up or down by inserting it before or after another row.
     *
     * @param {string} classname1 - The class of the first row to swap.
     * @param {string} classname2 - The class of the second row to swap.
     * @param {string} direction - The direction of movement ('up' or 'down').
     */

    function swapRows(classname1, classname2, direction) {
        var rows1 = document.querySelectorAll('[class*="' + classname1 + '"]');
        var rows2 = document.querySelectorAll('[class*="' + classname2 + '"]');

        if (rows1.length > 0 && rows2.length > 0) {
            var parent = rows1[0].parentNode;
            var row1 = rows1[0];
            var row2 = rows2[0];
    
            if(direction == 'up') {
                parent.insertBefore(row2, row1.nextSibling);
            } 
            else {
                parent.insertBefore(row1, row2.nextSibling);
            }
        }
    }
    
});