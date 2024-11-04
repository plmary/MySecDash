$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
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

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalMaJSociete( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var sct_id = $(this).attr('data-id');
						var sct_nom = $('#SCT_'+sct_id).find('div[data-src="sct_nom"]').find('span').text();

						ModalSupprimer( sct_id, sct_nom );
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


function ModalMaJSociete(sct_id=''){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'sct_id': sct_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var Titre_Ecran, Bouton_Ecran;
			var sct_nom = '', sct_description = '';
	
			if (sct_id == '') {
				Titre_Ecran = reponse['L_Titre_Ajouter'];
				Bouton_Ecran = reponse['L_Ajouter'];
			} else {
				Titre_Ecran = reponse['L_Titre_Modifier'];
				Bouton_Ecran = reponse['L_Modifier'];
				
				sct_nom = reponse['Societe'].sct_nom;
				
				if (reponse['Societe'].sct_description != null) {
					sct_description = reponse['Societe'].sct_description;
				}
			}

			construireModal( 'idModalSociete',
				Titre_Ecran,
				'<div class="form-group">' +
				' <label class="col-lg-2 col-form-label" for="sct_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				' <div class="col-lg-10">' +
				' <input id="sct_nom" class="form-control" type="text" value="' + sct_nom + '" required autofocus>' +
				'</div>' +
				'</div>' +
				'<div class="form-group">' +
				'<label class="col-lg-2 col-form-label" for="sct_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="sct_description" class="form-control">' + sct_description + '</textarea>' +
				'</div>' +
				'</div>',
				'idBoutonEcran', Bouton_Ecran,
				true, reponse[ 'L_Fermer' ],
				'formMaJSociete', 'modal-lg' );

			// Affiche la modale qui vient d'être créée
			$('#idModalSociete').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSociete').on('shown.bs.modal', function() {
				$('#sct_nom').focus();
				document.getElementById('sct_nom').selectionStart = sct_nom.length;
			});

			$('#idModalSociete').on('hidden.bs.modal', function() {
				$('#idModalSociete').remove(); // Supprime la modale d'ajout.
			});

			$('#formMaJSociete').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if (sct_id == '') {
					AjouterSociete();
				} else {
					ModifierSociete( sct_id );
				}
			} );


		}
	});

}
