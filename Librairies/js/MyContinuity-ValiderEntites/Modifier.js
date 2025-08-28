function ValiderEntite( ent_id ) {
	var ppr_id_validation = $('#ppr_id_validation').val();
	var cmen_date_validation = $('#cmen_date_validation').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Valider',
		type: 'POST',
		data: $.param({'ent_id': ent_id, 'ppr_id_validation': ppr_id_validation, 'cmen_date_validation': cmen_date_validation}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalEntite').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				var Nom_Valideur = reponse['infos_validation'].ppr_nom + ' ' + reponse['infos_validation'].ppr_prenom
				$('#ENT_'+ent_id+' div[data-src="cmen_date_validation"] span').text(reponse['infos_validation'].cmen_date_validation);
				$('#ENT_'+ent_id+' div[data-src="ppr_id_validation"] span').text(Nom_Valideur);
			} else {
				afficherMessage( texteMsg, statut, '#idModalEntite', 0, 'n' );
			}
		}
	});
}
