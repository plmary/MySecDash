$(function() {
	// Assigne l'événement "click" sur tous les boutons de Modification
	$('.btn-modifier').click( function( event ){
		var id = $(this).attr('data-id');

		ModalMAJCampagne( id );
	});
});



function ModifierCampagne( cmp_id ) {
	var cmp_date = $('#cmp_date').val();
	var cmp_validation = $('#cmp_flag_validation').val();

	var Liste_ENT_Ajouter = [];
	var Liste_ENT_Supprimer = [];
	var total_entites = Number($('#CMP_'+cmp_id+' .btn-entites').text());

	var Liste_APP_Ajouter = [];
	var Liste_APP_Supprimer = [];
	var total_applications = Number($('#CMP_'+cmp_id+' .btn-applications').text());

	var Liste_FRN_Ajouter = [];
	var Liste_FRN_Supprimer = [];
	var total_fournisseurs = Number($('#CMP_'+cmp_id+' .btn-fournisseurs').text());

	var Liste_STS_Ajouter = [];
	var Liste_STS_Supprimer = [];
	var total_sites = Number($('#CMP_'+cmp_id+' .btn-sites').text());


	$('input[id^="entite-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_ENT_Ajouter.push($(element).attr('id').split('-')[1]);
				total_entites += 1;
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_ENT_Supprimer.push($(element).attr('id').split('-')[1]);
				total_entites -= 1;
			}
		}
	});

	$('input[id^="application-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_APP_Ajouter.push($(element).attr('id').split('-')[1]);
				total_applications += 1;
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_APP_Supprimer.push($(element).attr('id').split('-')[1]);
				total_applications -= 1;
			}
		}
	});

	$('input[id^="fournisseur-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_FRN_Ajouter.push($(element).attr('id').split('-')[1]);
				total_fournisseurs += 1;
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_FRN_Supprimer.push($(element).attr('id').split('-')[1]);
				total_fournisseurs -= 1;
			}
		}
	});

	$('input[id^="site-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_STS_Ajouter.push($(element).attr('id').split('-')[1]);
				total_sites += 1;
			}
		} else {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_STS_Supprimer.push($(element).attr('id').split('-')[1]);
				total_sites -= 1;
			}
		}
	});

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'cmp_id': cmp_id, 'cmp_date': cmp_date, 'cmp_validation': cmp_validation,
			'liste_ent_ajouter': Liste_ENT_Ajouter, 'liste_ent_supprimer': Liste_ENT_Supprimer,
			'liste_app_ajouter': Liste_APP_Ajouter, 'liste_app_supprimer': Liste_APP_Supprimer,
			'liste_frn_ajouter': Liste_FRN_Ajouter, 'liste_frn_supprimer': Liste_FRN_Supprimer,
			'liste_sts_ajouter': Liste_STS_Ajouter, 'liste_sts_supprimer': Liste_STS_Supprimer}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalCampagne').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( '#CMP_' + cmp_id + ' div[data-src="cmp_date"] span' ).text( cmp_date );
				if ( cmp_validation == 0 ) {
					cmp_validation = reponse['L_Non'];
				} else {
					cmp_validation = reponse['L_Oui'];
				}
				$( '#CMP_' + cmp_id + ' div[data-src="cmp_flag_validation"] span' ).text( cmp_validation );

				$('#CMP_'+cmp_id+' .btn-entites').text( ajouterZero( total_entites, 2 ) );
				$('#CMP_'+cmp_id+' .btn-applications').text( ajouterZero( total_applications, 2 ) );
				$('#CMP_'+cmp_id+' .btn-fournisseurs').text( ajouterZero( total_fournisseurs, 2 ) );
				$('#CMP_'+cmp_id+' .btn-sites').text( ajouterZero( total_sites, 2 ) );
			} else {
				afficherMessage( texteMsg, statut, '#idModalCampagne', 0, 'n' );
			}
		}
	});
}
