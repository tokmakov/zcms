$(document).ready(function() {

    // загрузка файлов с использованием XmlHttpRequest, страница со списком всех
    // файлов блога
    $('#all-blog-files > div:first-child > form').on('submit', function(e) {
        e.preventDefault();
        var _this = $(this);
        var uploadFormData = new FormData(_this.get(0));
        $.ajax({
            url: _this.attr('action'),
            type: _this.attr('method'),
            contentType: false,
            processData: false,
            data: uploadFormData,
            dataType: 'html',
            success: function(html) {
                $('#all-blog-files > div:last-child > div:first-child > div').html(html);
            },
            error: function() {
                alert('Ошибка при загрузке файлов');
            }
        });
        // очищаем поле выбора файлов
        var input = $(this).children('input[type="file"]');
        input.replaceWith(input.clone());
    });
    
    // показать/скрыть список файлов блога, страница формы для
    // добавления/редактирования поста блога
    $('#add-edit-post > div#blog-files > div:last-child').hide();
    $('#add-edit-post > div#blog-files > div:first-child > div:last-child').hide();
    $('#add-edit-post > div#blog-files > div:first-child > div:first-child > span').click(function() {
        $(this).parent().next().toggle();
        $(this).parent().parent().next().slideToggle();
    });
   
    // загрузка файлов с использованием XmlHttpRequest, страница формы для
    // добавления/редактирования поста блога
    $('#add-edit-post').on('click', 'input[name="upload"]', null, function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var uploadFormData = new FormData(form.get(0));
        $.ajax({
            url: form.attr('action').replace(/(addpost|editpost)/i, 'upload'),
            type: form.attr('method'),
            contentType: false,
            processData: false,
            data: uploadFormData,
            dataType: 'html',
            success: function(html) {
                $('#add-edit-post > #blog-files > div:last-child > div:first-child > div').html(html);
            },
            error: function() {
                alert('Ошибка при загрузке файлов');
            }
        });
        // очищаем поле выбора файлов
        var input = $(this).siblings('input[type="file"]');
        input.replaceWith(input.clone());
    });
    
    // вставить ссылку на файл в текст записи (поста) блога, страница формы для
    // добавления/редактирования поста блога
    $('#add-edit-post #blog-files > div:last-child > div > div').on('click', 'ul > li > span', null, function() {
        var fileUrl = $(this).data('url');
        var fileType = $(this).data('type');
        var fileName = $(this).children('span').text();
        if (fileType == 'img') {
            $('#add-edit-post textarea[name="body"]').insertAtCaret('<img src="' + fileUrl + '" alt="" />'); 
        } else {
            $('#add-edit-post textarea[name="body"]').insertAtCaret('<a href="' + fileUrl + '">' + fileName + '</a>');
        }
    });

});