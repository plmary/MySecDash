function controleSaisieChamp( event, Id ) {
    var p_event = event;

    if ( p_event.which == KEY_RETURN ) sauverParametre( Id );
}


function sauverParametre( Id ) {
    var Old_Value;
    var New_Value;

    Old_Value = $('#prs_valeur-' + Id).attr('data-old');
    
    if ( $('#prs_valeur-' + Id).attr('type') == 'checkbox' ) {
        if ( $('#prs_valeur-' + Id).is(':checked') ) {
            New_Value = 'true';
        } else {
            New_Value = 'false';
        }
    } else if ( $('#prs_valeur-' + Id).attr('type') == 'radio' ) {
        New_Value = $('#prs_valeur-' + Id + ':checked').val();
    } else {
        Old_Value = $('#prs_valeur-' + Id).attr('data-old');
        New_Value = $('#prs_valeur-' + Id).val();
    }

    if ( Old_Value != New_Value ) {
        $.ajax({
            url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
            type: 'POST',
            data: $.param({'id': Id, 'libelle': New_Value}),
            dataType: 'json',
            success: function(reponse) {
                var statut = reponse['statut'];
                var texteMsg = reponse['texteMsg'];

                $('#prs_valeur-' + Id).attr('data-old', New_Value);

                afficherMessage( texteMsg, statut, 'body' );
            }
        });
    }
}
