function ModalSupprimer( Id_Referentiel ) {
	var lbr_code = $('#LBR_'+ Id_Referentiel + ' div[data-src="lbr_code"] span').text();
	var lng_id = $('#LBR_'+ Id_Referentiel + ' div[data-src="lng_id"] span').text();
	var lbr_libelle = $('#LBR_'+ Id_Referentiel + ' div[data-src="lbr_libelle"] span').text();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			var code_HTML = '';

			code_HTML += '<div class="row">' +
				'<label class="col-lg-3 col-form-label" for="lbr_code">' + reponse[ 'L_Code' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<input id="lbr_code" class="form-control" type="text" value="' + lbr_code + '" disabled>' +
				'</div>' +
				'</div> <!-- .row -->' +
				'<div class="row">' +
				'<label class="col-lg-3 col-form-label" for="lng_id">' + reponse[ 'L_Langue' ] + '</label>' +
				'<div class="col-lg-2">' +
				'<input id="lng_id" class="form-control" type="text" value="*" disabled>' +
				'</div>' +
				'</div> <!-- .row -->' +
				'<div class="row">' +
				'<label class="col-lg-3 col-form-label" for="lng_id">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<input id="lng_id" class="form-control" type="text" value="' + reponse['L_Supprime_Tous_Libelles_Code'] + '" disabled>' +
				'</div>' +
				'</div> <!-- .row -->';

			var Bouton = reponse[ 'L_Supprimer' ];


			construireModal( 'idModalSupprimer',
				reponse['L_Titre_Supprimer'],
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalSupprimer',
				'modal-xl' );


			// Affiche la modale qui vient d'être créée
			$('#idModalSupprimer').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSupprimer').on('shown.bs.modal', function() {
				$('.btn-fermer').focus();
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalSupprimer').on('hidden.bs.modal', function() {
				$('#idModalSupprimer').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalSupprimer').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				$.ajax({
					url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer',
					type: 'POST',
					dataType: 'json',
					data: $.param({'lbr_id': Id_Referentiel, 'lbr_code': lbr_code, 'lng_id': lng_id, 'lbr_libelle': lbr_libelle}),
					success: function( reponse ) {
						var statut = reponse[ 'statut' ];
						var texteMsg = reponse[ 'texteMsg' ];

						if ( statut == 'success' ) {
							afficherMessage( texteMsg, statut, 'body' );

/*							$('#LBR_'+ Id_Referentiel).remove();

							$('#totalOccurrences').text( reponse['total'] );*/

							$('#idModalSupprimer').modal('hide'); // Cache la modale.

							trier( $( 'div#entete_tableau div.row div:first'), false );
						} else {
							afficherMessage( texteMsg, statut, '#idModalSupprimer' );
						}
					}
				});
			});
		}
	});
}
