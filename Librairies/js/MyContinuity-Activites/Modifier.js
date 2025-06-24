function ModifierActivite( act_id ) {
	var act_nom = $('#act_nom').val();
	var ppr_id_responsable = $('#ppr_id_responsable').val();
	var ppr_nom_responsable = $('#ppr_id_responsable option:selected').text();
	var ppr_id_suppleant = $('#ppr_id_suppleant').val();
	var act_description = $('#act_description').val();
	var act_teletravail = $('#act_teletravail').val();
	var act_dependances_internes_amont = $('#act_dependances_internes_amont').val();
	var act_dependances_internes_aval = $('#act_dependances_internes_aval').val();
	var act_effectifs_en_nominal = Number( $('#act_effectifs_en_nominal').val() );
	var act_taux_occupation = Number( $('#act_taux_occupation').val() );
	var act_effectifs_a_distance = Number( $('#act_effectifs_a_distance').val() );
	var act_justification_dmia = $('#act_justification_dmia').val();
	var total_dmia = Number($('#ACT_'+act_id+' .btn_ete').text());
	var act_strategie_montee_en_charge = $('#act_strategie_montee_en_charge').val();
	var act_description_entraides = $('#act_description_entraides').val();
	var cmen_effectif_total = Number( $('#cmen_effectif_total').val() );

	if ( cmen_effectif_total == Number( $('#cmen_effectif_total').attr("data-old_value")) ) {
		cmen_effectif_total = null;
	}

	var dmia_activite = [];

	$('[id^="echelle-1-"]').each(function(index, value){
		var ete_id = $(value).attr('data-ete_id');
		var mim_id = $(value).attr('data-mim_id');
		var mim_id_old = $(value).attr('data-mim_id-old');

		if (mim_id != mim_id_old) {
			if (mim_id_old == undefined) {
				total_dmia += 1;
			}
			dmia_activite.push(ete_id+'='+mim_id_old+'-'+mim_id);
		}
	});

	
	var Liste_STS_Ajouter = [];
	var Liste_STS_Modifier = [];
	var Liste_STS_Supprimer = [];
	var total_sites = Number($('#ACT_'+act_id+' .btn_sts').text());

	$('input[id^="sts-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) { // Cas d'un ajout, car n'était pas coché avant.
				sts_id = $(element).attr('id').split('-')[1];

				acst_type_site = $('#acst_type_site-'+sts_id).val();
				if ( acst_type_site == '' ) {
					$('#afficher_sites').trigger('click');
					$('#acst_type_site-'+sts_id).css('border-color', 'red').focus();
					return -1;
				}
				Liste_STS_Ajouter.push([sts_id, acst_type_site]);
				total_sites += 1;
			} else { // Cas d'une possible modification, coché et qui reste coché
				sts_id = $(element).attr('id').split('-')[1];
				acst_type_site = $('#acst_type_site-'+sts_id).val();
				acst_type_site_old = $('#acst_type_site-'+sts_id).attr('data-old_value');

				if ( acst_type_site != acst_type_site_old ) {
					Liste_STS_Modifier.push([sts_id, acst_type_site]);
				}
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) { // Cas d'une suppression, n'est plus coché alors que coché précédemment.
				sts_id = $(element).attr('id').split('-')[1];
				Liste_STS_Supprimer.push(sts_id);
				total_sites -= 1;
			}
		}
	});


	var Liste_CLE_Ajouter = [];
	var Liste_CLE_Modifier = [];
	var Liste_CLE_Supprimer = [];
	var total_personnes_cles = Number($('#ACT_'+act_id+' .btn_ppr').text());

	$('input[id^="cle-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			ppr_id = $(element).attr('id').split('-')[1];
			ppac_description = $('#ppac_description-'+ppr_id).val();
			if ( $(element).attr('data-old_value') == 0) {
				Liste_CLE_Ajouter.push([ppr_id, ppac_description]);
				total_personnes_cles += 1;
			} else {
				Liste_CLE_Modifier.push([ppr_id, ppac_description]);
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_CLE_Supprimer.push($(element).attr('id').split('-')[1]);
				total_personnes_cles -= 1;
			}
		}
	});


	var Liste_APP_Ajouter = [];
	var Liste_APP_Modifier = [];
	var Liste_APP_Supprimer = [];
	var total_applications = Number($('#ACT_'+act_id+' .btn_app').text());

	$('input[id^="choix_application-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			app_id = $(element).attr('id').split('-')[1];
			ete_id_dima = $('#ete_id_dima-'+app_id).val();
			ete_id_pdma = $('#ete_id_pdma-'+app_id).val();
			acap_donnees = $('#acap_donnees-'+app_id).val();
			acap_palliatif = $('#acap_palliatif-'+app_id).val();
			acap_hebergement = $('#acap_hebergement-'+app_id).val();
			acap_niveau_service = $('#acap_niveau_service-'+app_id).val();

			if ( $(element).attr('data-old_value') == 0) {
				Liste_APP_Ajouter.push([app_id, ete_id_dima, ete_id_pdma, acap_donnees, acap_palliatif, acap_hebergement, acap_niveau_service]);
				total_applications += 1;
			} else {
				Liste_APP_Modifier.push([app_id, ete_id_dima, ete_id_pdma, acap_donnees, acap_palliatif, acap_hebergement, acap_niveau_service]);
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_APP_Supprimer.push($(element).attr('id').split('-')[1]);
				total_applications -= 1;
			}
		}
	});


	var Liste_FRN_Ajouter = [];
	var Liste_FRN_Modifier = [];
	var Liste_FRN_Supprimer = [];
	var total_fournisseurs = Number($('#ACT_'+act_id+' .btn_frn').text());

	$('input[id^="choix_fournisseur-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			frn_id = $(element).attr('id').split('-')[1];
			ete_id = $('#ete_id-fournisseur-'+frn_id).val();
			acfr_consequence_indisponibilite = $('#acfr_consequence_indisponibilite-'+frn_id).val();
			acfr_palliatif_tiers = $('#acfr_palliatif_tiers-'+frn_id).val();

			if ( $(element).attr('data-old_value') == 0) {
				Liste_FRN_Ajouter.push([frn_id, ete_id, acfr_consequence_indisponibilite,
					acfr_palliatif_tiers]);
				total_fournisseurs += 1;
			} else {
				Liste_FRN_Modifier.push([frn_id, ete_id, acfr_consequence_indisponibilite,
					acfr_palliatif_tiers]);
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_FRN_Supprimer.push($(element).attr('id').split('-')[1]);
				total_fournisseurs -= 1;
			}
		}
	});


	var Liste_PPR_Ajouter = [];
	var Liste_PPR_Modifier = [];

	$('input[id^="personnes_prioritaires-"]').each(function(index, element){
		if ($(element).val() != $(element).attr('data-old_value')) {
			ete_id = $(element).attr('id').split('-')[1];

			if ( $(element).attr('data-old_value') == '') {
				Liste_PPR_Ajouter.push([act_id, ete_id, $(element).val()]);
			} else {
				Liste_PPR_Modifier.push([act_id, ete_id, $(element).val()]);
			}
		}
	});

	var nim_nom_code = $('#act_niveau_impact_max').attr('title');
	var nim_couleur = $('#act_niveau_impact_max').css('background-color');
	var nim_numero = $('#act_niveau_impact_max').val();
	var ete_nom_code = $('#act_dmia_max').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'act_id': act_id, 'act_nom': act_nom,
			'ppr_id_responsable': ppr_id_responsable, 'ppr_id_suppleant': ppr_id_suppleant,
			'act_description': act_description, 'act_teletravail': act_teletravail,
			'dmia_activite': dmia_activite, 'act_taux_occupation': act_taux_occupation,
			'act_dependances_internes_amont': act_dependances_internes_amont,
			'act_dependances_internes_aval': act_dependances_internes_aval,
			'act_effectifs_en_nominal': act_effectifs_en_nominal, 'act_effectifs_a_distance': act_effectifs_a_distance,
			'act_strategie_montee_en_charge': act_strategie_montee_en_charge, 'act_description_entraides': act_description_entraides,
			'act_justification_dmia': act_justification_dmia, 'cmen_effectif_total': cmen_effectif_total, 
			'personnes_cles_a_ajouter': Liste_CLE_Ajouter, 'personnes_cles_a_supprimer': Liste_CLE_Supprimer,
			'personnes_cles_a_modifier': Liste_CLE_Modifier,
			'applications_a_ajouter': Liste_APP_Ajouter, 'applications_a_supprimer': Liste_APP_Supprimer,
			'applications_a_modifier': Liste_APP_Modifier,
			'fournisseurs_a_ajouter': Liste_FRN_Ajouter, 'fournisseurs_a_supprimer': Liste_FRN_Supprimer,
			'fournisseurs_a_modifier': Liste_FRN_Modifier,
			'sites_a_ajouter': Liste_STS_Ajouter, 'sites_a_supprimer': Liste_STS_Supprimer,
			'sites_a_modifier': Liste_STS_Modifier,
			'personnes_prioritaires_a_ajouter': Liste_PPR_Ajouter,
			'personnes_prioritaires_a_modifier': Liste_PPR_Modifier}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#ACT_' + act_id + ' div[data-src="act_nom"] span').text( act_nom );
				$('#ACT_' + act_id + ' div[data-src="ppr_id_responsable"] span').text( ppr_nom_responsable );

				$('#ACT_' + act_id + ' div[data-src="nim_poids"] button').text( nim_numero );
				if ( nim_numero > 0 ) {
					$('#ACT_' + act_id + ' div[data-src="nim_poids"] button').css('background-color', nim_couleur);
				}
				$('#ACT_' + act_id + ' div[data-src="nim_poids"] button').attr('title', nim_nom_code);

				$('#ACT_' + act_id + ' div[data-src="ete_poids"] span').text(ete_nom_code);

				$('#ACT_' + act_id + ' .btn_sts').text( total_sites );
				$('#ACT_' + act_id + ' .btn_ppr').text( total_personnes_cles );
				$('#ACT_' + act_id + ' .btn_app').text( total_applications );
				$('#ACT_' + act_id + ' .btn_frn').text( total_fournisseurs );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
