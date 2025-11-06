function ModifierApplication( app_id ) {
	var app_nom = $('#app_nom').val();
	var app_nom_alias = $('#app_nom_alias').val();
	var ete_id_dima_dsi = $('#ete_id_dima_dsi').val();
	var libelle_ete_id_dima_dsi = $('#ete_id_dima_dsi option:selected').text();
	var scap_description_dima = $('#scap_description_dima').val();
	var ete_id_pdma_dsi = $('#ete_id_pdma_dsi').val();
	var libelle_ete_id_pdma_dsi = $('#ete_id_pdma_dsi option:selected').text();
	var scap_description_pdma = $('#scap_description_pdma').val();
	var frn_id = $('#frn_id').val();
	var sct_id = $('#sct_id').val();
	var frn_nom = $('#frn_id option:selected').text();
	var app_hebergement = $('#app_hebergement').val();
	var app_niveau_service = $('#app_niveau_service' ).val()
	var app_description = $('#app_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'app_id': app_id, 'app_nom': app_nom, 'app_nom_alias': app_nom_alias, 'app_hebergement': app_hebergement,
			'ete_id_dima_dsi': ete_id_dima_dsi, 'libelle_ete_id_dima_dsi': libelle_ete_id_dima_dsi, 'scap_description_dima': scap_description_dima,
			'ete_id_pdma_dsi': ete_id_pdma_dsi, 'libelle_ete_id_pdma_dsi': libelle_ete_id_pdma_dsi, 'scap_description_pdma': scap_description_pdma,
			'app_niveau_service': app_niveau_service, 'app_description': app_description,
			'frn_id': frn_id, 'frn_nom': frn_nom, 'sct_id': sct_id}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#APP_' + app_id + ' div[data-src="app_nom"] span').text( app_nom );
				$('#APP_' + app_id + ' div[data-src="app_nom_alias"] span').text( app_nom_alias );
				$('#APP_' + app_id + ' div[data-src="ete_nom_dima_dsi"] span').text( libelle_ete_id_dima_dsi );
				$('#APP_' + app_id + ' div[data-src="ete_nom_pdma_dsi"] span').text( libelle_ete_id_pdma_dsi );
			}

			afficherMessage( texteMsg, statut, 'body' );

		}
	});
}
