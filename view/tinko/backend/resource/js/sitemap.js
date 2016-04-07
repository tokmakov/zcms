$(document).ready(function() {
    $('#sitemap-item').change(function() {
        var name = $.trim($(':selected', this).text());
        var capurl = $(this).val();
        if (name === 'Выберите' && capurl === '0') {
            $('#add-edit-sitemap-item input[name="name"]').val('');
            $('#add-edit-sitemap-item input[name="capurl"]').val('');
        } else {
            $('#add-edit-sitemap-item input[name="name"]').val(name);
            $('#add-edit-sitemap-item input[name="capurl"]').val(capurl);
        }
    });
});