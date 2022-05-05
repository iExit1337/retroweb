var interval;
var currentSlide = 1;

$(function () {
    startSlide();

    $("#news-go-to-previous").click(function () {
        prevSlide = currentSlide - 1;
        if (prevSlide < 1)
            goToSlide(availableSlides);
        else
            goToSlide(prevSlide);
    });

    $("#news-go-to-next").click(function () {
        nextSlide = currentSlide + 1;
        if (nextSlide > availableSlides)
            goToSlide(1);
        else
            goToSlide(nextSlide);
    });
});

function startSlide() {
    clearInterval(interval);
    interval = setInterval(function () {
        nextSlide();
    }, 7500);
}

function nextSlide() {
    if (currentSlide < availableSlides)
        currentSlide++;
    else
        currentSlide = 1;

    goToSlide(currentSlide);
}

function goToSlide(slideID) {
    clearInterval(interval);
    currentSlide = slideID;

    $('.news-slide-box').stop().fadeOut('fast', function () {
        $(this).removeClass('show').addClass('hide');
    });

    $('div[news-id="' + slideID + '"]').stop().fadeIn('fast', function () {
        $(this).addClass('show').removeClass('hide');
    });

    newsID = $('div[news-id="' + slideID + '"]').attr('news-real-link');

    if (availableSlides > 1)
        startSlide();
}    