(function () {
    $(document).ready(function () {
        $("#select_receivers").selectize({
            delimiter: ',',
            persist: false,
            closeAfterSelect: true,
            loadThrottle: 1,
            placeholder: "Tippe zum Suchen...",
            create: false,
            load: function (query, callback) {
                $.post(PATH + "ajax/messages/user", {
                    token: TOKEN,
                    term: query
                }, function (response) {
                    callback(response);
                });
            }
        });
    });
})();