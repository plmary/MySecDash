$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first' ), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});

	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change( function() {
		var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'trier': sens_recherche, 'sct_id': sct_id}),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ){
				var statut = reponse['statut'];
	
				if( statut == 'success' ){
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );

					trier( $( 'div#entete_tableau div.row div:first'), true );
				} else {
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );
				}
			}
		});
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
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

						ModalMAJEquipe( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var Libelle = $('#EIN_'+Id).find('div[data-src="ein_nom"]').find('span').text();

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


// ============================================

function ModalMAJEquipe( ein_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'ein_id': ein_id}),
		dataType: 'json',
		success: function( reponse ) {
			var ein_nom = '', ein_description = '';

			if ( ein_id != '' ) {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ];
				ein_nom = reponse['objEquipeInterne'][0]['ein_nom'];
				if ( reponse['objEquipeInterne'][0]['ein_description'] != null ) {
					ein_description = reponse['objEquipeInterne'][0]['ein_description'];
				}
			} else {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ];
			}

			construireModal( 'idModalEquipe',
				Titre,
				'<div class="form-group">' +
				'<label class="col-lg-2 col-form-label" for="ein_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="ein_nom" class="form-control" type="text" required autofocus value="' + ein_nom + '">' +
				'</div>' +
				'</div>' +
				'<div class="form-group">' +
				'<label class="col-lg-2 col-form-label" for="ein_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="ein_description" class="form-control">' + ein_description + '</textarea>' +
				'</div>' +
				'</div>',
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJEquipe', 'modal-lg' );

			// Affiche la modale qui vient d'être créée
			$('#idModalEquipe').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalEquipe').on('shown.bs.modal', function() {
				$('#ein_nom').focus();
				document.getElementById('ein_nom').selectionStart = ein_nom.length;
			});

			$('#idModalEquipe').on('hidden.bs.modal', function() {
				$('#idModalEquipe').remove(); // Supprime la modale d'ajout.
			});

			$('#formMAJEquipe').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( ein_id == '' ) {
					AjouterEquipe();
				} else {
					ModifierEquipe( ein_id );
				}
			} );

		}
	});

}
