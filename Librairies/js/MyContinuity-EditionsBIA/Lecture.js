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
					trier( $('div.active'), false );
				} else {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);
				}
			}
		});
	});
});


function trier( myElement, changerTri, chercher ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
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

				$('.btn-imprimer').click(function(){
					var cmp_id = $(this).attr('data-id');

					ModalChoixImpression( cmp_id );
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

function ModalChoixImpression( Id ) {
	var sct_nom = $('#s_sct_id option:selected').text();
	if (sct_nom == '') {
		sct_nom = $('#s_sct_id').val();
	}
	var cmp_date = $('#CMP_'+Id+' div[data-src="cmp_date"] span').text();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		data: $.param({'cmp_id': Id}),
		success: function( reponse ) {
			var Corps =
				'<div class="well">' +
				 '<div class="row">' +
				  '<div class="col-lg-4"><strong>' + reponse['L_Societe'] + '</strong></div>' +
				  '<div class="col-lg-5"><strong>' + reponse['L_Campagne'] + '</strong></div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<div class="col-lg-4 bg_couleur_2">' + sct_nom + '</div>' +
				  '<div class="col-lg-5 bg_couleur_2">' + cmp_date + '</div>' +
				 '</div> <!-- .row -->' +
				'</div> <!-- .well -->' +

				'<div class="well corps_onglet" style="max-height: 450px;">' +
					 '<div class="row">' +
					  '<label class="col-lg-4 col-form-label" for="editer_entite">' + reponse[ 'L_Entite' ] + '</label>' +
					  '<div class="col-lg-5">' +
					   '<select id="editer_entite" class="form-select" required>' +
					    '<option value="*">' + reponse['L_Toutes'] + '</option>';

				for (let Entite of reponse['Liste_Entites']) {
					var ent_nom = Entite.ent_nom;
					if (Entite.ent_description != null && Entite.ent_description != '') ent_nom += ' - ' + Entite.ent_description;
					
					Corps += '<option value="'+Entite.ent_id+'">'+ent_nom+'</option>';
				}

				Corps += '</select>' +
					  '</div>' +
					 '</div> <!-- .row -->' +
				 '<div class="row">' +
				  '<label class="col-lg-4 col-form-label" for="format_edition">' + reponse[ 'L_Format_Edition' ] + '</label>' +
				  '<div class="col-lg-2">' +
				   '<select id="format_edition" class="form-select" required>' +
				    '<option value="docx">Word</option>' +
				    '<option value="odt">OpenOffice</option>' +
				    '<option value="html">HTML</option>' +
				    '<option value="xlsx">Excel</option>' +
				   '</select>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="titre">' + reponse['L_Chapitres'] + '</div>' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label fst-italic text-left" for="m_flag_cocher_decocher">' + reponse[ 'L_Tout_Cocher_Decocher' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="m_flag_cocher_decocher" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label text-left" for="flag_synthese_manager">' + reponse[ 'L_Synthese_Manageriale_Globale' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_synthese_manager" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_pln_eff">' + reponse[ 'L_Planning' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_pln_eff" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_act">' + reponse[ 'L_Liste_Activites' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_act" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_pcl">' + reponse[ 'L_Liste_Personnes_Cles' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_pcl" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_int">' + reponse[ 'L_Liste_Interdependances' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_int" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_app">' + reponse[ 'L_Liste_Applications' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_app" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_frn">' + reponse[ 'L_Liste_Fournisseurs' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_frn" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				 '<div class="row">' +
				  '<label class="col-lg-4 form-check-label" for="flag_liste_ppr">' + reponse[ 'L_Liste_Personnes_Prioritaires' ] + '</label>' +
				  '<div class="col-lg-1">' +
				   '<input id="flag_liste_ppr" class="form-check-input" type="checkbox" checked>' +
				  '</div>' +
				 '</div> <!-- .row -->' +

				'</div> <!-- .well .corps_onglet -->';

			construireModal( 'idModal',
				reponse[ 'L_Edition_BIA' ],
				Corps,
				'idBoutonPrincipal', reponse[ 'L_Editer' ],
				true, reponse[ 'L_Fermer' ],
				'idForm', 'modal-xl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
//				$('#format_edition').on( 'change', function(){
//					alert($('#format_edition').val());
//				});
				$('#m_flag_cocher_decocher').on( 'click', function() {
					if ($('#m_flag_cocher_decocher').is(':checked') === true ) {
						$('input[id^="flag_"]').attr('checked', 'checked');
					} else {
						$('input[id^="flag_"]').removeAttr('checked');
					}
				});
				
				$('#editer_entite').on('change', function() {
					if ( $('#editer_entite').val() != '*' ) {
//						$('label[for="m_flag_cocher_decocher"]').addClass('disabled');
						$('label[for="flag_synthese_manager"]').addClass('disabled');
//						$('label[for="flag_liste_act"]').addClass('disabled');
//						$('label[for="flag_liste_app"]').addClass('disabled');
//						$('label[for="flag_liste_pcl"]').addClass('disabled');
//						$('label[for="flag_liste_ppr"]').addClass('disabled');
//						$('label[for="flag_liste_frn"]').addClass('disabled');

//						$('#m_flag_cocher_decocher').removeAttr('checked');
						$('#flag_synthese_manager').removeAttr('checked');
//						$('#flag_liste_act').removeAttr('checked');
//						$('#flag_liste_app').removeAttr('checked');
//						$('#flag_liste_ppr').removeAttr('checked');
//						$('#flag_liste_pcl').removeAttr('checked');
//						$('#flag_liste_frn').removeAttr('checked');
						$('#flag_liste_dtl_act').attr('checked', 'checked');

//						$('#m_flag_cocher_decocher').attr('disabled', 'disabled');
						$('#flag_synthese_manager').attr('disabled', 'disabled');
//						$('#flag_liste_act').attr('disabled', 'disabled');
//						$('#flag_liste_app').attr('disabled', 'disabled');
//						$('#flag_liste_ppr').attr('disabled', 'disabled');
//						$('#flag_liste_pcl').attr('disabled', 'disabled');
//						$('#flag_liste_frn').attr('disabled', 'disabled');
					} else {
						$('label[for="m_flag_cocher_decocher"]').removeClass('disabled');
						$('label[for="flag_synthese_manager"]').removeClass('disabled');
						$('label[for="flag_liste_act"]').removeClass('disabled');
						$('label[for="flag_liste_app"]').removeClass('disabled');
						$('label[for="flag_liste_ppr"]').removeClass('disabled');
						$('label[for="flag_liste_pcl"]').removeClass('disabled');
						$('label[for="flag_liste_frn"]').removeClass('disabled');
	
						$('#m_flag_cocher_decocher').attr('checked', 'checked');
						$('#flag_synthese_manager').attr('checked', 'checked');
						$('#flag_liste_act').attr('checked', 'checked');
						$('#flag_liste_app').attr('checked', 'checked');
						$('#flag_liste_ppr').attr('checked', 'checked');
						$('#flag_liste_pcl').attr('checked', 'checked');
						$('#flag_liste_frn').attr('checked', 'checked');
						$('#flag_liste_dtl_act').removeAttr('checked');
	
						$('#m_flag_cocher_decocher').removeAttr('disabled');
						$('#flag_synthese_manager').removeAttr('disabled');
						$('#flag_liste_act').removeAttr('disabled');
						$('#flag_liste_app').removeAttr('disabled');
						$('#flag_liste_ppr').removeAttr('disabled');
						$('#flag_liste_pcl').removeAttr('disabled');
						$('#flag_liste_frn').removeAttr('disabled');
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

				imprimerBIA( Id );
			} );
		}
	});
}


function imprimerBIA( Id ) {
	var entite_a_editer = $('#editer_entite').val();
	var nom_entite_a_editer = $('#editer_entite option:selected').text();
	var format_edition = $('#format_edition').val();
	var flag_synthese_manager = $('#flag_synthese_manager').is(':checked');
	var flag_liste_pln_eff = $('#flag_liste_pln_eff').is(':checked');
	var flag_liste_act = $('#flag_liste_act').is(':checked');
	var flag_liste_app = $('#flag_liste_app').is(':checked');
	var flag_liste_ppr = $('#flag_liste_ppr').is(':checked');
	var flag_liste_pcl = $('#flag_liste_pcl').is(':checked');
	var flag_liste_int = $('#flag_liste_int').is(':checked');
	var flag_liste_frn = $('#flag_liste_frn').is(':checked');
	var cmp_date = $('div#CMP_'+Id+' div[data-src="cmp_date"] span').text();

	var sct_nom = $('#s_sct_id option:selected').text();
	if (sct_nom == '') {
		sct_nom = $('#s_sct_id').val();
	}

	$('#idModal').modal('hide');

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Editer_Campagne',
		type: 'POST',
		dataType: 'json',
		data: $.param({'cmp_id': Id, 'format_edition': format_edition,
			'flag_synthese_manager': flag_synthese_manager,
			'flag_liste_pln_eff': flag_liste_pln_eff,
			'flag_liste_act': flag_liste_act,
			'flag_liste_app': flag_liste_app,
			'flag_liste_ppr': flag_liste_ppr,
			'flag_liste_pcl': flag_liste_pcl,
			'flag_liste_frn': flag_liste_frn,
			'flag_liste_int': flag_liste_int,
			'flag_liste_dtl_act':'false',
			'sct_nom': sct_nom, 'cmp_date': cmp_date,
			'entite_a_editer': entite_a_editer,
			'nom_entite_a_editer': nom_entite_a_editer
			}),
		success: function( reponse ) {
			statut = reponse['statut'];
			texteMsg = reponse['texteMsg'];

			afficherMessage(texteMsg, statut);
			
			window.location.href = Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Charger_Impression&Nom_Fichier='+reponse['Nom_Fichier']+'&Nom_Fichier_Complet='+reponse['Nom_Fichier_Complet'];
		}
	});
}