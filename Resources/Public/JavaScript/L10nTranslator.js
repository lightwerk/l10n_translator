
define(['jquery'], function ($) {
    'use strict';

    var L10nTranslator = {};

    L10nTranslator.init = function() {

        $('form.l10n-translation-translation').bind('submit', function (ev) {
            ev.preventDefault();

            var data = {
                language: $('input[name=language]', $(this)).val(),
                path: $('input[name=path]', $(this)).val(),
                target: $('input[name=target]', $(this)).val(),
                key: $('input[name=key]', $(this)).val()
            };

            $.ajax({
                type: 'POST',
                url: TYPO3.settings.ajaxUrls['L10nTranslator::translation::update'],
                data: data,
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    top.TYPO3.Notification.showMessage(
                        response.flashMessage.title,
                        response.flashMessage.message,
                        response.flashMessage.severity,
                        5
                    );

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    top.TYPO3.Notification.showMessage(
                        'Status: ' + textStatus,
                        'Error: ' + errorThrown,
                        top.TYPO3.Severity.error,
                        5
                    );
                }
            });

        });
    };

    $(document).ready(function() {
        L10nTranslator.init();
    });

});


