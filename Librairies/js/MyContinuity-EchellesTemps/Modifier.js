function ModifierEchelleTemps( ete_id ) {
	var ete_poids = $('#ete_poids').val();
	var ete_nom_code = $('#ete_nom_code').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'ete_id': ete_id, 'ete_poids': ete_poids, 'ete_nom_code': ete_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#ETE_' + ete_id + ' div[data-src="ete_poids"] span').text( ete_poids );
				$('#ETE_' + ete_id + ' div[data-src="ete_nom_code"] span').text( ete_nom_code );
			}

			afficherMessage( texteMsg, statut, 'body' );

		}
	});
}
