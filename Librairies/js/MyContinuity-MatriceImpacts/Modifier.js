function ModifierNiveau( nim_id ) {
	var nim_poids = $('#nim_poids').val();
	var nim_numero = $('#nim_numero').val();
	var nim_nom_code = $('#nim_nom_code').val();
	var nim_couleur = $('#nim_couleur option:selected').attr('data-color').substring(1);

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier_Niveau',
		type: 'POST',
		data: $.param({'nim_id': nim_id, 'nim_poids': nim_poids,
			'nim_numero': nim_numero, 'nim_nom_code': nim_nom_code,
			'nim_couleur': nim_couleur}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalMaJNiveau').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$('[data-nim_id="'+nim_id+'"] div').css( 'background-color', '#'+nim_couleur );
				$('[data-nim_id="'+nim_id+'"] div:first button').text( nim_numero+' - '+nim_nom_code );
			} else {
				afficherMessage( texteMsg, statut, '#idModalMaJNiveau', 0, 'n' );
			}
		}
	});
}


function ModifierType( tim_id ) {
	var tim_poids = $('#tim_poids').val();
	var tim_nom_code = $('#tim_nom_code').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier_Type',
		type: 'POST',
		data: $.param({'tim_id': tim_id, 'tim_poids': tim_poids, 'tim_nom_code': tim_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalMaJType').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$('div#entete_tableau [data-tim_id="'+tim_id+'"] button').text( tim_nom_code );
			} else {
				afficherMessage( texteMsg, statut, '#idModalMaJType', 0, 'n' );
			}
		}
	});
}


function ModifierDescription(mim_id, nim_id, tim_id) {
	mim_description = $('#mim_description').summernote('code');

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier_Description',
		type: 'POST',
		data: $.param({'mim_id': mim_id, 'mim_description': mim_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {
				$('#idModalMaJDescription').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$('#description-'+nim_id+'-'+tim_id).html(mim_description);
			} else {
				afficherMessage( texteMsg, statut, '#idModalMaJDescription', 0, 'n' );
			}
		}
	});
}
