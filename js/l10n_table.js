(function ($, Drupal) {

    "use strict";

    Drupal.behaviors.l10nGlossaryTable = {
        attach: function (context, settings) {
            var max = 0;
            $('table tbody tr td:nth-child(1)', context).each(function() {
                max = Math.max($(this).width(), max);
            }).width(max);

            var max = 0;
            $('table tbody tr td:nth-child(2)', context).each(function() {
                max = Math.max($(this).width(), max);
            }).width(max);

            $('caption', context).each(function() {
                var anchor = $(this).text().trim().slice(-1).toUpperCase();
                $(this).closest('table').before('<a id="'+anchor+'" href="#'+anchor+'"></a>');
                $(this).text($(this).text().toUpperCase());
            });

            var attachment = $('.attachment-before', context);
            $('a', attachment).each(function() {
                $(this).attr('href', '#'+$(this).text());
            });
            attachment.insertBefore('table', context);
        }
    };

})(jQuery, Drupal);
