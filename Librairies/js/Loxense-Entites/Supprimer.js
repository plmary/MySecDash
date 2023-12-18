// ============================================
// Fonctions répondant aux événements écoutés.

function ModalSupprimer( Id, Libelle ){
    var Message = 'zzz';
    var Erreur = false;

    $.ajax({
        url: '../../../Loxense-Entites.php?Action=AJAX_Verifier_Associer',
        type: 'POST',
        dataType: 'json',
        data: $.param({'id': Id, 'libelle': Libelle}),
        async: false,
        success: function( reponse ) {
            if ( reponse['statut'] == 'success' ) {
                Message = reponse['texteMsg'];
            } else {
                afficherMessage( reponse['texteMsg'], reponse['statut'], 'body', true );
                Erreur = true;
            }
        }
    });

    if ( Erreur === true ) return false;

    $.ajax({
        url: '../../../Loxense-Entites.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div id="ENT-SUPR">' +
                Message +
                '</div>';

            construireModal( 'idModalSuppEntite',
                reponse[ 'Titre1' ],
                Corps,
                'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
                true, reponse[ 'L_Fermer' ],
                'formSupprimerEntite' );

            $('#idModalSuppEntite').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalSuppEntite').on('shown.bs.modal', function() {
                $('#idBoutonSupprimer').focus();
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalSuppEntite').on('hidden.bs.modal', function() {
                $('#idModalSuppEntite').remove();
            });

            $('#formSupprimerEntite').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                supprimerEntite( Id, Libelle );
            } );
        }
    });
}


// Supprimer l'Entité dans la BDD et à l'écran.
function supprimerEntite( Id, Libelle ) {
    var total = $( '#totalOccurrences' ).text();
    total = Number(total) - 1;

    $.ajax({
        url: '../../../Loxense-Entites.php?Action=AJAX_Supprimer',
        type: 'POST',
        data: $.param({'id': Id, 'libelle': Libelle}),
        dataType: 'json',
        success: function( reponse ) {
            var statut = reponse[ 'statut' ];
            var texteMsg = reponse[ 'texteMsg' ];

            if ( statut == 'success' ) {
                $( '#ENT_' + Id ).remove();

                // Vérifie s'il y a une limitation à la création des Entités.
                if ( total >= reponse['limitation'] && reponse['limitation'] != 0 ) {
                    $('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled');
                } else {
                    var old_title = $('div#titre_ecran button.btn-ajouter').attr('data-old_title');

                    $('div#titre_ecran button.btn-ajouter').removeAttr('disabled').attr('title', old_title);
                }

                $( '#totalOccurrences' ).text( ajouterZero( total ) );
            }

            afficherMessage( texteMsg, statut, 'body' );
        }
    });

    $('.modal').modal('hide');
}