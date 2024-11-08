function ModifierActivite( act_id ) {
	var act_nom = $('#act_nom').val();
	var ppr_id_responsable = $('#ppr_id_responsable').val();
	var ppr_nom_responsable = $('#ppr_id_responsable option:selected').text();
	var ppr_id_suppleant = $('#ppr_id_suppleant').val();
	var sts_id_nominal = $('#sts_id_nominal').val();
	var sts_id_nominal_old = $('#sts_id_nominal').attr('data-old');
	var sts_id_secours = $('#sts_id_secours').val();
	var sts_id_secours_old = $('#sts_id_secours').attr('data-old');
	var act_description = $('#act_description').val();
	var act_teletravail = $('#act_teletravail').val();
	var act_dependances_internes_amont = $('#act_dependances_internes_amont').val();
	var act_dependances_internes_aval = $('#act_dependances_internes_aval').val();
	var act_effectifs_en_nominal = Number( $('#act_effectifs_en_nominal').val() );
	var act_effectifs_a_distance = Number( $('#act_effectifs_a_distance').val() );
	var act_justification_dmia = $('#act_justification_dmia').val();
	var total_dmia = Number($('#ACT_'+act_id+' .btn_ete').text());

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

			if ( $(element).attr('data-old_value') == 0) {
				Liste_APP_Ajouter.push([app_id, ete_id_dima, ete_id_pdma, acap_donnees, acap_palliatif]);
				total_applications += 1;
			} else {
				Liste_APP_Modifier.push([app_id, ete_id_dima, ete_id_pdma, acap_donnees, acap_palliatif]);
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

	var nim_nom_code = $('#act_niveau_impact_max').attr('title');
	var nim_couleur = $('#act_niveau_impact_max').css('background-color');
	var nim_numero = $('#act_niveau_impact_max').val();
	var ete_nom_code = $('#act_dmia_max').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'act_id': act_id, 'act_nom': act_nom, 'ppr_id_responsable': ppr_id_responsable,
			'ppr_id_suppleant': ppr_id_suppleant,
			'sts_id_nominal': sts_id_nominal, 'sts_id_nominal_old': sts_id_nominal_old,
			'sts_id_secours': sts_id_secours, 'sts_id_secours_old': sts_id_secours_old,
			'act_description': act_description, 'act_teletravail': act_teletravail,
			'dmia_activite': dmia_activite, 
			'act_dependances_internes_amont': act_dependances_internes_amont,
			'act_dependances_internes_aval': act_dependances_internes_aval,
			'act_effectifs_en_nominal': act_effectifs_en_nominal, 'act_effectifs_a_distance': act_effectifs_a_distance,
			'act_justification_dmia': act_justification_dmia,
			'personnes_cles_a_ajouter': Liste_CLE_Ajouter, 'personnes_cles_a_supprimer': Liste_CLE_Supprimer,
			'personnes_cles_a_modifier': Liste_CLE_Modifier,
			'applications_a_ajouter': Liste_APP_Ajouter, 'applications_a_supprimer': Liste_APP_Supprimer,
			'applications_a_modifier': Liste_APP_Modifier,
			'fournisseurs_a_ajouter': Liste_FRN_Ajouter, 'fournisseurs_a_supprimer': Liste_FRN_Supprimer,
			'fournisseurs_a_modifier': Liste_FRN_Modifier}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				// Met à jour les différents champs de l'occurrence modifiée.
				//$('#ACT_' + act_id).find('div[data-src="act_nom"]').find('span').text( act_nom );
				//$('#ACT_' + act_id).find('div[data-src="ppr_id_responsable"]').find('span').text( ppr_nom_responsable );
				$('#ACT_' + act_id + ' div[data-src="act_nom"] span').text( act_nom );
				$('#ACT_' + act_id + ' div[data-src="ppr_id_responsable"] span').text( ppr_nom_responsable );

				$('#ACT_' + act_id + ' div[data-src="nim_poids"] button').text( nim_numero );
				$('#ACT_' + act_id + ' div[data-src="nim_poids"] button').css('background-color', nim_couleur);
				$('#ACT_' + act_id + ' div[data-src="nim_poids"] button').attr('title', nim_nom_code);

				$('#ACT_' + act_id + ' div[data-src="ete_poids"] span').text(ete_nom_code);


				var total_sites = 1;
				if (sts_id_secours != '') total_sites += 1;

				$('#ACT_' + act_id + ' .btn_sts').text( total_sites );
				//$('#ACT_' + act_id + ' .btn_ete').text( total_dmia );
				$('#ACT_' + act_id + ' .btn_ppr').text( total_personnes_cles );
				$('#ACT_' + act_id + ' .btn_app').text( total_applications );
				$('#ACT_' + act_id + ' .btn_frn').text( total_fournisseurs );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
