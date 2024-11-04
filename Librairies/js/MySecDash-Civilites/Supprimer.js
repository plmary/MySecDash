function ModalSupprimer( Id, Last_Name, First_Name ){
	var Message = 'zzz';
	var Erreur = false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Verifier_Associer',
		type: 'POST',
		dataType: 'json',
		data: $.param({'id': Id, 'last_name': Last_Name, 'first_name': First_Name}),
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
				'<div id="CVL-SUPR">' +
				Message +
				'</div>';

			construireModal( 'idModalSupprimer',
				reponse[ 'L_Titre_Supprimer' ],
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

				supprimerCivilite( Id, Last_Name, First_Name );
			} );
		}
	});
}


// Supprimer l'Entité dans la BDD et à l'écran.
function supprimerCivilite( Id, Last_Name, First_Name ) {
	var total = $( '#totalOccurrences' ).text();
	total = Number(total) - 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer',
		type: 'POST',
		data: $.param({'id': Id, 'last_name': Last_Name, 'first_name': First_Name}),
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				$( '#CVL_' + Id ).remove();

				// Vérifie s'il y a une limitation à la création des Entités.
				if ( total >= reponse['limitation'] && reponse['limitation'] != 0 ) {
					$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']);
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