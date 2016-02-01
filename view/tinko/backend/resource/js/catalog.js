$(document).ready(function() {

    /* Форма для добавления/редактирования товара */

    // последняя пустая строка технических характеристик, которую будем клонировать
    // для добавления новых технических характеристик
    var lastTechdataRow = $('#add-edit-product #techdata > div:last-child > div:last-child');
    // скрываем последнюю строку технических характеристик, она только для клонирования
    lastTechdataRow.hide();
    // если есть только последняя скрытая строка технических характеристик, добавляем еще одну
    if ($('#add-edit-product #techdata > div:last-child > div').size() == 1) {
        lastTechdataRow.clone(true).insertBefore(lastTechdataRow).show();
    }
    // кнопка для добавления строки технических характеристик
    $('#add-edit-product #techdata > div:last-child > div > span:first-of-type').click(function() {
        lastTechdataRow.clone(true).insertAfter($(this).parent()).show();
    });
    // кнопка для удаления строки технических характеристик
    $('#add-edit-product #techdata > div:last-child > div > span:last-of-type').click(function() {
        $(this).parent().remove();
        // если это была единственная видимая строка технических характеристик, добавляем новую строку
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

    // подгружаем набор параметров для выбранной функциональной группы
    $('#add-edit-product select[name="group"]').change(function () {
        var group = $(this).val();
        var product = $('#add-edit-product input[name="id"]').val();
        $.ajax({
            type: 'POST',
            url: '/backend/filter/params',
            dataType: 'html',
            data: 'group=' + group + '&product=' + product,
            success: function(html) {
                $('#params > div:last-child').html(html);
            },
        });
    });
    
    // показать/скрыть параметры подбора
    $('#params > div:last-child').hide();
    $('#params > div:first-child > span').click(function() {
        $(this).parent().next().slideToggle();
    });
});