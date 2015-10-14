$(document).ready(function() {

    /* Форма для добавления/редактирования товара */

    // последняя пустая строка параметров, которую будем клонировать для добавления параметров
    var lastTechdataRow = $('#add-edit-product #techdata > div:last-child > div:last-child');
    // скрываем последнюю строку параметров, она только для клонирования
    lastTechdataRow.hide();
    // если есть только последняя скрытая строка параметров, добавляем еще одну
    if ($('#add-edit-product #techdata > div:last-child > div').size() == 1) {
        lastTechdataRow.clone(true).insertBefore(lastTechdataRow).show();
    }
    // кнопка для добавления строки параметров
    $('#add-edit-product #techdata > div:last-child > div > span:first-of-type').click(function() {
        lastTechdataRow.clone(true).insertAfter($(this).parent()).show();
    });
    // кнопка для удаления строки параметров
    $('#add-edit-product #techdata > div:last-child > div > span:last-of-type').click(function() {
        $(this).parent().remove();
        // если это была единственная видимая строка параметров, добавляем новую строку
        if ($('#add-edit-product #techdata > div:last-child > div').size() == 1) {
            lastTechdataRow.clone(true).insertBefore(lastTechdataRow).show();
        }
    });

    // последняя пустая строка загрузки файла, которую будем клонировать для добавления полей загрузки файлов
    var lastDocsRow = $('#add-edit-product #docs > div:last-child > div:last-child > div:last-child');
    // скрываем последнюю строку загрузки файла, она только для клонирования
    lastDocsRow.hide();
    // если есть только последняя скрытая строка загрузки файла, добавляем еще одну
    if ($('#add-edit-product #docs > div:last-child > div:last-child > div').size() == 1) {
        lastDocsRow.clone(true).insertBefore(lastDocsRow).show();
    }
    // кнопка для добавления строки загрузки файла
    $('#add-edit-product #docs > div:last-child > div:last-child > div > span:first-of-type').click(function() {
        lastDocsRow.clone(true).insertAfter($(this).parent()).show();
    });
    // кнопка для удаления строки загрузки файла
    $('#add-edit-product #docs > div:last-child > div:last-child > div > span:last-of-type').click(function() {
        $(this).parent().remove();
        // если это была единственная видимая строка загрузки файла, добавляем новую строку
        if ($('#add-edit-product #docs > div:last-child > div:last-child > div').size() == 1) {
            lastDocsRow.clone(true).insertBefore(lastDocsRow).show();
        }
    });
});