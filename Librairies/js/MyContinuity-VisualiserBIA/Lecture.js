$(function() {
	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'sct_id': sct_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];
				var pbSynchronisation = 0;

				if (statut == 'success') {
					$('#s_cmp_id option').remove();
					$('#s_ent_id option').remove();

					if ( reponse['cmp_id'] == '---' || reponse['cmp_id'] == '' ) {
						$('#s_cmp_id').append('<option value="">---</option>');
						afficherMessageCorps(reponse['L_Societe_Sans_Campagne'], reponse['L_Gestion_Campagnes']);
						pbSynchronisation = 1;
					} else {
						// Mise à jour de la liste déroulante des Campagnes associées à la Société
						for (let element of reponse['Liste_Campagnes']) {
							$('#s_cmp_id').append('<option value="' + element.cmp_id + '">' + element.cmp_date + '</option>');
						}
					}

					if ( reponse['ent_id'] == '---' || reponse['ent_id'] == '' ) {
						$('#s_ent_id').append('<option value="">---</option>');
						if (pbSynchronisation == 0) {
							afficherMessageCorps(reponse['L_Campagne_Sans_Entite'], reponse['L_Gestion_Entites']);
						}
					} else {
						// Mise à jour de la liste déroulante des Campagnes associées à la Société
						for (let element of reponse['Liste_Entites']) {
							$('#s_ent_id').append('<option value="' + element.ent_id + '">' + element.ent_nom + '</option>');
						}
					}

					if ( reponse['sct_id'] != '---' && reponse['sct_id'] != ''
					 && reponse['cmp_id'] != '---' && reponse['cmp_id'] != ''
					 && reponse['ent_id'] != '---' && reponse['ent_id'] != '' ) {
						afficherSynthese(reponse['ent_id']);
					}
				} else {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);
				}
			}
		});
	});

	// Active l'écoute du "select" sur le changement de Campagne.
	$('#s_cmp_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var cmp_id = $('#s_cmp_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Campagne',
			type: 'POST',
			data: $.param({/*'trier': sens_recherche,*/ 'cmp_id': cmp_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];

				if (statut == 'success') {
						$('#s_ent_id option').remove();

						if ( reponse['ent_id'] == '---' || reponse['ent_id'] == '' ) {
							$('#s_ent_id').append('<option value="">---</option>');
							//if (pbSynchronisation == 0) {
								afficherMessageCorps(reponse['L_Campagne_Sans_Entite'], reponse['L_Gestion_Entites']);
							//}
						} else {
							// Mise à jour de la liste déroulante des Campagnes associées à la Société
							for (let element of reponse['Liste_Entites']) {
								$('#s_ent_id').append('<option value="' + element.ent_id + '">' + element.ent_nom + '</option>');
							}
							$('#s_ent_id').append('<option value="*">' + reponse['L_Toutes'] + '</option>');
						}
						var texteMsg = reponse['texteMsg'];

						afficherMessage(texteMsg, statut);

						afficherSynthese( reponse['ent_id'] );
					} else {
						var texteMsg = reponse['texteMsg'];

						if (texteMsg == 'vide') {
							$('#s_ent_id option').remove();
							$('#s_ent_id').prepend('<option value="">---</option>');
						}
						
						afficherMessage(texteMsg, statut);
					}
			}
		});
	});

	// Active l'écoute du "select" sur le changement d'Entité.
	$('#s_ent_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var ent_id = $('#s_ent_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Entite',
			type: 'POST',
			data: $.param({/*'trier': sens_recherche,*/ 'ent_id': ent_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];

				if (statut == 'success') {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);

					afficherSynthese(reponse['ent_id']);
				} else {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);
				}
			}
		});
	});

	afficherSynthese($('#s_ent_id').val());
});



function afficherMessageCorps(Libelle_Message, Libelle_Bouton) {
	$('#corps_ecran').html(
		'<h2 class="text-center">' + Libelle_Message + '</h2>' +
		'<p class="text-center"><button class="btn btn-primary">' + Libelle_Bouton + '</button></p>'
	);
}



function afficherSynthese(ent_id) {
	if (ent_id == '*') {
		var Action = 'AJAX_Synthese_Gloable';
	} else {
		var Action = 'AJAX_Synthese_Specifique';
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=' + Action,
		type: 'POST',
		data: $.param({'ent_id': ent_id }),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function(reponse) {
			var statut = reponse['statut'];

			if (statut == 'success') {
				var texteHTML = reponse['texteHTML'];

				$('#corps_tableau').html(
					texteHTML
				);
				
				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage(texteMsg, statut);
			}
		}
	});
}
