function ModifierApplication( app_id ) {
	var app_nom = $('#app_nom').val();
	var frn_id = $('#frn_id').val();
	var sct_id = $('#sct_id').val();
	var frn_nom = $('#frn_id option:selected').text();
	var app_hebergement = $('#app_hebergement').val();
	var app_niveau_service = $('#app_niveau_service' ).val()
	var app_description = $('#app_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'app_id': app_id, 'app_nom': app_nom, 'app_hebergement': app_hebergement,
			'app_niveau_service': app_niveau_service, 'app_description': app_description,
			'frn_id': frn_id, 'frn_nom': frn_nom, 'sct_id': sct_id}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			var libelle_sct_id = $('#sct_id option:selected').text();

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#APP_' + app_id + ' div[data-src="app_nom"] span').text( app_nom );
				if (frn_id == '') {
					frn_nom = '';
				}
				$('#APP_' + app_id + ' div[data-src="frn_id"] span').text( frn_nom );
				$('#APP_' + app_id + ' div[data-src="app_hebergement"] span').text( app_hebergement );
				$('#APP_' + app_id + ' div[data-src="app_niveau_service"] span').text( app_niveau_service );
				$('#APP_' + app_id + ' div[data-src="app_description"] span').text( app_description );
				$('#APP_' + app_id + ' div[data-src="sct_id"] span').text( libelle_sct_id );
			}

			afficherMessage( texteMsg, statut, 'body' );

		}
	});
}
