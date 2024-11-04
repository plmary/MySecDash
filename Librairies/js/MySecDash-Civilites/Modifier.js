function ModifierCivilite( Id ) {
	var Last_Name = $('#cvl_nom').val();
	var First_Name = $('#cvl_prenom').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'cvl_id': Id,'last_name': Last_Name,'first_name': First_Name}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalCivilite').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#CVL_' + Id).find('div[data-src="cvl_nom"]').find('span').text( Last_Name );
				$('#CVL_' + Id).find('div[data-src="cvl_prenom"]').find('span').text( First_Name );
			}

			afficherMessage( texteMsg, statut, 'body' );

		}
	});
}
