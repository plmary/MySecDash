function ModifierIndispoPersonnelActivite( act_id ) {
	var Liste_IPI_Ajouter = [];
	var Liste_IPI_Modifier = [];
	var Liste_IPI_Supprimer = [];

	var total_IPI_ajouter = 0;
	var total_IPI_modifier = 0;
	var total_IPI_supprimer = 0;

	var Liste_IPP_Ajouter = [];
	var Liste_IPP_Modifier = [];
	var Liste_IPP_Supprimer = [];

	var total_IPP_ajouter = 0;
	var total_IPP_modifier = 0;
	var total_IPP_supprimer = 0;


	$('#zone-ipi-corps div[id^="IPI_"]').each(function(index, element){
		if ($(element).attr('data-action') == 'M') {
			flag_modifier = 0;

			ipi_id = $(element).attr('id').split('_')[1];
			ein_id = $(element).find('div[id="ein_id-'+ipi_id+'"]').attr('data-ein_id');
			ppr_id = $(element).find('select[id="ppr_id-'+ipi_id+'"]').val();
			ppr_id_old = $(element).find('select[id="ppr_id-'+ipi_id+'"]').attr('data-old');
			ete_id = $(element).find('select[id="ete_id_ein-'+ipi_id+'"]').val();
			ete_id_old = $(element).find('select[id="ete_id_ein-'+ipi_id+'"]').attr('data-old');
			ipi_commentaires = $(element).find('textarea[id="ipi_commentaires-'+ipi_id+'"]').val();
			ipi_commentaires_old = $(element).find('textarea[id="ipi_commentaires-'+ipi_id+'-old"]').val();
			//alert('act_id="'+act_id+'", ein_id="'+ein_id+'", ete_id="'+ete_id+'", ppr_id="'+ppr_id+'", ipi_commentaires="'+ipi_commentaires+'"');

			if (ppr_id != ppr_id_old || ete_id != ete_id_old || ipi_commentaires != ipi_commentaires_old) {
				Liste_IPI_Modifier.push([ipi_id, act_id, ein_id, ete_id, ppr_id, ipi_commentaires]);

				total_IPI_modifier += 1;
			}
		} else if ($(element).attr('data-action') == 'S') {
			ipi_id = $(element).attr('id').split('_')[1];

			Liste_IPI_Supprimer.push(ipi_id);

			total_IPI_supprimer += 1;
		} else if ($(element).attr('data-action') == 'A') {
			ipi_id = $(element).attr('id').split('_')[1];
			ein_id = $(element).find('div[id="ein_id-'+ipi_id+'"]').attr('data-ein_id');
			ppr_id = $(element).find('select[id="ppr_id-'+ipi_id+'"]').val();
			ete_id = $(element).find('select[id="ete_id_ein-'+ipi_id+'"]').val();
			ipi_commentaires = $(element).find('textarea[id="ipi_commentaires-'+ipi_id+'"]').val();

			Liste_IPI_Ajouter.push([act_id, ein_id, ete_id, ppr_id, ipi_commentaires]);

			total_IPI_ajouter += 1;
		}
	});


	$('#zone-ipp-corps div[id^="IPP_"]').each(function(index, element){
		if ($(element).attr('data-action') == 'M') {
			flag_modifier = 0;

			ipp_id = $(element).attr('id').split('_')[1];
			frn_id = $(element).find('div[id="frn_id-'+ipp_id+'"]').attr('data-frn_id');
			ipp_contact = $(element).find('input[id="ipp_contact-'+ipp_id+'"]').val();
			ipp_contact_old = $(element).find('input[id="ipp_contact-'+ipp_id+'"]').attr('data-old');
			ete_id = $(element).find('select[id="ete_id_frn-'+ipp_id+'"]').val();
			ete_id_old = $(element).find('select[id="ete_id_frn-'+ipp_id+'"]').attr('data-old');
			ipp_commentaires = $(element).find('textarea[id="ipp_commentaires-'+ipp_id+'"]').val();
			ipp_commentaires_old = $(element).find('textarea[id="ipp_commentaires-'+ipp_id+'-old"]').val();
			//alert('ipp_id="'+ipp_id+'", frn_id="'+frn_id+'", ipp_contact="'+ipp_contact+'", ete_id="'+ete_id+'", ipp_commentaires="'+ipp_commentaires+'"');

			if (ipp_contact != ipp_contact_old || ete_id != ete_id_old || ipp_commentaires != ipp_commentaires_old) {
				Liste_IPP_Modifier.push([ipp_id, act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]);

				total_IPP_modifier += 1;
			}
		} else if ($(element).attr('data-action') == 'S') {
			ipp_id = $(element).attr('id').split('_')[1];

			Liste_IPP_Supprimer.push(ipp_id);

			total_IPP_supprimer += 1;
		} else if ($(element).attr('data-action') == 'A') {
			ipp_id = $(element).attr('id').split('_')[1];
			frn_id = $(element).find('div[id="frn_id-'+ipp_id+'"]').attr('data-frn_id');
			ipp_contact = $(element).find('input[id="ipp_contact-'+ipp_id+'"]').val();
			ete_id = $(element).find('select[id="ete_id_frn-'+ipp_id+'"]').val();
			ipp_commentaires = $(element).find('textarea[id="ipp_commentaires-'+ipp_id+'"]').val();
			//alert('ipp_id="'+ipp_id+'", frn_id="'+frn_id+'", ipp_contact="'+ipp_contact+'", ete_id="'+ete_id+'", ipp_commentaires="'+ipp_commentaires+'"');

			Liste_IPP_Ajouter.push([act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]);

			total_IPP_ajouter += 1;
		}
	});

//	alert('Ajouter='+(total_IPI_ajouter+total_IPP_ajouter)+', '+'Modifier='+(total_IPI_modifier+total_IPP_modifier)+
//		', '+'Supprimer='+(total_IPI_supprimer+total_IPP_supprimer));

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({
			'liste_ipi_ajouter': Liste_IPI_Ajouter, 'liste_ipp_ajouter': Liste_IPP_Ajouter,
			'liste_ipi_modifier': Liste_IPI_Modifier, 'liste_ipp_modifier': Liste_IPP_Modifier,
			'liste_ipi_supprimer': Liste_IPI_Supprimer, 'liste_ipp_supprimer': Liste_IPP_Supprimer}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				trier();
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});
}
