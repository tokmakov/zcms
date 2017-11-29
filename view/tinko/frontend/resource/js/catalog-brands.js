$(document).ready(function() {
    var left = $('#brands > section:first-of-type').offset().left + $('#brands').width() - 30;
    $("a.scroll").css('left', left + 'px');
    $("a.scroll").click(function() {
        $("html, body").animate({
            scrollTop: $($(this).attr("href")).offset().top + "px"
        }, {
            duration: 500,
            easing: "swing"
        });
        return false;
    });
    $(window).resize(function() {
        var left = $('#brands > section:first-of-type').offset().left + $('#brands').width() - 30;
        $("a.scroll").css('left', left + 'px');
    });
});
