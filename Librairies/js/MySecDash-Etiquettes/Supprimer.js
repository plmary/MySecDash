// ============================================
// Fonctions répondant aux événements écoutés.

function ModalSupprimer( Id ){
    var Message = 'zzz';
    var Erreur = false;

    var Code = $('#TGS_'+Id).find('[data-src="tgs_code"]').find('span').text();
    var Libelle = $('#TGS_'+Id).find('[data-src="tgs_libelle"]').find('span').text();


    $.ajax({
        url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Verifier_Associer',
        type: 'POST',
        dataType: 'json',
        data: $.param({'id': Id, 'code': Code, 'libelle': Libelle}),
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
        url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div id="TGS-SUPR">' +
                Message +
                '</div>';

            construireModal( 'idModalSupprimer',
                reponse[ 'Titre_Supprimer' ],
                Corps,
                'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
                true, reponse[ 'L_Fermer' ],
                'formSupprimer' );

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

                supprimerEtiquette( Id );
            } );
        }
    });
}


// Supprimer le Type d'Actif Support dans la BDD et à l'écran.
function supprimerEtiquette( Id ) {
	var total = $( '#totalOccurrences' ).text();
	total = Number(total) - 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer',
		type: 'POST',
		data: $.param({'id': Id}),
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				$( '#TGS_' + Id ).remove();

				// Vérifie s'il y a une limitation à la création des Entités.
//				gererBoutonAjouter( reponse['total'], reponse['limitation'], reponse['libelle_limitation'] );

                $( '#totalOccurrences' ).text( ajouterZero( total ) );
            }

            afficherMessage( texteMsg, statut, 'body' );
        }
    });

    $('.modal').modal('hide');
}