function ModifierTypeFournisseur( tfr_id ) {
	var tfr_nom_code = $('#tfr_nom_code').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'tfr_id': tfr_id, 'tfr_nom_code': tfr_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#TFR_' + tfr_id).find('div[data-src="tfr_nom_code"]').find('span').text( tfr_nom_code );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
