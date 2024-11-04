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

				// Postionne la couleur sur la colonne active sur le tri.
				$('div#entete_tableau div.row div.triable').removeClass('active');
				$(myElement).addClass('active');

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalAjouterModifier( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var Libelle = $('#AIN_'+Id).find('div[data-src="ain_libelle"]').find('span').text();
						var Type = $('#AIN_'+Id).find('div[data-src="tap_code"]').find('span').text();
						var Localisation = $('#AIN_'+Id).find('div[data-src="ain_localisation"]').find('span').text();

						ModalSupprimer( Id, Libelle, Type, Localisation );
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


function ModalAjouterModifier( ain_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'ain_id': ain_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			if ( ain_id == '' ) {
				Titre = reponse['L_Titre_Ajouter'];
				ain_libelle = '';
				tap_id = '';
				ain_localisation = '';
				Bouton = reponse[ 'L_Ajouter' ]
			} else {
				Titre = reponse['L_Titre_Modifier'];
				ain_libelle = reponse['Application']['ain_libelle'];
				tap_id = reponse['Application']['tap_id'];
				ain_localisation = reponse['Application']['ain_localisation'];
				Bouton = reponse[ 'L_Modifier' ]
			}

			var Corps =
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ain_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="ain_libelle" class="form-control" type="text" value="'+ ain_libelle + '" required>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="tap_id">' + reponse[ 'L_Type' ] + '</label>' +
				'<div class="col-lg-4">' +
				'<select id="tap_id" class="form-select" required>';
				for (let Type_Application of reponse['Liste_Types_Application']) {
					if (reponse['Application'] !== undefined) {
						if (Type_Application.tap_id == reponse['Application'].tap_id) {
							Selected = ' selected';
						} else {
							Selected = '';
						}
					} else {
						Selected = '';
					}
					Corps += '<option value="' + Type_Application.tap_id + '"' + Selected + '>' + Type_Application.tap_code + '</option>';
				}
			Corps += '</select>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ain_localisation">' + reponse[ 'L_Localisation' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="ain_localisation" class="form-control" type="text"  value="'+ ain_localisation + '"required>' +
				'</div>' +
				'</div>';

			construireModal( 'idModalApplication',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formAjouterApplication', 'modal-lg' );

			$('#idModalApplication').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalApplication').on('shown.bs.modal', function() {
				$('#ain_libelle').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModalApplication').on('hidden.bs.modal', function() {
				$('#idModalApplication').remove();
			});

			$('#formAjouterApplication').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( ain_id == '' ) {
					AjouterApplication();
				} else {
					ModifierApplication( ain_id );
				}
			} );
		}
	});
}
