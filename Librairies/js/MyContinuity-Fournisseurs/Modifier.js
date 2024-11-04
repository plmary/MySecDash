function ModifierFournisseur( frn_id ) {
	var frn_nom = $('#frn_nom').val();
	var tfr_id = $('#tfr_id').val();
	var tfr_code = $('#tfr_id option:selected').text();
	var frn_description = $('#frn_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'frn_id': frn_id, 'frn_nom': frn_nom, 'tfr_id': tfr_id, 'frn_description': frn_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#FRN_' + frn_id).find('div[data-src="frn_nom"]').find('span').text( frn_nom );
				$('#FRN_' + frn_id).find('div[data-src="tfr_id"]').find('span').text( tfr_code );
				$('#FRN_' + frn_id).find('div[data-src="frn_description"]').find('span').text( frn_description );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
