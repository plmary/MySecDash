function ModifierApplication( ain_id ) {
	var ain_libelle = $('#ain_libelle').val();
	var tap_id = $('#tap_id').val();
	var tap_code = $('#tap_id option:selected' ).text()
	var ain_localisation = $('#ain_localisation').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'ain_id': ain_id,'ain_libelle': ain_libelle,'tap_id': tap_id,'ain_localisation': ain_localisation}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalApplication').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#AIN_' + ain_id).find('div[data-src="ain_libelle"]').find('span').text( ain_libelle );
				$('#AIN_' + ain_id).find('div[data-src="tap_code"]').find('span').text( tap_code );
				$('#AIN_' + ain_id).find('div[data-src="ain_localisation"]').find('span').text( ain_localisation );
			}

			afficherMessage( texteMsg, statut, 'body' );

		}
	});
}
