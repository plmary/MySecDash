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

						ModalMAJEntite( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var Libelle = $('#ENT_'+Id).find('div[data-src="ent_nom"]').find('span').text();

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

function ModalMAJEntite( ent_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'ent_id': ent_id}),
		dataType: 'json',
		success: function( reponse ) {
			var ent_nom = '', ent_description = '';

			if ( ent_id != '' ) {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ];
				ent_nom = reponse['objEntite']['ent_nom'];
				if ( reponse['objEntite']['ent_description'] != null ) {
					ent_description = reponse['objEntite']['ent_description'];
				}
			} else {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ];
			}

			construireModal( 'idModalEntite',
				Titre,
				'<div class="form-group">' +
				'<label class="col-lg-2 col-form-label" for="ent_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="ent_nom" class="form-control" type="text" required autofocus value="' + ent_nom + '">' +
				'</div>' +
				'</div>' +
				'<div class="form-group">' +
				'<label class="col-lg-2 col-form-label" for="ent_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="ent_description" class="form-control">' + ent_description + '</textarea>' +
				'</div>' +
				'</div>',
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJEntite', 'modal-lg' );

			// Affiche la modale qui vient d'être créée
			$('#idModalEntite').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalEntite').on('shown.bs.modal', function() {
				$('#ent_nom').focus();
				document.getElementById('ent_nom').selectionStart = ent_nom.length;
			});

			$('#idModalEntite').on('hidden.bs.modal', function() {
				$('#idModalEntite').remove(); // Supprime la modale d'ajout.
			});

			$('#formMAJEntite').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( ent_id == '' ) {
					AjouterEntite();
				} else {
					ModifierEntite( ent_id );
				}
			} );

		}
	});

}
