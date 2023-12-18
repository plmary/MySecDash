$(document).ready(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});


    if ( $('.btn-chercher').length > 0 ) {
        $('.btn-chercher').on('click', function() {
            trier( $('div.active'), false, $('#c_rechercher').val() );
        });
    }
});


function trier( myElement, changerTri, chercher ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: '../../../Loxense-EditionsRisques.php?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche, 'chercher': chercher}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				var texteMsg = reponse['texteHTML'];

				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				if ( changer_tri == true ) {
					var Element = sens_recherche.split('-');
					if ( Element[ Element.length - 1 ] == 'desc' ) {
						sens_recherche = Element[ 0 ];
					} else {
						sens_recherche = Element[ 0 ] + '-desc';
					}
				}

				// Postionne la couleur sur la colonne active sur le tri.
				$('div#entete_tableau div.row div.triable').removeClass('active');
				$(myElement).addClass('active');

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


				$('.btn-telecharger-e').click(function(){
					var crs_id = $(this).attr('data-id');

					genererImpressionCarto( crs_id, 'excel' );
				});


				$('.btn-telecharger-w').click(function(){
					var crs_id = $(this).attr('data-id');

					genererImpressionCarto( crs_id, 'word' );
				});


				$('.btn-imprimer').click(function(){
					var crs_id = $(this).attr('data-id');

					ModalChoixImpression( crs_id );
				});

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


// ===============================================
// Fonctions communes pour gérer les impressions.

function genererImpressionCarto( crs_id, type_edition, flag_apr_spp, flag_apr, flag_spp, flag_evr,
 flag_a_risques, flag_risques_evalues, flag_a_risques_max,
 flag_t_risques, flag_t_risques_max, 
 flag_actions, flag_actifs_risques, flag_risques_mesures, flag_actifs_mesures, flag_repartition_risques ) {
	var flag_apr_spp = flag_apr_spp || 'o';
	var flag_apr = flag_apr || 'o';
	var flag_spp = flag_spp || 'o';
	var flag_evr = flag_evr || 'o';
	var flag_a_risques = flag_a_risques || 'o';
	var flag_risques_evalues = flag_risques_evalues || 'o';
	var flag_a_risques_max = flag_a_risques_max || 0;
	var flag_t_risques = flag_t_risques || 'o'
	var flag_t_risques_max = flag_t_risques_max || 0;
	var flag_actions = flag_actions || 'o';
	var flag_actifs_risques = flag_actifs_risques || 'o';
	var flag_risques_mesures = flag_risques_mesures || 'o';
	var flag_actifs_mesures = flag_actifs_mesures || 'o';
	var flag_repartition_risques = flag_repartition_risques || 'o';

	$.ajax({
		url: '../../../Loxense-EditionsRisques.php?Action=AJAX_Generer_Impression',
		type: 'POST',
		data: $.param({'crs_id': crs_id, 'type_edition': type_edition,
			'flag_apr_spp': flag_apr_spp, 'flag_apr': flag_apr, 'flag_spp': flag_spp, 'flag_evr': flag_evr,
			'flag_a_risques': flag_a_risques, 'flag_risques_evalues': flag_risques_evalues, 'flag_a_risques_max': flag_a_risques_max, 
			'flag_t_risques': flag_t_risques, 'flag_t_risques_max': flag_t_risques_max, 
			'flag_actions': flag_actions, 'flag_actifs_risques': flag_actifs_risques,
			'flag_risques_mesures': flag_risques_mesures, 'flag_actifs_mesures': flag_actifs_mesures,
			'flag_repartition_risques': flag_repartition_risques}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			afficherMessage( reponse['texteMsg'], reponse['statut'] );

			if ( type_edition == 'excel' ) chargerImpressionsCartoExcel( crs_id );
			else if ( type_edition == 'word' ) chargerImpressionsCartoWord( crs_id );
			else if ( type_edition == 'html' ) alert('Yop');
		}
	});
}


function chargerImpressionsCartoExcel( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionsRisques.php?Action=AJAX_Verifier_Impression_Excel',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionsRisques.php?Action=AJAX_Charger_Impression_Excel&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function chargerImpressionsCartoWord( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionsRisques.php?Action=AJAX_Verifier_Impression_Word',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionsRisques.php?Action=AJAX_Charger_Impression_Word&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function ModalChoixImpression( Id ) {
	var ent_libelle = $('#CRS_'+Id).find('div[data-src="ent_libelle"]').find('span').text();
	var crs_libelle = $('#CRS_'+Id).find('div[data-src="crs_libelle"]').find('span').text();
	var crs_periode = $('#CRS_'+Id).find('div[data-src="crs_periode"]').find('span').text();
	var crs_version = $('#CRS_'+Id).find('div[data-src="crs_version"]').find('span').text();

	$.ajax({
		url: '../../../Loxense-EditionsRisques.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
//		data: $.param({'Types_Actifs_Primordiaux': apr_type_code, 'Criteres_Valorisation_Actifs': Id,
//			'Associer_APR': Id, 'Type_Actif': apr_type, 'entete': 'non'}),
		success: function( reponse ) {
			var Corps =
				'<div class="well">' +
				 '<div class="row">' +
				  '<div class="col-lg-4"><strong>' + reponse['L_Entite'] + '</strong></div>' +
				  '<div class="col-lg-5"><strong>' + reponse['L_Libelle'] + '</strong></div>' +
				  '<div class="col-lg-1"><strong>' + reponse['L_Periode'] + '</strong></div>' +
				  '<div class="col-lg-2"><strong>' + reponse['L_Version'] + '</strong></div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<div class="col-lg-4" style="background-color: #dcafdd;">' + ent_libelle + '</div>' +
				  '<div class="col-lg-5" style="background-color: #dcafdd;">' + crs_libelle + '</div>' +
				  '<div class="col-lg-1" style="background-color: #dcafdd;">' + crs_periode + '</div>' +
				  '<div class="col-lg-2" style="background-color: #dcafdd;">' + crs_version + '</div>' +
				 '</div>' +
				'</div>' +


				'<div class="well corps_onglet">' +
				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="format_edition">' + reponse[ 'L_Format_Edition' ] + '</label>' +
				  '<div class="col-lg-2">' +
				   '<select id="format_edition" class="form-select" required>' +
				    '<option>excel</option>' +
				    '<option>word</option>' +
//				    '<option disabled>html</option>' +
				   '</select>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label text-left" for="flag_apr_spp">' + reponse[ 'L_Tout_Cocher_Decocher' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="m_flag_cocher_decocher" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="titre">' + reponse['L_Chapitres'] + '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label text-left" for="flag_apr_spp">' + reponse[ 'L_Actifs_Primordiaux_Supports' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_apr_spp" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_apr">' + reponse[ 'L_Actifs_Primordiaux' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_apr" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +


				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_spp">' + reponse[ 'L_Actifs_Supports' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_spp" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_evr">' + reponse[ 'L_Evenements_Redoutes' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_evr" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +


				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_a_risques">' + reponse[ 'L_Appreciation_Risques' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_a_risques" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +

				  '<div class="col-lg-1">' +
				   '<input id="flag_a_risques_max" class="form-control" type="number">' +
				  '</div>' +
				  '<label class="col-lg-2 form-control-static" for="flag_a_risques_max" style="font-weight: normal;">' + reponse[ 'L_Limitation_Affichage_Risques' ] + '</label>' +

				  '<div class="col-lg-3"><div class="form-check">' +
				   '<input id="flag_risques_evalues" class="form-check-input" type="checkbox" checked>' +
				   '<label class="form-check-label" for="flag_risques_evalues">' +
				    reponse[ 'L_Affiche_Uniquement_Risques_Evalues' ] +
				   '</label>' +
				  '</div></div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_t_risques">' + reponse[ 'L_Traitement_Risques' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_t_risques" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +

				  '<div class="col-lg-1">' +
				   '<input id="flag_t_risques_max" class="form-control" type="number">' +
				  '</div>' +
				  '<label class="col-lg-4 form-control-static" for="flag_t_risques_max" style="font-weight: normal;">' + reponse[ 'L_Limitation_Affichage_Risques' ] + '</label>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_actions">' + reponse[ 'L_Actions' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_actions" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_actifs_risques">' + reponse[ 'L_Actifs_Risques' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_actifs_risques" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_actions">' + reponse[ 'L_Risques_Mesures' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_risques_mesures" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_actions">' + reponse[ 'L_Actifs_Mesures' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_actifs_mesures" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label" for="flag_actions">' + reponse[ 'L_Repartition_Risques' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_repartition_risques" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div>' +

				'</div>';

			construireModal( 'idModal',
				reponse[ 'L_Editions_Risques' ],
				Corps,
				'idBoutonPrincipal', reponse[ 'L_Imprimer' ],
				true, reponse[ 'L_Fermer' ],
				'idForm', 'modal-xl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
//				$('#format_edition').on( 'change', function(){
//					alert($('#format_edition').val());
//				});
				$('#m_flag_cocher_decocher').on( 'change', function() {
					if ($('#m_flag_cocher_decocher').is(':checked') === true ) {
						$('input[id^="flag_apr_spp"]').attr('checked', 'checked');
						$('input[id^="flag_"]').attr('checked', 'checked');
					} else {
						$('input[id^="flag_"]').removeAttr('checked');
					}
				});

				$('#idModal select:first').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#idForm').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				imprimerCartographie( Id );
			} );
		}
	});
}


function imprimerCartographie( Id ) {
	var format_edition = $('#format_edition').val();
	var flag_apr_spp = $('#flag_apr_spp').is(':checked');
	var flag_apr = $('#flag_apr').is(':checked');
	var flag_spp = $('#flag_spp').is(':checked');
	var flag_evr = $('#flag_evr').is(':checked');
	var flag_a_risques = $('#flag_a_risques').is(':checked');
	var flag_risques_evalues = $('#flag_risques_evalues').is(':checked');
	var flag_a_risques_max = $('#flag_a_risques_max').val();
	var flag_t_risques = $('#flag_t_risques').is(':checked');
	var flag_t_risques_max = $('#flag_t_risques_max').val();
	var flag_actions = $('#flag_actions').is(':checked');
	var flag_actifs_risques = $('#flag_actifs_risques').is(':checked');
	var flag_risques_mesures = $('#flag_risques_mesures').is(':checked');
	var flag_actifs_mesures = $('#flag_actifs_mesures').is(':checked');
	var flag_repartition_risques = $('#flag_repartition_risques').is(':checked');

	if ( flag_apr_spp === true ) flag_apr_spp = 'o';
	else flag_apr_spp = 'n';

	if ( flag_apr === true ) flag_apr = 'o';
	else flag_apr = 'n';

	if ( flag_spp === true ) flag_spp = 'o';
	else flag_spp = 'n';

	if ( flag_evr === true ) flag_evr = 'o';
	else flag_evr = 'n';

	if ( flag_a_risques === true ) flag_a_risques = 'o';
	else flag_a_risques = 'n';

	if ( flag_risques_evalues === true ) flag_risques_evalues = 'o';
	else flag_risques_evalues = 'n';

	if ( flag_t_risques === true ) flag_t_risques = 'o';
	else flag_t_risques = 'n';

	if ( flag_actions === true ) flag_actions = 'o';
	else flag_actions = 'n';

	if ( flag_actifs_risques === true ) flag_actifs_risques = 'o';
	else flag_actifs_risques = 'n';

	if ( flag_risques_mesures === true ) flag_risques_mesures = 'o';
	else flag_risques_mesures = 'n';

	if ( flag_actifs_mesures === true ) flag_actifs_mesures = 'o';
	else flag_actifs_mesures = 'n';

	if ( flag_repartition_risques === true ) flag_repartition_risques = 'o';
	else flag_repartition_risques = 'n';

	$('#idModal').modal('hide');

	genererImpressionCarto( Id, format_edition, flag_apr_spp, flag_apr, flag_spp, flag_evr,
		flag_a_risques, flag_risques_evalues, flag_a_risques_max,
		flag_t_risques, flag_t_risques_max, 
		flag_actions, flag_actifs_risques, flag_risques_mesures, flag_actifs_mesures, flag_repartition_risques );
}