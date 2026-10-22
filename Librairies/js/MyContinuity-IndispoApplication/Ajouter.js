// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalAjouterModifier();
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function ajouterEquipeInterneListe( L_ERR_Champ_Obligatoire ) {
	var ein_nom = $('#ein_nom_n').val();
	var ein_description = $('#ein_description_n').val();

	if (ein_nom == '') {
		$('#ein_nom_n').focus();
		var LibelleChamp = $('#ein_nom_n').attr('placeholder');
		afficherMessage( LibelleChamp + ' : ' + L_ERR_Champ_Obligatoire, 'erreur', 'body' );
		return -1;
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Equipe_Interne',
		type: 'POST',
		data: $.param({'ein_nom': ein_nom, 'ein_description': ein_description}),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				ein_id = reponse['ein_id'];

				$('select#ein_id_n').append(
					'<option value="' + ein_id +'">' +
					ein_nom +
					'</option>'
				);

				$('select#ein_id_n').val(ein_id);
			}

			afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
		}
	});
}


function ajouterResponsableEquipe( L_ERR_Champs_Obligatoires, L_Interne ) {
	var ppr_nom = $('#ppr_nom_responsable').val();
	var ppr_prenom = $('#ppr_prenom_responsable').val();

	if (ppr_nom == '') {
		$('#ppr_nom_responsable').focus();
		afficherMessage( L_ERR_Champs_Obligatoires, 'erreur', 'body' );
		return -1;
	}

	if (ppr_prenom == '') {
		$('#ppr_prenom_responsable').focus();
		afficherMessage( L_ERR_Champs_Obligatoires, 'erreur', 'body' );
		return -1;
	}

	ppr_nom = ppr_nom.toUpperCase();
	ppr_prenom = transformePrenom( ppr_prenom );

	if ($('#ppr_interne_responsable').is(':checked') == true) {
		ppr_interne = 1;
		t_ppr_interne = ' (' + L_Interne + ')';
	} else {
		ppr_interne = 0;
		t_ppr_interne = '';
	}


	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Responsable_Equipe',
		type: 'POST',
		data: $.param({'ppr_nom': ppr_nom, 'ppr_prenom': ppr_prenom, 'ppr_interne': ppr_interne}),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				ppr_id = reponse['ppr_id'];

				$('select#ppr_id_n').append(
					'<option value="' + ppr_id +'">' +
					ppr_nom + ' ' + ppr_prenom + ' (' + L_Interne + ')' +
					'</option>'
				);

				$('select#ppr_id_n').val(ppr_id);
	
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
			}
		}
	});
}


function ajouterPrestataire() {
	var frn_nom = $('#frn_nom_n').val();
	var tfr_id = $('#tfr_id_n').val();
	var frn_description = $('#frn_description_n').val();

	if (frn_nom == '') {
		$('#frn_nom_n').focus();
		//afficherMessage( reponse['L_Champ_Obligatoire'], 'erreur', 'body' );
		return -1;
	}

	if (tfr_id == '') {
		$('#tfr_id_n').focus();
		//afficherMessage( reponse['L_Champ_Obligatoire'], 'erreur', 'body' );
		return -1;
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Prestataire',
		type: 'POST',
		data: $.param({'frn_nom': frn_nom, 'tfr_id': tfr_id, 'frn_description': frn_description}),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				var frn_id = reponse['frn_id'];

				$('select#frn_id_n').append(
					'<option value="' + frn_id +'">' +
					frn_nom +
					'</option>'
				);

				$('select#frn_id_n').val(frn_id);

				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
			}
		}
	});
}



function ajouterApplicationListe( L_ERR_Champ_Obligatoire ) {
	var app_nom = $('#ZoneCreerApplication #app_nom').val();
	var app_description = $('ZoneCreerApplication #app_description').val();
	var app_hebergement = $('ZoneCreerApplication #app_hebergement').val();
	var frn_id = $('ZoneCreerApplication #frn_id').val();
	var app_niveau_service = $('ZoneCreerApplication #app_niveau_service').val();

	if (app_nom == '') {
		$('#ZoneCreerApplication #app_nom').focus();
		var LibelleChamp = $('#ZoneCreerApplication #app_nom').attr('placeholder');
		afficherMessage( LibelleChamp + ' : ' + L_ERR_Champ_Obligatoire, 'erreur', 'body' );
		return -1;
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Application',
		type: 'POST',
		data: $.param({'app_nom': app_nom, 'app_description': app_description, 'app_hebergement': app_hebergement,
			'frn_id': frn_id, 'app_niveau_service': app_niveau_service }),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				app_id = reponse['app_id'];

				$('select#app_id_n').append(
					'<option value="' + app_id +'">' + app_nom + '</option>'
				);

				$('select#app_id_n').val(app_id);
			}

			afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
		}
	});
}
