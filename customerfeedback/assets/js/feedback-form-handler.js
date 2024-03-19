jQuery(document).ready(function($) {
    $('#saveOptionsButton').click(function() {
        var selectedOptions = [];
        var inputCategory = $('#inputCategory').val(); 
        
        $('tbody.table-group-divider input[type="checkbox"]:checked').each(function() {
            var $row = $(this).closest('tr');
            var category = $row.find('td:nth-child(2)').text().trim();
            var type = $row.find('td:nth-child(3)').text().trim();
            var label = $row.find('td:nth-child(4)').text().trim();
            selectedOptions.push({ category: category, type: type, label: label });
        });

        $.ajax({
            url: ajax_object.ajax_url,
            type: "POST",
            data: {
                action: "save_selected_data",
                inputCategory: inputCategory,
                selectedOptions: selectedOptions
            },
            success: function(response) {
                alert("Successfully Added");
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
});
