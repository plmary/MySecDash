function ModifierIndispoPersonnelActivite( act_id ) {
	var Liste_PEI_Ajouter = [];
	var Liste_PEI_Modifier = [];
	var Liste_PEI_Supprimer = [];

	var total_PEI_ajouter = 0;
	var total_PEI_modifier = 0;
	var total_PEI_supprimer = 0;

	var Liste_PAP_Ajouter = [];
	var Liste_PAP_Modifier = [];
	var Liste_PAP_Supprimer = [];

	var total_PAP_ajouter = 0;
	var total_PAP_modifier = 0;
	var total_PAP_supprimer = 0;

	frn_id = $('#frn_nom').attr('data-frn_id');
	act_id = $('#act_nom').attr('data-act_id');


	$('#zone-pei-corps div[id^="PEI_"]').each(function(index, element){
		if ($(element).attr('data-action') == 'M') {
			flag_modifier = 0;

			pei_id = $(element).attr('id').split('_')[1];
			ein_id = $(element).find('select[id="ein_id-'+pei_id+'"]').val();
			ppr_id = $(element).find('select[id="ppr_id-'+pei_id+'"]').val();
			ppr_id_old = $(element).find('select[id="ppr_id-'+pei_id+'"]').attr('data-old');
			ete_id = $(element).find('select[id="ete_id_ein-'+pei_id+'"]').val();
			ete_id_old = $(element).find('select[id="ete_id_ein-'+pei_id+'"]').attr('data-old');
			pei_commentaires = $(element).find('textarea[id="pei_commentaires-'+pei_id+'"]').val();
			pei_commentaires_old = $(element).find('textarea[id="pei_commentaires-'+pei_id+'-old"]').val();
			//alert('act_id="'+act_id+'", ein_id="'+ein_id+'", ete_id="'+ete_id+'", ppr_id="'+ppr_id+'", pei_commentaires="'+pei_commentaires+'"');

			if (ppr_id != ppr_id_old || ete_id != ete_id_old || pei_commentaires != pei_commentaires_old) {
				Liste_PEI_Modifier.push([pei_id, act_id, ein_id, ete_id, ppr_id, pei_commentaires]);

				total_PEI_modifier += 1;
			}
		} else if ($(element).attr('data-action') == 'S') {
			pei_id = $(element).attr('id').split('_')[1];

			Liste_PEI_Supprimer.push(pei_id);

			total_PEI_supprimer += 1;
		} else if ($(element).attr('data-action') == 'A') {
			pei_id = $(element).attr('id').split('_')[1];
			ein_id = $(element).find('select[id="ein_id-'+pei_id+'"]').val();
			ppr_id = $(element).find('select[id="ppr_id-'+pei_id+'"]').val();
			ete_id = $(element).find('select[id="ete_id_ein-'+pei_id+'"]').val();
			pei_commentaires = $(element).find('textarea[id="pei_commentaires-'+pei_id+'"]').val();

			Liste_PEI_Ajouter.push([frn_id, act_id, ein_id, ete_id, ppr_id, pei_commentaires]);

			total_PEI_ajouter += 1;
		}
	});


	$('#zone-pap-corps div[id^="PAP_"]').each(function(index, element){
		if ($(element).attr('data-action') == 'M') {
			flag_modifier = 0;

			pap_id = $(element).attr('id').split('_')[1];
			frn_id_ancien = $(element).find('select[id="frn_id-'+pap_id+'"]').attr('data-frn_id');
			frn_id_nouveau = $(element).find('select[id="frn_id-'+pap_id+'"]').val();
			pap_contact = $(element).find('input[id="pap_contact-'+pap_id+'"]').val();
			pap_contact_old = $(element).find('input[id="pap_contact-'+pap_id+'"]').attr('data-old');
			ete_id = $(element).find('select[id="ete_id_frn-'+pap_id+'"]').val();
			ete_id_old = $(element).find('select[id="ete_id_frn-'+pap_id+'"]').attr('data-old');
			pap_commentaires = $(element).find('textarea[id="pap_commentaires-'+pap_id+'"]').val();
			pap_commentaires_old = $(element).find('textarea[id="pap_commentaires-'+pap_id+'-old"]').val();

			if (frn_id_ancien != frn_id_nouveau || pap_contact != pap_contact_old || ete_id != ete_id_old || pap_commentaires != pap_commentaires_old) {
				Liste_PAP_Modifier.push([pap_id, act_id, frn_id, frn_id_nouveau, pap_contact, ete_id, pap_commentaires]);

				total_PAP_modifier += 1;
			}
		} else if ($(element).attr('data-action') == 'S') {
			pap_id = $(element).attr('id').split('_')[1];

			Liste_PAP_Supprimer.push(pap_id);

			total_PAP_supprimer += 1;
		} else if ($(element).attr('data-action') == 'A') {
			pap_id = $(element).attr('id').split('_')[1];
			frn_id_nouveau = $(element).find('select[id="frn_id-'+pap_id+'"]').val();
			pap_contact = $(element).find('input[id="pap_contact-'+pap_id+'"]').val();
			ete_id = $(element).find('select[id="ete_id_frn-'+pap_id+'"]').val();
			pap_commentaires = $(element).find('textarea[id="pap_commentaires-'+pap_id+'"]').val();
			//alert('pap_id="'+pap_id+'", frn_id="'+frn_id+'", pap_contact="'+pap_contact+'", ete_id="'+ete_id+'", pap_commentaires="'+pap_commentaires+'"');

			Liste_PAP_Ajouter.push([act_id, frn_id, frn_id_nouveau, pap_contact, ete_id, pap_commentaires]);

			total_PAP_ajouter += 1;
		}
	});

/*	alert('Ajouter='+(total_PEI_ajouter+total_PAP_ajouter)+', '+'Modifier='+(total_PEI_modifier+total_PAP_modifier)+
		', '+'Supprimer='+(total_PEI_supprimer+total_PAP_supprimer));
	alert(Liste_PEI_Ajouter);*/

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({
			'liste_PEI_ajouter': Liste_PEI_Ajouter, 'liste_PAP_ajouter': Liste_PAP_Ajouter,
			'liste_PEI_modifier': Liste_PEI_Modifier, 'liste_PAP_modifier': Liste_PAP_Modifier,
			'liste_PEI_supprimer': Liste_PEI_Supprimer, 'liste_PAP_supprimer': Liste_PAP_Supprimer}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				trier( $( 'div#entete_tableau div.row div:first'), false );
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
