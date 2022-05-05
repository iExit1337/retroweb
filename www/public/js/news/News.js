$(document).ready(function () {
    $("#send-button").click(function () {
        var comment_box = $("#news-comment");
        var comment = comment_box.val();
        comment_box.addClass("sending");
        comment_box.attr("disabled", "disabled");
        $.post(PATH + 'articles/comment/add', {
            token: TOKEN,
            comment: comment,
            news_id: NEWS_ID
        }, function (data) {
            comment_box.removeClass("sending");
            comment_box.removeAttr("disabled");
            if (data.error == true) {
                $("#error-msg").show();
                $("#error-msg").html(data.error_msg);
            }

            if (data.success == true) {
                $("#error-msg").hide();
                $("#success-msg").show();
                setTimeout(function () {
                    window.location.href = PATH + 'articles/' + NEWS_ID;
                }, 500);
            }
        });
    });
});