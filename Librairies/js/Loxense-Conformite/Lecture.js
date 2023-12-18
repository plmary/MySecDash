$(function() {
    // Charge les données du tableau.
    trier( $( 'div#entete_tableau div.row div:first'), false );


    // Active l'écoute du "click" sur les libellés de l'entête du tableau.
    $('.triable').click( function() {
        trier( this, true );
    });


    $("#s_crs_id").change(function(){
        $.ajax({
            url: '../../../Loxense-Conformite.php?Action=AJAX_Modifier_Cartographie',
            type: 'POST',
            data: $.param({'s_crs_id': $("#s_crs_id").val(), 's_ent_id': $("#s_crs_id option:selected").attr('data-ent_id')}),
            dataType: 'json',

            success: function(reponse){
                // Remet à zéro les zones d'affichage.
                $('#totalOccurrences').text( ajouterZero( 0, 3 ) );
                $('div#corps_tableau').html( '' );

                // Afficher les informations trouvées
                trier( $( 'div#entete_tableau div.row div:first'), false );
            }
        }); 
    })


    if ( $('.btn-chercher').length > 0 ) {
        $('.btn-chercher').on('click', function() {
            trier( $('div.active'), false, $('#c_rechercher').val() );
        });
    }


    if ( $('.btn-regenerer').length > 0 ) {
        $('.btn-regenerer').on('click', function() {
            $.ajax({
                url: '../../../Loxense-Conformite.php?Action=AJAX_Regenerer',
                type: 'POST',
                data: $.param({'s_crs_id': $("#s_crs_id").val(), 's_ent_id': $("#s_crs_id option:selected").attr('data-ent_id')}),
                dataType: 'json',

                success: function(reponse){
                    var statut = reponse['statut'];
                    var texteMsg = reponse['texteMsg'];

                    afficherMessage( texteMsg, statut );

                    trier( $( 'div#entete_tableau div.row div:first'), false );
                }
            }); 
        });
    }


    if ( $('#i_impression').length > 0 ) {
        $('#i_impression').on('click', function() {
            $.ajax({
                url: '../../../Loxense-Conformite.php?Action=AJAX_Generer_Impression',
                type: 'POST',
                data: $.param({'s_crs_id': $("#s_crs_id").val(), 's_ent_id': $("#s_crs_id option:selected").attr('data-ent_id')}),
                dataType: 'json',

                success: function(reponse){
                    var statut = reponse['statut'];
                    var texteMsg = reponse['texteMsg'];

                    afficherMessage( texteMsg, statut );

                    window.location.href = '../../../Loxense-Conformite.php?Action=AJAX_Charger_Impression_Excel&crs_id='+$("#s_crs_id").val();
                }
            }); 
        });
    }
});


function trier( myElement, changerTri, chercher ) {
    // AJAX changeant la valeur du filtre
    var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
    var changer_tri = changerTri || false;

    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Trier',
        type: 'POST',
        data: $.param({'trier': sens_recherche, 'chercher': chercher}),
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

                // Postionne la couleur sur la colonne active sur le tri.
                $('div#entete_tableau div.row div.triable').removeClass('active');
                $(myElement).addClass('active');

                $(myElement).attr( 'data-sens-tri', sens_recherche );

                $('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );

                // Vérifie s'il y a une limitation à la création des Cartographies.
                gererBoutonAjouter( reponse['total'], reponse['limitation'], reponse['libelle_limitation'] );


                if ( reponse[ 'droit_modifier' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Modification
                    $('.btn-modifier').click( function( event ){
                        var Id = $(this).attr('data-id');

                        ModalModifier( Id );
                    });
                }

                redimensionnerWindow();
            } else {
                if ( statut == 'warning' ) {
                    $('#corps_tableau').append(
                        '<div class="card m-3">' +
                         '<div class="card-header">' + reponse['avertissement'] + '</div>' +
                         '<div class="card-body">' +
                         '<p>' + reponse['texteMsg'] + '</p>' +
                         '<button class="btn btn-danger" onClick="window.location.href = \'../../Loxense-CartographiesRisques.php\'">Gérer les cartographies</button>' +
                         '</div>' +
                        '</div>'
                        );

                    $('#s_crs_id').attr('disabled', 'disabled');
                } else {
                    var texteMsg = reponse['texteMsg'];

                    afficherMessage( texteMsg, statut );
                }
            }
        }
    });
}
