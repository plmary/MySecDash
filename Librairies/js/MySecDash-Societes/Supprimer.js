// ============================================
// Fonctions répondant aux événements écoutés.

function ModalSupprimer( Id ){
	var Message = 'zzz';
	var Erreur = false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Verifier_Associer',
		type: 'POST',
		dataType: 'json',
		data: $.param({'sct_id': Id}),
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
				'<div id="ENT-SUPR">' +
				Message +
				'</div>';

			construireModal( 'idModalSuppSociete',
				reponse[ 'L_Titre_Supprimer' ],
				Corps,
				'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
				true, reponse[ 'L_Fermer' ],
				'formSupprimer' );

			$('#idModalSuppSociete').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSuppSociete').on('shown.bs.modal', function() {
				$('.btn-fermer').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModalSuppSociete').on('hidden.bs.modal', function() {
				$('#idModalSuppSociete').remove();
			});

			$('#formSupprimer').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				supprimerSociete( Id );
			} );
		}
	});
}


// Supprimer la Société dans la BDD et à l'écran.
function supprimerSociete( Id ) {
	var total = $( '#totalOccurrences' ).text();
	total = Number(total) - 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer',
		type: 'POST',
		data: $.param({'sct_id': Id}),
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				$( '#SCT_' + Id ).remove();

				$( '#totalOccurrences' ).text( ajouterZero( total ) );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});

	$('.modal').modal('hide');
}