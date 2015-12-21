$(document).ready(function() {
    $('#sitemap #catalog-tree > li > ul').hide();
    $('#sitemap #catalog-tree > li > span').click(function() {
        $(this).next().slideToggle();
    })
});
