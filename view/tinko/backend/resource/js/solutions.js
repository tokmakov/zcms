$(document).ready(function() {
    /*
     * Форма для добавления/редактирования товара
     */
    $('#add-edit-product #load-by-code').click(function() {
        var code = $('#add-edit-product input[name="code"]').val();
        if (code.match(/^\d{6}$/)) {
        $.get('/backend/solutions/ajax/code/' + code, function(data) { // получаем данные о товаре по коду
            if (data.name === undefined) {
                return;
            }
            $('#add-edit-product input[name="name"]').val(data.name); // торговое наименование
            $('#add-edit-product input[name="title"]').val(data.title); // функциональное наименование
            $('#add-edit-product input[name="price"]').val(data.price); // цена
            $("#add-edit-product select[name='unit'] option").each(function(){
                if (data.unit == this.value) {
                    this.selected = true;
                } else {
                    this.selected = false;
                }
            });
            $('#add-edit-product textarea[name="shortdescr"]').val(data.shortdescr); // краткое описание
        }, 'json');
        } else {
            alert('Код товара состоит из шести цифр!')
        }
    })
});