$(document).ready(function() {

    /*
     * Форма для добавления/редактирования страницы
     */

    // последняя пустая строка загрузки файла, которую будем клонировать для добавления полей загрузки файлов
    var lastFilesRow = $('#add-edit-page #new-files > div:last-child > div:last-child');
    // скрываем последнюю строку загрузки файла, она только для клонирования
    lastFilesRow.hide();
    // если есть только последняя скрытая строка загрузки файла, добавляем еще одну
    if ($('#add-edit-page #new-files > div:last-child > div').size() == 1) {
        lastFilesRow.clone(true).insertBefore(lastFilesRow).show();
    }
    // кнопка для добавления строки загрузки файла
    $('#add-edit-page #new-files > div:last-child > div > span:first-of-type').click(function() {
        lastFilesRow.clone(true).insertAfter($(this).parent()).show();
    });
    // кнопка для удаления строки загрузки файла
    $('#add-edit-page #new-files > div:last-child > div > span:last-of-type').click(function() {
        $(this).parent().remove();
        // если это была единственная видимая строка загрузки файла, добавляем новую строку
        if ($('#add-edit-page #new-files > div:last-child > div').size() == 1) {
            lastFilesRow.clone(true).insertBefore(lastFilesRow).show();
        }
    });

    // вставить ссылку на файл в текст страницы
    $('#add-edit-page #old-files > div:last-child > div > span').click(function() {
        var fileUrl = $(this).prev().attr('href');
        var fileName = $(this).prev().text();
        $('#add-edit-page textarea[name="body"]').insertAtCaret('<a href="' + fileUrl + '">' + fileName + '</a>');
    });


});