function modifier_DMIA_Activite( mim_id, n_mim_id, act_id, ete_id ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'act_id': act_id, 'ete_id': ete_id, 'mim_id': mim_id, 'n_mim_id': n_mim_id}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Récupère les informations utiles pour mettre à jour l'écran.
				var nim_couleur = $('#mim_id-' + n_mim_id).attr('data-nim_couleur');
				var nim_numero = $('#mim_id-' + n_mim_id).attr('data-nim_numero');
				var tim_nom = $('#mim_id-' + n_mim_id).attr('data-tim_nom');

				// Met à jour les champs de l'occurrence modifiée.
				var Cellule_Cible = '#ACT_' + act_id + ' tr.cellule-valeur td[data-ete_id="' + ete_id + '"]';
				$(Cellule_Cible).css( 'background-color', '#' + nim_couleur );
				$(Cellule_Cible).attr( 'data-mim_id', n_mim_id );
				$(Cellule_Cible).attr( 'data-nim_numero', nim_numero );
				$(Cellule_Cible).text( nim_numero );

				var Cellule_Cible = '#ACT_' + act_id + ' tr.cellule-libelle td[data-ete_id="' + ete_id + '"]';
				$(Cellule_Cible).text( tim_nom );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
