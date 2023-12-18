$(function() {
    // Charge les données du tableau.
    trier( $( 'div#entete_tableau div.row div:first'), true );

    // Active l'écoute du "click" sur les libellés de l'entête du tableau.
    $('.triable').click( function() {
        trier( this, true );
    });
});


function trier( myElement, changerTri ) {
    // AJAX changeant la valeur du filtre
    var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
    var changer_tri = changerTri || false;

    $.ajax({
        url: '../../../Loxense-Entites.php?Action=AJAX_Trier',
        type: 'POST',
        data: $.param({'trier': sens_recherche}),
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ){
            var statut = reponse['statut'];

            if( statut == 'success' ){
                var texteMsg = reponse['texteHTML'];

                $('div#corps_tableau').html( reponse[ 'texteHTML'] );

                if ( changer_tri == true ) {
                    var Element = sens_recherche.split('-');
                    if ( Element[ Element.length - 1 ] == 'desc' ) {
                        sens_recherche = Element[ 0 ];
                    } else {
                        sens_recherche = Element[ 0 ] + '-desc';
                    }
                }

                $(myElement).attr( 'data-sens-tri', sens_recherche );

                $('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


                // Vérifie s'il y a une limitation à la création des Entités.
                if ( reponse['total'] >= reponse['limitation'] && reponse['limitation'] != 0 ) {
                    var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');

                    $('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']).attr('data-old_title', old_title);
                }

                if ( reponse[ 'droit_modifier' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Modification
                    $('.btn-modifier').click( function( event ){
                        var Id = $(this).attr('data-id');

                        ouvrirChamp( event, 'ent_libelle', 'ENT_'+Id );
                    });
                }

                if ( reponse[ 'droit_supprimer' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Suppression
                    $('.btn-supprimer').click(function(){
                        var Id = $(this).attr('data-id');
                        var Libelle = $('#ENT_'+Id).find('div[data-src="ent_libelle"]').find('span').text();

                        ModalSupprimer( Id, Libelle );
                    });
                }

                redimensionnerWindow();
            } else {
                var texteMsg = reponse['texteMsg'];

                afficherMessage( texteMsg, statut );
            }
        }
    });
}