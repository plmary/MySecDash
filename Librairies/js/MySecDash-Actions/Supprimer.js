function ModalSupprimer( Id, mcr_libelle, spp_nom, act_libelle, acteur, act_priorite, date_debut, date_fin, act_statut_libelle ) {
    var act_id = Id.split('-')[0];
    var mcr_id = Id.split('-')[1];

    $.ajax({
        url: '../../../Loxense-Actions.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',

        success: function( reponse ) {
            var evr_libelle = evr_libelle || '&nbsp;';

            var Corps =
                '<div id="ACT-SUPR">' +
                reponse['L_Action_Confirm_Suppression'].replace("%act_libelle", act_libelle)
                    .replace("%acteur", acteur)
                    .replace("%act_priorite", act_priorite)
                    .replace("%date_debut", date_debut)
                    .replace("%date_fin", date_fin)
                    .replace("%act_statut", act_statut_libelle) +
                '</div>';

            construireModal( 'idModalSupprimer',
                reponse[ 'Titre_Supprimer' ],
                Corps,
                'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
                true, reponse[ 'L_Fermer' ],
                'formSupprimer', 'modal-lg' );

            $('#idModalSupprimer').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalSupprimer').on('shown.bs.modal', function() {
                $('#idBoutonSupprimer').focus();
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalSupprimer').on('hidden.bs.modal', function() {
                $('#idModalSupprimer').remove();
            });

            $('#formSupprimer').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                supprimerAction( Id, act_id, mcr_libelle, spp_nom, act_libelle, acteur, act_priorite, date_debut, date_fin, act_statut_libelle );
            } );
        }
    });
}


// Supprimer l'Action dans la BDD et à l'écran.
function supprimerAction( Id, act_id, mcr_libelle, spp_nom, act_libelle, acteur, act_priorite, date_debut, date_fin, act_statut_libelle ) {
    var total = $( '#totalOccurrences' ).text();
    total = Number(total) - 1;

    var Mesure = Id.split('-')[1];

    $.ajax({
        url: '../../../Loxense-Actions.php?Action=AJAX_Supprimer',
        type: 'POST',
        data: $.param({'act_id': act_id, 'mcr_libelle': mcr_libelle, 'spp_nom': spp_nom, 'act_libelle': act_libelle, 'acteur': acteur,
            'act_priorite': act_priorite, 'date_debut': date_debut, 'date_fin': date_fin, 'act_statut_libelle': act_statut_libelle}),
        dataType: 'json',

        success: function( reponse ) {
            var statut = reponse[ 'statut' ];
            var texteMsg = reponse[ 'texteMsg' ];

            if ( statut == 'success' ) {
                if ( $('#corps_tableau div[id$="'+Mesure+'"]').length > 1 ) {
                    $( '#ACT_' + Id ).remove();
                } else {
                    $( '#ACT_' + Id + ' div[data-src="act_libelle"]' ).html( '<span></span>' );
                    $( '#ACT_' + Id + ' div[data-src="acteur"]' ).html( '<span></span>' );
                    $( '#ACT_' + Id + ' div[data-src="act_priorite"]' ).html( '<span></span>' );
                    $( '#ACT_' + Id + ' div[data-src="date_debut"]' ).html( '<span></span>' );
                    $( '#ACT_' + Id + ' div[data-src="date_fin"]' ).html( '<span></span>' );
                    $( '#ACT_' + Id + ' div[data-src="act_statut_libelle"]' ).html( '<span></span>' );

                    $( '#ACT_' + Id + ' button' ).attr( 'disabled', 'disabled' );
                }

                $( '#totalOccurrences' ).text( ajouterZero( total ) );
            }

            afficherMessage( texteMsg, statut, 'body' );
        }
    });

    $('.modal').modal('hide');
}
