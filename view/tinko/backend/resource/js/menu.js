$(document).ready(function() {
    $('#menu-item').change(function() {
        var name = $.trim($(':selected', this).text());
        var url = $(this).val();
        if (name === 'Выберите' && url === '0') {
            $('#add-edit-menu-item input[name="name"]').val('');
            $('#add-edit-menu-item input[name="url"]').val('');
        } else {
            $('#add-edit-menu-item input[name="name"]').val(name);
            $('#add-edit-menu-item input[name="url"]').val(url);
        }
    });
});