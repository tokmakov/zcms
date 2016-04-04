$(document).ready(function() {

    /*
     * добавление/редактирование вакансии
     */
     
    // удалить требование к соискателю
    $('#add-edit-vacancy > div > div:last-child > div').on('click', 'p > span:last-child', function() {
        if ($(this).parent().siblings().length > 1) {
            $(this).parent().remove();
        }
    });
    
    // добавить требование к соискателю
    $('#add-edit-vacancy > div > div:last-child > div').on('click', 'p > span:first-of-type', function() {
        $(this).parent().clone().insertAfter($(this).parent()).find('input').val('');
    });

});