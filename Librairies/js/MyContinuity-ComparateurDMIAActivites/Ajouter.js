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

function AjouterActivite() {
	var act_nom = $('#act_nom').val();
	var ppr_id_responsable = $('#ppr_id_responsable').val();
	var ppr_id_suppleant = $('#ppr_id_suppleant').val();
	var sts_id_nominal = $('#sts_id_nominal').val();
	var sts_id_secours = $('#sts_id_secours').val();
	var act_description = $('#act_description').val();
	var act_teletravail = $('#act_teletravail').val();
	var act_dependances_internes_amont = $('#act_dependances_internes_amont').val();
	var act_dependances_internes_aval = $('#act_dependances_internes_aval').val();
	var act_effectifs_en_nominal = Number( $('#act_effectifs_en_nominal').val() );
	var act_effectifs_a_distance = Number( $('#act_effectifs_a_distance').val() );
	var act_justification_dmia = $('#act_justification_dmia').val();
	var dmia_activite = [];
	var total_dmia = 0;

	$('[id^="echelle-1-"]').each(function(index, value){
		var ete_id = $(value).attr('data-ete_id');
		var mim_id = $(value).attr('data-mim_id');
		var mim_id_old = $(value).attr('data-mim_id-old');

		if (mim_id != undefined && mim_id_old == undefined) {
			total_dmia += 1;
			dmia_activite.push(ete_id+'='+mim_id);
		}
	});

	var Liste_PPR_CLE_Ajouter = [];
	var total_personnes_cles = 0;

	$('input[id^="cle-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				ppr_id = $(element).attr('id').split('-')[1];
				ppac_description = $('#ppac_description-'+ppr_id).val();
				Liste_PPR_CLE_Ajouter.push([ppr_id, ppac_description]);
				total_personnes_cles += 1;
			}
		}
	});

	var Liste_APP_Ajouter = [];
	var total_applications = 0;

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
			}
		}
	});

	var Liste_FRN_Ajouter = [];
	var total_fournisseurs = 0;

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
			}
		}
	});

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	var nim_nom_code = $('#act_niveau_impact_max').attr('title');
	var nim_couleur = $('#act_niveau_impact_max').css('background-color');
	var nim_numero = $('#act_niveau_impact_max').val();
	var ete_nom_code = $('#act_dmia_max').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'act_nom': act_nom, 'ppr_id_responsable': ppr_id_responsable,
			'act_teletravail': act_teletravail,
			'ppr_id_suppleant': ppr_id_suppleant, 'sts_id_nominal': sts_id_nominal,
			'act_description': act_description, 'dmia_activite': dmia_activite, 'total_dmia': total_dmia,
			'sts_id_secours': sts_id_secours, 'act_dependances_internes_amont': act_dependances_internes_amont,
			'act_dependances_internes_aval': act_dependances_internes_aval,
			'act_effectifs_en_nominal': act_effectifs_en_nominal, 'act_effectifs_a_distance': act_effectifs_a_distance,
			'act_justification_dmia': act_justification_dmia,
			'personnes_cles_a_ajouter': Liste_PPR_CLE_Ajouter, 'nim_nom_code': nim_nom_code,
			'nim_couleur': nim_couleur, 'nim_numero': nim_numero, 'ete_nom_code': ete_nom_code,
			'applications_a_ajouter': Liste_APP_Ajouter, 'fournisseurs_a_ajouter': Liste_FRN_Ajouter}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				var act_id = reponse['id'];

				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				var total_sites = 0;
				if (sts_id_nominal != '' && sts_id_nominal != null) total_sites += 1;
				if (sts_id_secours != '' && sts_id_secours != null) total_sites += 1;

				$('#ACT_' + act_id + ' .btn_sts').text( total_sites );
				$('#ACT_' + act_id + ' .btn_ete').text( dmia_activite.length );
				$('#ACT_' + act_id + ' .btn_ppr').text( total_personnes_cles );
				$('#ACT_' + act_id + ' .btn_app').text( total_applications );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#ACT_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#ACT_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Libelle = act_nom;

						ModalSupprimer( reponse['id'], Libelle );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}


