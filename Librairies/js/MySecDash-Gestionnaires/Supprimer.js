// ============================================
// Fonctions répondant aux événements écoutés.

function ModalSupprimer( Id ){
    var Message = 'zzz';
    var Erreur = false;

    var Libelle = $('#GST_'+Id).find('[data-src="gst_libelle"]').find('span').text();

    $.ajax({
        url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_Verifier_Associer',
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
        url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div id="GST-SUPR">' +
                Message +
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

                supprimerGestionnaire( Id );
            } );
        }
    });
}


// Supprimer le Type d'Actif Support dans la BDD et à l'écran.
function supprimerGestionnaire( Id ) {
	var total = $( '#totalOccurrences' ).text();
	total = Number(total) - 1;

	$.ajax({
		url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_Supprimer',
		type: 'POST',
		data: $.param({'id': Id}),
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				$( '#GST_' + Id ).remove();

				// Vérifie s'il y a une limitation à la création des Entités.
//				gererBoutonAjouter( reponse['total'], reponse['limitation'], reponse['libelle_limitation'] );

                $( '#totalOccurrences' ).text( ajouterZero( total ) );
            }

            afficherMessage( texteMsg, statut, 'body' );
        }
    });

    $('.modal').modal('hide');
}