$(document).ready(function() {
    $('.product-list-comment form').ajaxForm({
        success: function() {
            // показываем окно с сообщением
            $('<div>Комментарий к товару сохранен</div>')
                .prependTo('body')
                .hide()
                .addClass('modal-window')
                .center()
                .fadeIn(500, function() {
                    $(this).delay(1000).fadeOut(500, function() {
                        $(this).remove();
                    });
                });
        },
        error: function() {
            alert('Ошибка при добавлении комментария');
        }
    });
});
