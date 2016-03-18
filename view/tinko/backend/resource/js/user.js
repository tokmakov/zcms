$(document).ready(function() {
    /*
     * Форма для редактирования личных данных пользователя
     */
    if ( ! $('#add-edit-user input[name="change"]').prop('checked')) {
        $('#add-edit-user .password').hide();
    }
    $('#add-edit-user input[name="change"]').change(function() {
        $('#add-edit-user .password').slideToggle();
    });
});