function DupliquerActivite( act_id ) {
	var n_act_nom = $('#n_act_nom').val();
	var flag_dmia = $('#flag_dmia').is(':checked');
	var flag_sites = $('#flag_sites').is(':checked');
	var flag_fournisseurs = $('#flag_fournisseurs').is(':checked');
	var flag_applications = $('#flag_applications').is(':checked');
	var flag_personnes_cles = $('#flag_personnes_cles').is(':checked');

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Dupliquer',
		type: 'POST',
		data: $.param({'act_id': act_id, 'n_act_nom': n_act_nom, 'flag_dmia': flag_dmia, 'flag_sites': flag_sites,
			'flag_fournisseurs': flag_fournisseurs, 'flag_applications': flag_applications,
			'flag_personnes_cles': flag_personnes_cles}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				var act_id = reponse['act_id'];

				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

				var total = $( '#totalOccurrences' ).text();
				total = Number(total) + 1;
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Duplication
				if ( reponse[ 'droit_ajouter' ] == true ) {
					$('#ACT_' + act_id + ' .btn-dupliquer').click( function( event ){
						ModalDupliquer( act_id );
					});
				}

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#ACT_' + act_id + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( act_id );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#ACT_' + act_id + ' .btn-supprimer').click(function(){
						var Libelle = n_act_nom;

						ModalSupprimer( act_id, Libelle );
					});
				}
			} else {
				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function creerPersonneCle() {
	var ppr_nom = $('#ppr_nom_cle').val();
	var ppr_prenom = $('#ppr_prenom_cle').val();
	
	if (ppr_nom == '') {
		$('#ppr_nom_cle').focus();
		afficherMessage( reponse['L_ERR_Champs_Obligatoires'], 'erreur', 'body' );
		return -1;
	}
	
	if (ppr_prenom == '') {
		$('#ppr_prenom_cle').focus();
		afficherMessage( reponse['L_ERR_Champs_Obligatoires'], 'erreur', 'body' );
		return -1;
	}
	
	ppr_nom = ppr_nom.toUpperCase();
	ppr_prenom = transformePrenom( ppr_prenom );
	
	if ($('#ppr_interne_cle').is(':checked') == true) {
		ppr_interne = 1;
		t_ppr_interne = ' (' + reponse['L_Interne'] + ')';
	} else {
		ppr_interne = 0;
		t_ppr_interne = '';
	}
	
	
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_PartiePrenante',
		type: 'POST',
		data: $.param({'ppr_nom': ppr_nom, 'ppr_prenom': ppr_prenom, 'ppr_interne': ppr_interne}),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				ppr_id = reponse['ppr_id'];
				
				Nom_Complet = ppr_nom + ' ' + ppr_prenom + t_ppr_interne;
	
				Corps = '<div class="row liste">' +
					 '<div class="col-5">' +
					  '<div class="form-check">' + 
					   '<input class="form-check-input" type="checkbox" value="" data-old_value="" id="cle-'+ppr_id+'" checked>' +
					   '<label class="form-check-label" for="cle-'+ppr_id+'">'+Nom_Complet+'</label>' +
					  '</div> <!-- .form-check -->' +
					 '</div> <!-- .col-5 -->' +
					 '<label for="ppac_description-'+ppr_id+'" class="form-label col-2">'+reponse['L_Description']+'</label>' +
					 '<div class="col-5">' +
					  '<textarea id="ppac_description-'+ppr_id+'" type="text" class="form-control" placeholder=""></textarea>' +
					 '</div> <!-- .col-5 -->' +
					'</div> <!-- .row -->';
	
				$('#zone-personnes_cles').prepend( Corps );
	
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				$('#btn-fermer-zone-personne_cle').trigger('click');
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}


function creerFournisseur() {
	var frn_nom = $('#frn_nom').val();
	var tfr_id = $('#tfr_id').val();
	var frn_description = $('#frn_description').val();

	if (frn_nom == '') {
		$('#frn_nom').focus();
		afficherMessage( reponse['L_Champ_Obligatoire'], 'erreur', 'body' );
		return -1;
	}

	if (tfr_id == '') {
		$('#tfr_id').focus();
		afficherMessage( reponse['L_Champ_Obligatoire'], 'erreur', 'body' );
		return -1;
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Fournisseur',
		type: 'POST',
		data: $.param({'frn_nom': frn_nom, 'tfr_id': tfr_id,
			'frn_description': frn_description}),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				var frn_id = reponse['frn_id'];
				
				Nom_Complet = frn_nom;

				if (frn_description != '' && frn_description != null) {
					Nom_Complet += ' [' + frn_description + ']';
				}

				Corps = creerOccurrenceFournisseurDansListe(frn_id, Nom_Complet,
					0, ' checked', reponse['Liste_EchellesTemps'], reponse['L_DMIA'], '',
					reponse['L_Consequence_Indisponibilite'], '',
					reponse['L_Palliatif'], '',
					reponse['L_Aucun']);

				$('div#zone-fournisseurs div#liste-donnees').prepend( Corps );

				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				$('#btn-fermer-zone-fournisseur').trigger('click');
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}


function creerApplication() {
	app_nom = $('#app_nom').val();
	app_hebergement = $('#app_hebergement').val();
	app_niveau_service = $('#app_niveau_service').val();
	app_description = $('#app_description').val();

	if (app_nom == '') {
		$('#app_nom').focus();
		afficherMessage( reponse['L_ERR_Champs_Obligatoires'], 'erreur', 'body' );
		return -1;
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Application',
		type: 'POST',
		data: $.param({'app_nom': app_nom, 'app_hebergement': app_hebergement,
			'app_niveau_service': app_niveau_service, 'app_description': app_description}),
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				ppr_id = reponse['ppr_id'];
				
				Nom_Complet = app_nom;

				if (app_hebergement != '' && app_hebergement != null) {
					Nom_Complet += ' [' + app_hebergement + ']';
				}

				if (app_niveau_service != '' && app_niveau_service != null) {
					Nom_Complet += ' [' + app_niveau_service + ']';
				}

				if (app_description != '' && app_description != null) {
					Nom_Complet += ' [' + app_description + ']';
				}

				Corps = creerOccurrenceApplicationDansListe(reponse['app_id'], Nom_Complet,
					0, ' checked', reponse['Liste_EchellesTemps'], reponse['L_DMIA'], '',
					reponse['L_PDMA'], '', reponse['L_Palliatif'], '', '', reponse['L_Aucun']);

				$('div#zone-applications div#liste-donnees').prepend( Corps );

				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				$('#btn-fermer-zone-application').trigger('click');
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}


function ajouterRolePartiePrenante() {
	var n_rpp_nom_code = $('#n_rpp_nom_code').val();
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Role_PartiePrenante',
		type: 'POST',
		data: $.param({'n_rpp_nom_code': n_rpp_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse['statut'];

			if( statut == 'success' ){
				$('#rpp_id option').removeAttr('selected');
				$('#rpp_id').prepend(
					'<option value="' + reponse['rpp_id'] + '" selected>' + n_rpp_nom_code + '</option>'
				);

				$('#Zone-Ajout-Role-PartiePrenante').remove();
				$('#Selectionner-Role-PartiePrenante').show();
				
				$('#act_interne').focus();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function ajouterPartiePrenante( ppr_nom, ppr_prenom, ppr_interne, champ ) {
	if (ppr_interne == true) ppr_interne = 1;
	if (ppr_interne == false) ppr_interne = 0;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_PartiePrenante',
		type: 'POST',
		data: $.param({'ppr_nom': ppr_nom, 'ppr_prenom': ppr_prenom, 'ppr_interne': ppr_interne}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			afficherMessage( texteMsg, statut );

			if( statut == 'success' ) {
				$('#' + champ + ' option').removeAttr('selected');

				champ_responsable = '';
				champ_suppleant = '';

				if (champ == 'ppr_id_responsable') champ_responsable = 'selected';
				if (champ == 'ppr_id_suppleant') champ_suppleant = 'selected';

				$('#ppr_id_responsable').prepend('<option value="' + reponse['ppr_id'] + '" ' + champ_responsable + '>' + ppr_nom + ' ' + ppr_prenom + '</option>');
				$('#ppr_id_suppleant').prepend('<option value="' + reponse['ppr_id'] + '" ' + champ_suppleant + '>' + ppr_nom + ' ' + ppr_prenom + '</option>');

				return;
			} else {
				return -1;
			}
		}
	});
}


function ajouterSiteNominal( sts_nom ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Site',
		type: 'POST',
		data: $.param({'sts_nom': sts_nom}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			afficherMessage( texteMsg, statut );

			if( statut == 'success' ) {
				$('#sts_id_nominal option').removeAttr('selected');

				$('#sts_id_nominal').prepend('<option value="' + reponse['sts_id'] + '" selected>' + sts_nom + '</option>');
				$('#sts_id_secours').prepend('<option value="' + reponse['sts_id'] + '" selected>' + sts_nom + '</option>');

				return;
			} else {
				return -1;
			}
		}
	});
}


function ajouterSiteSecours( sts_nom ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Site',
		type: 'POST',
		data: $.param({'sts_nom': sts_nom}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			afficherMessage( texteMsg, statut );

			if( statut == 'success' ) {
				$('#sts_id_secours option').removeAttr('selected');

				$('#sts_id_secours').prepend('<option value="' + reponse['sts_id'] + '" selected>' + sts_nom + '</option>');
				$('#sts_id_nominal').prepend('<option value="' + reponse['sts_id'] + '" selected>' + sts_nom + '</option>');

				return;
			} else {
				return -1;
			}
		}
	});
}
