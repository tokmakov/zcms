$(document).ready(function() {
    /*
     * Форма для редактирования личных данных пользователя
     */
    if ( ! $('#add-edit-user input[name="change"]').prop('checked')) {
        $(this).parent().parent().parent().next().hide();
    }
    $('#add-edit-user input[name="change"]').change(function() {
        $(this).parent().parent().parent().next().slideToggle();
    })
});