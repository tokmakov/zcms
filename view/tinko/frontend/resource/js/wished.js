$(document).ready(function() {
    $('.products-list-line .product-line-comment > div > span:last-child').click(function() {
        var textarea = $(this).parent().next().children('textarea');
        var product_id = textarea.data('id');
        var comment = textarea.val();
        $.ajax({
            type: 'POST',
            url: '/wished/ajax/comment',
            data: {product_id : product_id, comment : comment},
            success: function() {
                textarea.animate(
                    {opacity: 0.2},
                    500,
                    function() {
                        $(this).animate(
                            {opacity: 1},
                            500
                        );
                    }
                );
            }
        });
    })
});
