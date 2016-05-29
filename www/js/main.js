$('.autoplay').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
});

$(document).ajaxStart(function(){

    $("#ajax-spinner").fadeIn('fast');
}).ajaxStop(function(){
    $("#ajax-spinner").stop().fadeOut('fast');
});
