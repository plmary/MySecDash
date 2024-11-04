function ModifierPartiePrenante( ppr_id ) {
	var ppr_nom = $('#ppr_nom').val();
	var ppr_prenom = $('#ppr_prenom').val();
	var ppr_interne = $('#ppr_interne').val();
	var ppr_interne_libelle = $('#ppr_interne option:selected').text();
	var ppr_description = $('#ppr_description').val();

	ppr_nom = ppr_nom.toUpperCase();
	ppr_prenom = ppr_prenom[0].toUpperCase()+ppr_prenom.substring(1);

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'ppr_id': ppr_id, 'ppr_nom': ppr_nom, 'ppr_prenom': ppr_prenom,
			'ppr_interne': ppr_interne, 'ppr_description': ppr_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#PPR_' + ppr_id).find('div[data-src="ppr_nom"]').find('span').text( ppr_nom );
				$('#PPR_' + ppr_id).find('div[data-src="ppr_prenom"]').find('span').text( ppr_prenom );
				$('#PPR_' + ppr_id).find('div[data-src="ppr_interne"]').find('span').text( ppr_interne_libelle );
				$('#PPR_' + ppr_id).find('div[data-src="ppr_description"]').find('span').text( ppr_description );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
