(function ($) {

    Drupal.behaviors.l10nGlossary = {
        attach: function (context, settings) {
            $('.l10n-table tr', context).once('l10nGlossary').click(function () {
                // Highlight selected row.
                $('.l10n-table tr', context).each(function () {
                    $(this).removeClass('l10n-glossary');
                });
                $(this).addClass('l10n-glossary');

                // Get source id for translation.
                var sid = $('.sid a[href]', this).attr('href').split('=')[1];
                // TODO: Fix URL handling.
                $.get('/l10n-glossary/'+sid, function(data) {
                    $('#l10n-glossary').empty();
                    $.each(data.lemmata, function(index, data) {
                        // TODO: Fix URL handling.
                        $('#l10n-glossary').append("<dl data-lemma-id='"+data.id+"'><dt><strong><a href='/node/"+data.id+"'>"+data.source+"</a></strong></dt><dd><em>"+data.lemma+"</em></dd></dl>");
                    });

                    $('#l10n-glossary').append("<div style='border-bottom: 1px solid #d6d6d6;'></div>");

                    $.each(data.usage, function(index, data) {
                        $('#l10n-glossary').append("<div class='usage' data-translation-id='"+index+"' data-lemmata-used='"+data.used+"' data-lemmata-unused='"+data.unused+"'><em>Usage of lemmata in translation #"+index+" at "+data.percentage+"%</em></div>");
                    });

                    $('#l10n-glossary div.usage').mouseover(function() {
                        $('input').parent('li').removeClass('l10n-glossary-highlight-translation');
                        $('*[data-lemma-id]').removeClass('l10n-glossary-highlight-lemma-used');
                        $('*[data-lemma-id]').removeClass('l10n-glossary-highlight-lemma-unused');
                        var tid = $(this).data('translation-id');
                        var used = $(this).data('lemmata-used');
                        var unused = $(this).data('lemmata-unused');
                        $('input[value="'+tid+'"]').parent('li').addClass('l10n-glossary-highlight-translation');
                        $.each(used.toString().split(','), function(index, data) {
                            var lemma = $('*[data-lemma-id="'+data+'"]').addClass('l10n-glossary-highlight-lemma-used');
                        });
                        $.each(unused.toString().split(','), function(index, data) {
                            var lemma = $('*[data-lemma-id="'+data+'"]').addClass('l10n-glossary-highlight-lemma-unused');
                        });
                    });
                });
            });
        }
    };

})(jQuery);
