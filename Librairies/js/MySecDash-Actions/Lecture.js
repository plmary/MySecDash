$(function() {
    // Charge les données du tableau.
    trier( $( 'div#entete_tableau div.row div:first'), false );


    // Active l'écoute du "click" sur les libellés de l'entête du tableau.
    $('.triable').click( function() {
        trier( this, true );
    });


    $("#s_crs_id").change(function(){
        $.ajax({
            url: '../../../Loxense-Actions.php?Action=AJAX_Modifier_Cartographie',
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
});


function trier( myElement, changerTri, chercher ) {
    // AJAX changeant la valeur du filtre
    var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
    var changer_tri = changerTri || false;

    $.ajax({
        url: '../../../Loxense-Actions.php?Action=AJAX_Trier',
        type: 'POST',
        data: $.param({'trier': sens_recherche, 'chercher': chercher}),
        dataType: 'json', // le résultat est transmit dans un objet JSON

        success: function( reponse ){
            var statut = reponse['statut'];

            if( statut == 'success' ){
                var texteHTML = reponse['texteHTML'].replace(/#_#/g,'&hellip;');

                $('div#corps_tableau').html( texteHTML );

                if ( changer_tri == true ) {
                    var Element = sens_recherche.split('-');
                    if ( Element[ Element.length - 1 ] == 'desc' ) {
                        sens_recherche = Element[ 0 ];
                    } else {
                        sens_recherche = Element[ 0 ] + '-desc';
                    }
                }

                // Désactive les boutons "Modifier" et "Supprimer" s'il n'y a pas d'action associé à la mesure.
                $('div[id^="ACT_"').each( function( index, action ) {
                    var _parent = $(this);

                    $(action).find('div[data-src="act_libelle"]').each( function( index, act_libelle ) {
                        if ( $(act_libelle).text() == '' ) {
                            $(action).find('.btn-modifier').each( function( index, bouton ) {
                                $(bouton).attr('disabled', 'disabled');
                            });

                            $(action).find('.btn-supprimer').each( function( index, bouton ) {
                                $(bouton).attr('disabled', 'disabled');
                            });
                        }
                    });
                });

                // Postionne la couleur sur la colonne active sur le tri.
                $('div#entete_tableau div.row div.triable').removeClass('active');
                $(myElement).addClass('active');

                $(myElement).attr( 'data-sens-tri', sens_recherche );

                $('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


                if ( reponse[ 'droit_modifier' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Modification
                    $('.btn-modifier').click( function( event ){
                        var Id = $(this).attr('data-id');

                        ModalModifier( Id );
                    });
                }


                if ( reponse[ 'droit_supprimer' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Suppression
                    $('.btn-supprimer').click(function(){
                        var Id = $(this).attr('data-id');

                        var mcr_libelle = $('#ACT_'+Id).find('div[data-src="mcr_libelle"]').find('span').text();
                        var spp_nom = $('#ACT_'+Id).find('div[data-src="spp_nom"]').find('span').text();
                        var act_libelle = $('#ACT_'+Id).find('div[data-src="act_libelle"]').find('span').text();
                        var acteur = $('#ACT_'+Id).find('div[data-src="acteur"]').find('span').text();
                        var act_priorite = $('#ACT_'+Id).find('div[data-src="act_priorite"]').find('span').text();
                        var date_debut = $('#ACT_'+Id).find('div[data-src="date_debut"]').find('span').text();
                        var date_fin = $('#ACT_'+Id).find('div[data-src="date_fin"]').find('span').text();
                        var act_statut_libelle = $('#ACT_'+Id).find('div[data-src="act_statut_libelle"]').find('span').text();

                        ModalSupprimer( Id, mcr_libelle, spp_nom, act_libelle, acteur, act_priorite, date_debut, date_fin, act_statut_libelle );
                    });
                }


                $('#corps_tableau .row').on('mouseenter', function( event ) {
                    $(this).find('div:last').addClass('text-right').removeClass('invisible');
                });


                $('#corps_tableau .row').on('mouseleave', function( event ) {
                    $(this).find('div:last').addClass('invisible');
                });


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

                    $('button.btn-ajouter').attr('disabled', 'disabled');
                    $('#s_crs_id').attr('disabled', 'disabled');
                } else {
                    var texteMsg = reponse['texteMsg'];

                    afficherMessage( texteMsg, statut );
                }
            }
        }
    });
}
