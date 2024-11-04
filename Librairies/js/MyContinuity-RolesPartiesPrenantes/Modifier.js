function ModifierRolePartiePrenante( rpp_id ) {
	var rpp_nom_code = $('#rpp_nom_code').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'rpp_id': rpp_id, 'rpp_nom_code': rpp_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#RPP_' + rpp_id).find('div[data-src="rpp_nom_code"]').find('span').text( rpp_nom_code );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
