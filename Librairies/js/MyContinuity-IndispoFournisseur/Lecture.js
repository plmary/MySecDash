var nbr_ajout_occurrence_pap = 0;
var nbr_ajout_occurrence_pei = 0;
var nbr_ajout_occurrence_ipi = 0;

$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), false );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});

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
						var _Numero = 0, _Selected;
						for (let element of reponse['Liste_Campagnes']) {
							_Numero += 1;

							if (_Numero == 1) {
								_Selected = ' selected';
							} else {
								_Selected = '';
							}

							$('#s_cmp_id').append('<option value="' + element.cmp_id + '"' + _Selected + '>' + element.cmp_date + '</option>');
						}
					}

					if ( reponse['ent_id'] == '---' || reponse['ent_id'] == '' ) {
						$('#s_ent_id').append('<option value="">---</option>');
						if (pbSynchronisation == 0) {
							afficherMessageCorps(reponse['L_Campagne_Sans_Entite'], reponse['L_Gestion_Entites']);
						}
					} else {
						// Mise à jour de la liste déroulante des Campagnes associées à la Société
						var _Numero = 0, _Selected;
						for (let element of reponse['Liste_Entites']) {
							_Numero += 1;

							if (_Numero == 1) {
								_Selected = ' selected';
							} else {
								_Selected = '';
							}

							$('#s_ent_id').append('<option value="' + element.ent_id + '"' + _Selected + '>' + element.ent_nom + ' (' + element.total_activites + ' ' + (element.total_activites > 1 ? reponse['L_Activites'] : reponse['L_Activite']) + ')</option>');
						}
					}

					if ( reponse['sct_id'] != '---' && reponse['sct_id'] != ''
					 && reponse['cmp_id'] != '---' && reponse['cmp_id'] != ''
					 && reponse['ent_id'] != '---' && reponse['ent_id'] != '' ) {
						trier( $( 'div#entete_tableau div.row div:first'), false );
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
							$('#s_ent_id').append('<option value="' + element.ent_id + '">' + element.ent_nom + ' (' + element.total_activites + ' ' + (element.total_activites > 1 ? reponse['L_Activites'] : reponse['L_Activite']) + ')</option>');
						}
					}
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);

					trier( $( 'div#entete_tableau div.row div:first'), false );
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
		var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var ent_id = $('#s_ent_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Entite',
			type: 'POST',
			data: $.param({'trier': sens_recherche, 'ent_id': ent_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];

				if (statut == 'success') {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);

					trier( $( 'div#entete_tableau div.row div:first'), false );
				} else {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);
				}
			}
		});
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				if ( reponse['s_cmp_id'] == '---' || reponse['s_cmp_id'] == '' ) {
					$('#s_cmp_id').append('<option value="">---</option>');
					afficherMessageCorps(reponse['L_Societe_Sans_Campagne'], reponse['L_Gestion_Campagnes']);
				} else {
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
	
	
					if ( reponse[ 'droit_modifier' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Modification
						$('.btn-modifier').click( function( event ){
							var Id = $(this).attr('data-id');

							ModalAjouterModifier( Id );
						});
					}
				}

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}



function ModalAjouterModifier( frn_id = '' ) {
	$.ajax({
	url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
	type: 'POST',
	data: $.param({'frn_id': frn_id}), // les paramètres sont protégés avant envoi
	dataType: 'json',
	success: function( reponse ) {
		var Titre = reponse['L_Titre_Modifier'];
		var Bouton = reponse[ 'L_Modifier' ];

		var frn_nom = reponse['Fournisseurs'][0].frn_nom;

		if ( reponse['Fournisseurs'][0].pei_composite != null ) {
			var tableau_equipes = reponse['Fournisseurs'][0].pei_composite;
		} else {
			var tableau_equipes = '';
		}

		if ( reponse['Fournisseurs'][0].pap_composite != null ) {
			var tableau_prestataires = reponse['Fournisseurs'][0].pap_composite;
		} else {
			var tableau_prestataires = '';
		}

		var Corps =
			// =====================================
			// *************************************
			// Zone entête de la fenêtre de d'Ajout/Modification
			'<div class="row g-3">' +
			 '<label class="col-form-label col-lg-1" for="frn_nom">' + reponse[ 'L_Fournisseur' ] + '</label>' +
			 '<div class="col-lg-7">' +
			  '<input id="frn_nom" class="form-control fg_vert" type="text" data-frn_id="' + frn_id + '" value="'+ reponse['Fournisseurs'][0].frn_nom + '" disabled>' +
			 '</div>' +
			 '<label class="col-form-label col-lg-1 text-end" for="frn_nom">' + reponse[ 'L_DMIA' ] + '</label>' +
			 '<div class="col-lg-2">' +
			  '<input id="ete_id_frn" class="form-control fg_vert" type="text" value="'+ reponse['Fournisseurs'][0].ete_nom_code + '" disabled>' +
			 '</div>' +
			'</div> <!-- .row -->' +
			'<div class="row g-3 mb-4">' +
			 '<label class="col-form-label col-lg-1" for="frn_nom">' + reponse[ 'L_Activite' ] + '</label>' +
			 '<div class="col-lg-11">' +
			  '<input id="act_nom" class="form-control fg_vert" type="text" data-act_id="' + reponse['Fournisseurs'][0].act_id + '" value="'+ reponse['Fournisseurs'][0].act_nom + '" disabled>' +
			 '</div>' +
			'</div> <!-- .row -->';

			// =====================================
			// *************************************
			// Zone des onglets de la fenêtre de d'Ajout/Modification
			Corps +='<ul class="nav nav-tabs">' +
				'<li><a id="afficher_pei" class="nav-link" href="#">' + reponse[ 'L_Indispo_Fournisseur_Equipe'] + '</a></li>' +
				'<li><a id="afficher_pap" class="nav-link" href="#">' + reponse[ 'L_Indispo_Fournisseur_Autre_Fournisseur'] + '</a></li>' +
				'</ul>';

			Corps += '<div id="ZoneCreerEquipe" class="input-group mb-3 d-none">' +
				 '<input type="text" class="form-control" placeholder="' + reponse['L_Equipe'] + '" id="ein_nom_n">' +
				 '<textarea class="form-control" placeholder="' + reponse['L_Description'] + '" id="ein_description_n"></textarea>' +
				 '<button type="button" class="btn btn-outline-secondary" id="btn-creer-equipe">' + reponse['L_Creer'] + '</button>' +
				 '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-equipe">' + reponse['L_Fermer'] + '</button>' +
				'</div> <!-- #ZoneCreerEquipe -->' +

				 '<div id="ZoneCreerResponsableEquipe" class="input-group mb-3 d-none">' +
				  '<input type="text" class="form-control text-uppercase" placeholder="' + reponse['L_Nom'] + '" id="ppr_nom_responsable">' +
				  '<input type="text" class="form-control text-capitalize" placeholder="' + reponse['L_Prenom'] + '" id="ppr_prenom_responsable">' +
				  '<span class="input-group-text"><div class="form-check form-check-reverse">' +
				  '<input class="form-check-input" type="checkbox" value="" id="ppr_interne_responsable" checked>' +
				  '<label class="form-check-label" for="ppr_interne_cle">' +reponse['L_Interne'] + '</label>' +
				  '</div></span>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-creer-responsable">' + reponse['L_Creer'] + '</button>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-responsable">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerResponsableEquipe -->' +

				 '<div id="ZoneCreerPrestataire" class="input-group mb-3 d-none">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Nom'] + '" id="frn_nom_n">' +
				  '<select type="text" class="form-select" placeholder="' + reponse['L_Hebergement'] + '" id="tfr_id_n">' +
				  ' <option value="">' + reponse['L_Aucun'] + '</option>';
			for (let Type_Fournisseur of reponse['Liste_Types_Fournisseur']) {
				Corps += '<option value="' + Type_Fournisseur.tfr_id + '">' + Type_Fournisseur.tfr_nom_code + '</option>';
			}
			Corps += '</select>' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Description'] + '" id="frn_description_n">' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-creer-prestataire">' + reponse['L_Creer'] + '</button>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-prestataire">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerPrestataire -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-pei">' +
				// ------------------------------------
				// Barre d'ajout d'une nouvelle Equipe Interne avec le nom de son Responsable pour remplacer le Fournisseur sur une Activité.
				'<div id="pei-barre">' + // class="input-group mb-3">' +
				 '<div class="row mb-0">' +
				  '<div class="col-lg-3">' +
				   '<label class="col-form-label" for="ein_id_n">' + reponse['L_Equipe'] + '</label>' +
				  '</div>' +
				  '<div class="col-lg-3">' +
				   '<label class="col-form-label" for="ppr_id_n">' + reponse['L_Responsable'] + '</label>' +
				  '</div>' +
				  '<div class="col-lg-2">' +
				   '<label class="col-form-label" for="ete_id_ein">' + reponse['L_DMIA'] + '</label>' +
				  '</div>' +
				  '<div class="col-lg-3">' +
				   '<label class="col-form-label" for="pei_commentaires">' + reponse['L_Description'] + '</label>' +
				  '</div>' +
				 '</div> <!-- .row -->' +
				 '<div class="row">' +
				  '<div class="col-lg-3">' +
				  '<div class="input-group">' +
				   '<select id="ein_id_n" class="form-select">' +
				    '<option value="">' + reponse['L_Aucune'] + '</option>';
			for (let Equipe of reponse['Liste_Equipes']) {
				Corps += '<option value="' + Equipe.ein_id + '">' + Equipe.ein_nom + '</option>';
			}
			Corps += '</select>';

			if ( reponse['Droit_Ajouter_Personnes_Cles'] == true ) {
				Corps += '<button class="btn btn-outline-secondary" id="btn-section-ajouter-equipe" type="button" title="'+reponse['L_Creer']+'"><i class="bi-plus"></i></button>';
			}

			Corps += '</div>' +
				'</div>' +
				'<div class="col-lg-3">' +
				'<div class="input-group">' +
				'<select id="ppr_id_n" class="form-select">' +
				'<option value="">' + reponse['L_Aucun'] + '</option>';
			for (let Partie_Prenante of reponse['Liste_Parties_Prenantes']) {
				if (Partie_Prenante.ppr_interne == true) {
					Flag_Interne = ' (' + reponse['L_Interne'] + ')';
				} else {
					Flag_Interne = '';
				}

				Corps += '<option value="' + Partie_Prenante.ppr_id + '">' + Partie_Prenante.ppr_nom + ' ' + Partie_Prenante.ppr_prenom + Flag_Interne + '</option>';
			}
			Corps += '</select>';
			
			if ( reponse['Droit_Ajouter_Personnes_Cles'] == true ) {
				Corps += '<button class="btn btn-outline-secondary" id="btn-section-ajouter-responsable" type="button" title="'+reponse['L_Creer']+'"><i class="bi-plus"></i></button>';
			}

			Corps += '</div>' +
				'</div>' +
				'<div class="col-lg-2">' +
				'<select id="ete_id_ein" class="form-select">' +
				'<option value="">' + reponse['L_Aucune'] + '</option>';
			for (let Echelle of reponse['Liste_EchellesTemps']) {
				Corps += '<option value="' + Echelle.ete_id + '">' + Echelle.ete_nom_code + '</option>';
			}
			Corps += '</select>' +
				'</div>';

			Corps += '<div class="col-lg-3">' +
				'<textarea id="pei_commentaires_n" class="form-control"></textarea>' +
				'</div>';

			Corps += '<div class="col-lg-1">' +
				'<button class="btn btn-outline-secondary" id="btn-ajouter-pei" type="button">'+reponse['L_Ajouter']+'</button>' +
				'</div>' +
				'</div> <!-- .row -->' +
				'</div> <!-- #pei-barre -->';


			Corps += '<div id="zone-pei-corps">' +
				'<div class="row titre">' +
				 '<div class="col-3">' + reponse['L_Equipe'] + '</div>' +
				 '<div class="col-3">' + reponse['L_Responsable'] + '</div>' +
				 '<div class="col-2">' + reponse['L_DMIA'] + '</div>' +
				 '<div class="col-3">' + reponse['L_Description'] + '</div>' +
				 '<div class="col-1">' + reponse['L_Action'] + '</div>' +
				'</div> <!-- .row -->';

			if ( reponse['Fournisseurs'][0].pei_composite != '' && reponse['Fournisseurs'][0].pei_composite != null ) {
				for (let Equipe of reponse['Fournisseurs'][0].pei_composite.split('===')) {
					Infos_Equipe = Equipe.split('---');
					pei_id = Infos_Equipe[0];
					ete_id = Infos_Equipe[1];
					ete_nom = Infos_Equipe[2];
					ein_id = Infos_Equipe[3];
					ein_nom = Infos_Equipe[4];
					ppr_id = Infos_Equipe[5];
					ppr_prenom = Infos_Equipe[6];
					ppr_nom = Infos_Equipe[7];
					ppr_interne = Infos_Equipe[8];
					pei_commentaires = Infos_Equipe[9];
	
					Corps += '<div class="row liste p-2" id="PEI_' + pei_id + '">' +
						'<div class="col-3">' +
						'<select id="ein_id-' + pei_id + '" data-ein_id="' + ein_id + '" class="form-select">';
					for (let Equipe of reponse['Liste_Equipes']) {
						if (Equipe.ein_id == ein_id) {
							Defaut = ' selected';
						} else {
							Defaut = '';
						}

						Corps += '<option value="' + Equipe.ein_id + '"' + Defaut  + '>' + Equipe.ein_nom + '</option>';
					}
					Corps += '</select>' +
						'</div>' +
						'<div class="col-3">' + 
						'<select id="ppr_id-' + pei_id + '"  data-old="' + ppr_id + '" class="form-select">' +
							'<option value="">' + reponse['L_Aucun'] + '</option>';
						for (let Partie_Prenante of reponse['Liste_Parties_Prenantes']) {
							if (Partie_Prenante.ppr_id == ppr_id) {
								Defaut = ' selected';
							} else {
								Defaut = '';
							}
	
							if (Partie_Prenante.ppr_interne == true) {
								Flag_Interne = ' (' + reponse['L_Interne'] + ')';
							} else {
								Flag_Interne = '';
							}
	
							Corps += '<option value="' + Partie_Prenante.ppr_id + '"' + Defaut  + '>' + Partie_Prenante.ppr_nom + ' ' + Partie_Prenante.ppr_prenom + Flag_Interne + '</option>';
						}
						Corps += '</select>' +
						'</div>' +
						'<div class="col-2">' +
							'<select id="ete_id_ein-' + pei_id + '" data-old="' + ete_id + '" class="form-select">';
							//'<option value="">' + reponse['L_Aucun'] + '</option>';
						for (let Echelle of reponse['Liste_EchellesTemps']) {
							if (Echelle.ete_id == ete_id) {
								Defaut = ' selected';
							} else {
								Defaut = '';
							}
	
							Corps += '<option value="' + Echelle.ete_id + '"' + Defaut  + '>' + Echelle.ete_nom_code + '</option>';
						}
						Corps += '</select>' +
						'</div>' +
						'<div class="col-3">' +
						'<textarea id="pei_commentaires-' + pei_id + '-old" class="form-control d-none disabled">' + pei_commentaires + '</textarea>' +
						'<textarea id="pei_commentaires-' + pei_id + '" class="form-control">' + pei_commentaires + '</textarea>' +
						'</div>' +
						'<div class="col-1">' +
						 '<button class="btn btn-outline-secondary btn-sm btn-supprimer" data-id="' + pei_id + '" title="' + reponse['L_Supprimer'] + '" type="button">' +
						  '<i class="bi-x-circle"></i>' +
						 '</button>' +
						'</div> <!-- .col-2 -->' +
						'</div> <!-- .row -->';
				}
			}
			Corps += '</div> <!-- #zone-pei-corps -->' +
				'</div> <!-- #zone-pei -->';


			// ====================================================================================

			// *************************************
			Corps += '<div id="zone-pap" class="d-none">' +
				// ------------------------------------
				// Barre d'ajout d'une nouvelle Equipe Interne avec le nom de son Responsable pour remplacer le personnel de l'Activité.
				'<div class="input-group mb-3" id="pap-barre">' +
				 // Selection de l'équipe de remplacement
				 '<label class="input-group-text" for="frn_id_n">' + reponse['L_Fournisseur'] + '</label>' +
				 '<select id="frn_id_n" class="form-select">' +
				  '<option value="">' + reponse['L_Aucun'] + '</option>';
			for (let Fournisseur of reponse['Liste_Fournisseurs']) {
				Corps += '<option value="' + Fournisseur.frn_id + '">' + Fournisseur.frn_nom + '</option>';
			}
			Corps += '  </select>';

			if ( reponse['Droit_Ajouter_Fournisseurs'] == true ) {
				Corps += '<button class="btn btn-outline-secondary" id="btn-section-ajouter-prestataire" type="button" title="'+reponse['L_Creer']+'"><i class="bi-plus"></i></button>';
			}

			Corps += '<label class="input-group-text" for="pap_contact_n">' + reponse['L_Contact'] + '</label>' +
				'<input id="pap_contact_n" class="form-control" type="text">';

			Corps += '<label class="input-group-text" for="ete_id_frn">' + reponse['L_DMIA'] + '</label>' +
				'<select id="ete_id_frn" class="form-select">' +
					'<option value="">' + reponse['L_Aucune'] + '</option>';
				for (let Echelle of reponse['Liste_EchellesTemps']) {
					Corps += '<option value="' + Echelle.ete_id + '">' + Echelle.ete_nom_code + '</option>';
				}
			Corps += '  </select>';

			Corps += '<label class="input-group-text" for="pap_commentaires_n">' + reponse['L_Description'] + '</label>' +
				'<textarea id="pap_commentaires_n" class="form-control"></textarea>';

			Corps += '<button class="btn btn-outline-secondary" id="btn-ajouter-pap" type="button">'+reponse['L_Ajouter']+'</button>' +
				 '</div> <!-- #pap-barre -->';


			Corps += '<div id="zone-pap-corps">' +
				'<div class="row titre">' +
				 '<div class="col-3">' + reponse['L_Fournisseur'] + '</div>' +
				 '<div class="col-3">' + reponse['L_Contact'] + '</div>' +
				 '<div class="col-2">' + reponse['L_DMIA'] + '</div>' +
				 '<div class="col-3">' + reponse['L_Description'] + '</div>' +
				 '<div class="col-1">' + reponse['L_Action'] + '</div>' +
				'</div> <!-- .row -->';

			if ( reponse['Fournisseurs'][0].pap_composite != '' && reponse['Fournisseurs'][0].pap_composite != null ) {
				for (let Fournisseur of reponse['Fournisseurs'][0].pap_composite.split('===')) {
					Infos_Fournisseur = Fournisseur.split('---');
					pap_id = Infos_Fournisseur[0];
					ete_id = Infos_Fournisseur[1];
					ete_nom = Infos_Fournisseur[2];
					frn_id = Infos_Fournisseur[3];
					frn_nom_nouveau = Infos_Fournisseur[4];
					pap_contact = Infos_Fournisseur[5];
					pap_commentaires = Infos_Fournisseur[6];
	
					Corps += '<div class="row liste p-2" id="PAP_' + pap_id + '" data-action="M">' +
						'<div class="col-3">' +
						 '<select class="form-select" id="frn_id-' + pap_id + '" data-frn_id="' + frn_id + '">';
					for (let Fournisseur of reponse['Liste_Fournisseurs']) {
						if (Fournisseur.frn_id == frn_id) {
							Defaut = ' selected';
						} else {
							Defaut = '';
						}
						Corps += '<option value="' + Fournisseur.frn_id + '"' + Defaut + '>' + Fournisseur.frn_nom + '</option>';
					}
					Corps += '</select>' +
						'</div>' +
						'<div class="col-3"><input id="pap_contact-' + pap_id + '" class="form-control" type="text" data-old="' + pap_contact + '" value="' + pap_contact + '"></div>' +
						'<div class="col-2">' +
						'<select id="ete_id_frn-' + pap_id + '" class="form-select" data-old="' + ete_id + '">';
					for (let Echelle of reponse['Liste_EchellesTemps']) {
						if (Echelle.ete_id == ete_id) {
							Defaut = ' selected';
						} else {
							Defaut = '';
						}
						Corps += '<option value="' + Echelle.ete_id + '"' + Defaut + '>' + Echelle.ete_nom_code + '</option>';
					}
					Corps += '</select>' +
						'</div>' +
						'<div class="col-3">' +
						'<textarea id="pap_commentaires-' + pap_id + '-old" class="form-control d-none disabled">' + pap_commentaires + '</textarea>' +
						'<textarea id="pap_commentaires-' + pap_id + '" class="form-control">' + pap_commentaires + '</textarea>' +
						'</div>' +
						'<div class="col-1">' +
						 '<button class="btn btn-outline-secondary btn-sm btn-supprimer" data-id="' + pap_id + '" title="' + reponse['L_Supprimer'] + '" type="button">' +
						  '<i class="bi-x-circle"></i>' +
						 '</button>' +
						'</div> <!-- .col-2 -->' +
						'</div> <!-- .row -->';
				}
			}
			Corps += '</div> <!-- #zone-pap-corps -->' +
				'</div> <!-- zone-pap -->';

			// =========================================

			construireModal( 'idModal',
				Titre,
				Corps,
				'idBoutonModifier', Bouton,
				false, reponse[ 'L_Fermer' ],
				'formAjouterModifier', 'modal-xxl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				//alert(document.getElementById('formAjouterModifier').offsetHeight+', '+window.innerHeight);
				// Met en place les écoutes utiles sur l'onglet PEI
				$('#pei-barre select[id="ein_id_n"]').on('change', function( event ){
					ein_id = $(this).val();
					if ( ein_id != '' ) {
						ein_id = 'PEI_'+ein_id;
					}

					rechercherObjetsDansOngletParId('#zone-pei-corps', 'div[id^="PEI_"]', ein_id);

					return -1;
				});

				$('#zone-pei-corps select[id^="ppr_id-"]').on('change', function( event ){
					$(this).parent().parent().attr('data-action','M');

					return -1;
				});

				$('#zone-pei-corps select[id^="ete_id_ein-"]').on('change', function( event ){
					$(this).parent().parent().attr('data-action','M');

					return -1;
				});

				$('#zone-pei-corps textarea[id^="pei_commentaires-"]').on('keyup', function( event ){
					$(this).parent().parent().attr('data-action','M');

					return -1;
				});

				$('#pei-barre button[id="btn-ajouter-pei"]').on('click', function( event ){
					nbr_ajout_occurrence_pei += 1;
					ein_id = $('#ein_id_n').val();
					ein_nom = $('#ein_id_n option:selected').text();
					ppr_id = $('#ppr_id_n').val();
					ete_id = $('#ete_id_n').val();
					pei_commentaires = $('#pei_commentaires_n').val();

					if (ein_id == '') {
						return;
					}

					var texteHTML = '<div class="row liste p-2" id="PEI_N' + nbr_ajout_occurrence_pei + '" data-action="A">' +
						//'<div class="col-3" id="ein_id-N' + nbr_ajout_occurrence_ipi + '" data-ein_id="' + ein_id + '">' + ein_nom + '</div>' +
						'<div class="col-3">' +
						'<select id="ein_id-N' + nbr_ajout_occurrence_pei + '" class="form-select">' +
						'<option value="">' + reponse['L_Aucun'] + '</option>';

						for (let Equipe of reponse['Liste_Equipes']) {
							if (Equipe.ein_id == ein_id) {
								Defaut = ' selected';
							} else {
								Defaut = '';
							}

							texteHTML += '<option value="' + Equipe.ein_id + '"' + Defaut  + '>' + Equipe.ein_nom + '</option>';
						}

					texteHTML += '</select>' +
						'</div>' +
						'<div class="col-3">' + 
						'<select id="ppr_id-N' + nbr_ajout_occurrence_pei + '" class="form-select">' +
							'<option value="">' + reponse['L_Aucun'] + '</option>';

					for (let Partie_Prenante of reponse['Liste_Parties_Prenantes']) {
						if (Partie_Prenante.ppr_id == ppr_id) {
							Defaut = ' selected';
						} else {
							Defaut = '';
						}

						if (Partie_Prenante.ppr_interne == true) {
							Flag_Interne = ' (' + reponse['L_Interne'] + ')';
						} else {
							Flag_Interne = '';
						}

						texteHTML += '<option value="' + Partie_Prenante.ppr_id + '"' + Defaut  + '>' + Partie_Prenante.ppr_nom + ' ' + Partie_Prenante.ppr_prenom + Flag_Interne + '</option>';
					}

					texteHTML += '</select>' +
						'</div>' +
						'<div class="col-2">' +
							'<select id="ete_id_ein-N' + nbr_ajout_occurrence_pei + '" class="form-select">';
							//'<option value="">' + reponse['L_Aucun'] + '</option>';
					for (let Echelle of reponse['Liste_EchellesTemps']) {
						if (Echelle.ete_id == ete_id) {
							Defaut = ' selected';
						} else {
							Defaut = '';
						}

						texteHTML += '<option value="' + Echelle.ete_id + '"' + Defaut  + '>' + Echelle.ete_nom_code + '</option>';
					}
					texteHTML += '</select>' +
					'</div>' +
					'<div class="col-3">' +
					'<textarea id="pei_commentaires-N' + nbr_ajout_occurrence_pei + '" class="form-control">' + pei_commentaires + '</textarea>' +
					'</div>' +
					'<div class="col-1">' +
					 '<button class="btn btn-outline-secondary btn-sm btn-supprimer-N" data-id="PEI_N' + nbr_ajout_occurrence_pei + '" title="' + reponse['L_Supprimer'] + '" type="button">' +
					  '<i class="bi-x-circle"></i>' +
					 '</button>' +
					'</div> <!-- .col-2 -->' +
						'</div> <!-- .row -->';

					$('#zone-pei-corps').append( texteHTML );

					// Nettoyage des champs
					$('#ein_id_n').val('');
					$('#ppr_id_n').val('');
					$('#ete_id_ein').val('');
					$('#pei_commentaires_n').val('');

					rechercherObjetsDansOngletParId('#zone-pei-corps', 'div[id^="PEI_"]', '');

					$('#zone-pei-corps button.btn-supprimer-N').off('click').on('click', function( event ) {
						var idOcc = $(this).attr('data-id');
						$('div#' + idOcc).remove();
					});

					return -1;
				});

				$('#zone-pei-corps button.btn-supprimer').on('click', function( event ){
					pei_id = $(this).attr('data-id');

					if ( $(this).parent().parent().attr('data-action') == 'S' ) {
						$(this).parent().parent().attr('data-action','M');
						$('#zone-pei-corps select[id="ein_id-' + pei_id + '"]').removeAttr('disabled').toggleClass('text-secondary').toggleClass('border-warning');
						$('#zone-pei-corps select[id="ppr_id-' + pei_id + '"]').removeAttr('disabled').toggleClass('text-secondary').toggleClass('border-warning');
						$('#zone-pei-corps select[id="ete_id_ein-' + pei_id + '"]').removeAttr('disabled').toggleClass('text-secondary').toggleClass('border-warning');
						$('#zone-pei-corps textarea[id="pei_commentaires-' + pei_id + '"]').removeAttr('disabled').toggleClass('text-secondary').toggleClass('border-warning');
					} else {
						$(this).parent().parent().attr('data-action','S');
						$('#zone-pei-corps select[id="ein_id-' + pei_id + '"]').attr('disabled', 'disabled').toggleClass('text-secondary').toggleClass('border-warning');
						$('#zone-pei-corps select[id="ppr_id-' + pei_id + '"]').attr('disabled', 'disabled').toggleClass('text-secondary').toggleClass('border-warning');
						$('#zone-pei-corps select[id="ete_id_ein-' + pei_id + '"]').attr('disabled', 'disabled').toggleClass('text-secondary').toggleClass('border-warning');
						$('#zone-pei-corps textarea[id="pei_commentaires-' + pei_id + '"]').attr('disabled', 'disabled').toggleClass('text-secondary').toggleClass('border-warning');
					}

					return -1;
				});


				$('button#btn-section-ajouter-equipe').on('click', function( event ){
					$('#pei-barre').addClass('d-none');
					$('#ZoneCreerEquipe').removeClass('d-none');

					$('#ein_nom_n').focus();
				});

				$('button#btn-fermer-zone-equipe').on('click', function( event ){
					$('#pei-barre').removeClass('d-none');
					$('#ZoneCreerEquipe').addClass('d-none');

					$('#ein_nom_n').val('');
					$('#ein_description_n').val('');

					$('#ZoneCreerEquipe').off('blur');
				});

				$('button#btn-creer-equipe').on('click', function( event ){
					if ( ajouterEquipeInterneListe( reponse['L_ERR_Champ_Obligatoire'] ) != -1 ) {
						$('button#btn-fermer-zone-equipe').trigger('click');
					}
				});


				$('button#btn-section-ajouter-responsable').on('click', function( event ){
					$('#pei-barre').addClass('d-none');
					$('#ZoneCreerResponsableEquipe').removeClass('d-none');

					$('#ppr_nom_responsable').focus();
				});

				$('button#btn-fermer-zone-responsable').on('click', function( event ){
					$('#pei-barre').removeClass('d-none');
					$('#ZoneCreerResponsableEquipe').addClass('d-none');

					$('#ppr_nom_reponsable').val('');
					$('#ppr_prenom_reponsable').val('');
					$('#ppr_interne_reponsable').attr('checked', 'checked');
				});

				$('button#btn-creer-responsable').on('click', function( event ){
					ajouterResponsableEquipe( reponse['L_ERR_Champs_Obligatoires'], reponse['L_Interne'] );

					$('button#btn-fermer-zone-responsable').trigger('click');
				});


				// =================================================
				// *************************************************
				// =================================================

				// Met en place les écoutes utiles sur l'onglet PAP
				$('#zone-pap-corps input[id^="pap_contact-"]').on('change', function( event ){
					$(this).parent().parent().attr('data-action','M');

					return -1;
				});

				$('#zone-pap-corps select[id^="ete_id_frn-"]').on('change', function( event ){
					$(this).parent().parent().attr('data-action','M');

					return -1;
				});

				$('#zone-pap-corps textarea[id^="pap_commentaires-"]').on('keyup', function( event ){
					$(this).parent().parent().attr('data-action','M');

					return -1;
				});

				$('#pap-barre button[id="btn-ajouter-pap"]').on('click', function( event ){
					nbr_ajout_occurrence_pap += 1;
					frn_id = $('#frn_id_n').val();
					frn_nom = $('#frn_id_n option:selected').text();
					pap_contact = $('#pap_contact_n').val();
					ete_id = $('#ete_id_frn').val();
					pap_commentaires = $('#pap_commentaires_n').val();

					if (frn_id == '') {
						return;
					}

					var texteHTML = '<div class="row liste p-2" id="PAP_N' + nbr_ajout_occurrence_pap + '" data-action="A">' +
						'<div class="col-3">' +
						'<select id="frn_id-N' + nbr_ajout_occurrence_pap + '" class="form-select">';
						for (let Fournisseur of reponse['Liste_Fournisseurs']) {
							if (Fournisseur.frn_id == frn_id) {
								Defaut = ' selected';
							} else {
								Defaut = '';
							}
							texteHTML += '<option value="' + Fournisseur.frn_id + '"' + Defaut + '>' + Fournisseur.frn_nom + '</option>';
						}

					texteHTML += '</select>' +
						'</div>' +
						'<div class="col-3">' +
						'<input id="pap_contact-N' + nbr_ajout_occurrence_pap + '" class="form-control" type="text" value="' + pap_contact + '">' +
						'</div>' +
						'<div class="col-2">' +
						'<select id="ete_id_frn-N' + nbr_ajout_occurrence_pap + '" class="form-select">';

					for (let Echelle of reponse['Liste_EchellesTemps']) {
						if (Echelle.ete_id == ete_id) {
							Defaut = ' selected';
						} else {
							Defaut = '';
						}
						texteHTML += '<option value="' + Echelle.ete_id + '"' + Defaut + '>' + Echelle.ete_nom_code + '</option>';
					}

					texteHTML += '</select>' +
						'</div>' +
						'<div class="col-3">' +
						'<textarea id="pap_commentaires-N' + nbr_ajout_occurrence_pap + '" class="form-control">' + pap_commentaires + '</textarea>' +
						'</div>' +
						'<div class="col-1">' +
						 '<button class="btn btn-outline-secondary btn-sm btn-supprimer-N" data-id="PAP_N' + nbr_ajout_occurrence_pap + '" title="' + reponse['L_Supprimer'] + '" type="button">' +
						  '<i class="bi-x-circle"></i>' +
						 '</button>' +
						'</div> <!-- .col-2 -->' +
						'</div> <!-- .row -->';

					$('#zone-pap-corps').append( texteHTML );

					// Nettoyage des champs
					$('#frn_id_n').val('');
					$('#pap_contact_n').val('');
					$('#ete_id_frn').val('');
					$('#pap_commentaires_n').val('');

					rechercherObjetsDansOngletParId('#zone-pap-corps', 'div[id^="PAP_"]', '');

					$('#zone-pap-corps button.btn-supprimer-N').off('click').on('click', function( event ) {
						var idOcc = $(this).attr('data-id');
						$('div#' + idOcc).remove();
					});

					return -1;
				});

				$('#zone-pap-corps button.btn-supprimer').on('click', function( event ){
					pap_id = $(this).attr('data-id');

					if ( $(this).parent().parent().attr('data-action') == 'S' ) {
						$(this).parent().parent().attr('data-action','M');
						$('#zone-pap-corps select[id="frn_id-' + pap_id + '"]').removeAttr('disabled');
						$('#zone-pap-corps input[id="pap_contact-' + pap_id + '"]').removeAttr('disabled');
						$('#zone-pap-corps select[id="ete_id_frn-' + pap_id + '"]').removeAttr('disabled');
						$('#zone-pap-corps textarea[id="pap_commentaires-' + pap_id + '"]').removeAttr('disabled');
					} else {
						$(this).parent().parent().attr('data-action','S');
						$('#zone-pap-corps select[id="frn_id-' + pap_id + '"]').attr('disabled', 'disabled');
						$('#zone-pap-corps input[id="pap_contact-' + pap_id + '"]').attr('disabled', 'disabled');
						$('#zone-pap-corps select[id="ete_id_frn-' + pap_id + '"]').attr('disabled', 'disabled');
						$('#zone-pap-corps textarea[id="pap_commentaires-' + pap_id + '"]').attr('disabled', 'disabled');
					}

					return -1;
				});


				$('button#btn-section-ajouter-prestataire').on('click', function( event ){
					$('#pap-barre').addClass('d-none');
					$('#ZoneCreerPrestataire').removeClass('d-none');

					$('#frn_nom_n').focus();
				});

				$('button#btn-fermer-zone-prestataire').on('click', function( event ){
					$('#pap-barre').removeClass('d-none');
					$('#ZoneCreerPrestataire').addClass('d-none');
				});

				$('button#btn-creer-prestataire').on('click', function( event ){
					ajouterPrestataire( reponse['L_ERR_Champs_Obligatoires'], reponse['L_Interne'] );

					$('#frn_nom_n').val('');
					$('#tfr_id_n').val('');
					$('#frn_description_n').val('');

					$('#pap-barre').removeClass('d-none');
					$('#ZoneCreerPrestataire').addClass('d-none');
				});


				function rechercherObjetsDansOngletParId(Id_Zone, Champ_Spec = '', Valeur_Recherche = '') {
					//alert(Id_Zone+', '+Champ_Spec+', '+Valeur_Recherche);
					var Total = 0;

					$(Id_Zone+' '+Champ_Spec).each( function( index, valeur ) {
						//alert(index);
						if ( Valeur_Recherche == $(valeur).attr('id') || Valeur_Recherche == '' ) {
							$( this ).show();
							Total += 1;
						} else {
							$( this ).hide();
						}

						if (Total > 0 && Valeur_Recherche != '') {
							$('#btn-ajouter-pei').attr('disabled', 'disabled');
						} else {
							$('#btn-ajouter-pei').removeAttr('disabled');
						}
					});
				}


				function rechercherObjetsDansOngletParValeur(Id_Zone, Champ_Spec = '', Valeur_Recherche = '') {
					//alert(Id_Zone+', '+Champ_Spec+', '+Valeur_Recherche);
					$(Id_Zone+' .liste '+Champ_Spec).each( function( index, valeur ){
						Valeur = $( this ).text();

						if (Valeur_Recherche == '') {
							$( this ).show();
						} else {
							if (Valeur.search(Valeur_Recherche) >= 0) {
								$( this ).show();
							} else {
								$( this ).hide();
							}
						}
					});
				}


				$('#btn-section-ajouter-responsable').on('click', function(){
					$('#section-selectionner-responsable').addClass('d-none');
					$('#section-ajouter-responsable').removeClass('d-none');

					$('#ppr_nom_resp').focus();

					return -1;
				});

				$('#btn-fermer-zone-responsable').on('click', function(){
					$('#section-selectionner-responsable').removeClass('d-none');
					$('#section-ajouter-responsable').addClass('d-none');

					$('#ppr_id_responsable').focus();

					return -1;
				});

				$('#btn-ajouter-responsable').on('click', function(){
					ppr_nom = $('#ppr_nom_resp').val();
					ppr_prenom = $('#ppr_prenom_resp').val();
					ppr_interne = $('#ppr_interne_resp').is(':checked');

					if (ppr_nom == '') {
						$('#ppr_nom_resp').focus();
						return -1;
					}
					if (ppr_prenom == '') {
						$('#ppr_prenom_resp').focus();
						return -1;
					}

					ppr_nom = ppr_nom.toUpperCase();
					ppr_prenom = ppr_prenom[0].toUpperCase()+ppr_prenom.substring(1);

					ajouterPartiePrenante( ppr_nom, ppr_prenom, ppr_interne, 'ppr_id_responsable' );

					$('#section-selectionner-responsable').removeClass('d-none');
					$('#section-ajouter-responsable').addClass('d-none');
					
					return -1;
				});


				$('#btn-section-ajouter-suppleant').on('click', function(){
					$('#section-selectionner-suppleant').addClass('d-none');
					$('#section-ajouter-suppleant').removeClass('d-none');

					$('#ppr_nom_supp').focus();

					return -1;
				});

				$('#btn-fermer-zone-suppleant').on('click', function(){
					$('#section-selectionner-suppleant').removeClass('d-none');
					$('#section-ajouter-suppleant').addClass('d-none');

					$('#ppr_id_suppleant').focus();

					return -1;
				});

				$('#btn-ajouter-suppleant').on('click', function(){
					ppr_nom = $('#ppr_nom_supp').val();
					ppr_prenom = $('#ppr_prenom_supp').val();
					ppr_interne = $('#ppr_interne_supp').is(':checked');

					if (ppr_nom == '') {
						$('#ppr_nom_supp').focus();
						return -1;
					}
					if (ppr_prenom == '') {
						$('#ppr_prenom_supp').focus();
						return -1;
					}

					ppr_nom = ppr_nom.toUpperCase();
					ppr_prenom = ppr_prenom[0].toUpperCase()+ppr_prenom.substring(1);

					ajouterPartiePrenante( ppr_nom, ppr_prenom, ppr_interne, 'ppr_id_suppleant' );

					$('#section-selectionner-suppleant').removeClass('d-none');
					$('#section-ajouter-suppleant').addClass('d-none');
					
					return -1;
				});


				// Ce bouton permet de valider les informations liées à cette activité.
				// Il alimente le champ avec la date du jour où l'activité a été validée.
				$('#btn-validation').on('click', function(){
					DateDuJour = new Date();
					Annee = DateDuJour.getFullYear();
					Mois = (DateDuJour.getMonth()+1);
					if (String(Mois).length == 1) Mois = '0'+Mois;
					Jour = DateDuJour.getDate();
					if (String(Jour).length == 1) Jour = '0'+Jour;
					DateDuJour = Annee+'-'+Mois+'-'+Jour;
					alert(DateDuJour);
					
					return -1;
				});


				// ===============================
				// -------------------------------
				// Affiche le contenu des onglets.

				// =========
				// Gestion de l'onglet "Reprise du Fournisseur par une équipe"
				$('#afficher_pei').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_pei').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-pei').removeClass('d-none');
					$('#zone-pei-corps').removeClass('d-none');
				});


				// =========
				// Gestion de l'onglet "Reprise de l'Activité par un Prestataire"
				$('#afficher_pap').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_pap').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-pap').removeClass('d-none');
					$('#zone-pap-corps').removeClass('d-none');
				});


				// =========
				// Gestion de l'onglet "Fournisseurs"
				$('#afficher_fournisseurs').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_fournisseurs').addClass('active');

					$('div[id^="zone-"]').addClass('d-none');
					$('#zone-fournisseurs').removeClass('d-none');

					if ( reponse['Droit_Ajouter_Fournisseurs'] == true ) {
						$('#zone-btn-creer-objet').removeClass('d-none');
					} else {
						$('#zone-btn-creer-objet').addClass('d-none');
					}

					$('#zone-action').removeClass('d-none');
					$('div[id^="ZoneCreer"]').addClass('d-none');
					$('div[id^="ZoneRecherche"]').removeClass('d-none');

					$('#chp-rechercher-objet').val('');
					rechercherObjetsDansOnglet('div#zone-fournisseurs ');

					$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
						rechercherObjetsDansOnglet('div#zone-fournisseurs ');
					});

					$('#btn-fermer-zone-fournisseur').off('click').on('click', function(){
						$('#ZoneCreerFournisseur').addClass('d-none');
						$('#ZoneRecherche').removeClass('d-none');

						$('#frn_nom').val('');
						$('#tfr_id').val('');
						$('#frn_description').val('');
					});

					$('#btn-creer-objet').off('click').on('click', function(){
						$('#ZoneCreerFournisseur').removeClass('d-none');
						$('#ZoneRecherche').addClass('d-none');

						$('div#ZoneCreerFournisseur input:first').focus();

						return false;
					});

					$('#btn-creer-fournisseur').off('click').on('click', function(){
						creerFournisseur();
					});

					$('[id^="choix_fournisseur-"').on('change', function(){
						var _Id = $(this).attr('id').split('-')[1];

						if ($('#choix_fournisseur-'+_Id).is(':checked')) {
							$('#ete_id-fournisseur-'+_Id).attr('required', 'required');
						} else {
							$('#ete_id-fournisseur-'+_Id).removeAttr('required');
						}
					});

					$('div#zone-fournisseurs input:first').focus();
				});

				$('.nav-tabs a:first').trigger('click');
			});


			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});


			$('#idBoutonModifier').on('click', function( event ) {
				//event.preventDefault(); // Laisse le contrôle au Javascript.
				//alert('contrôle de surface possible');
/*				if ($('#ppr_id_responsable').val() == '') {
					$('#afficher_cartouche').trigger('click');
					$('#ppr_id_responsable').focus().css('border-width', '2px');
					//afficherMessage(reponse['L_ERR_Champ_Obligatoire'], 'error');
					return -1;
				}

				if ($('#sts_id_nominal').val() == '') {
					$('#afficher_cartouche').trigger('click');
					$('#sts_id_nominal').focus().css('border-width', '2px');
					//afficherMessage(reponse['L_ERR_Champ_Obligatoire'], 'error');
					return -1
				}*/
			});


			$('#formAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				ModifierIndispoPersonnelActivite( frn_id );
			} );
		}
	});
}



function afficherMessageCorps(Libelle_Message, Libelle_Bouton) {
	$('#corps_tableau').html(
		'<h2 class="text-center">' + Libelle_Message + '</h2>' +
		'<p class="text-center"><button class="btn btn-primary">' + Libelle_Bouton + '</button></p>'
	);
}



function creerOccurrenceApplicationDansListe(ID_Application, Nom_Application, Flag_Selection_Origine,
	Selection_Actuelle, Liste_EchellesTemps, Libelle_DMIA, ID_DIMA, Libelle_PDMA, ID_PDMA,
	Libelle_Palliatif, Donnees, Palliatif, Libelle_Aucun, ID_DIMA_DSI, ID_PDMA_DSI, Libelle_DMIA_SI, Libelle_PDMA_SI) {
	if (Selection_Actuelle != '') {
		var Champ_Obligatoire = 'required';
	} else {
		var Champ_Obligatoire = '';
	}


	Corps = '<div class="row liste">' +
		 '<div class="col-3 text-end">' +
		  '<div class="form-check text-start">' + 
		   '<input class="form-check-input" type="checkbox" value="" data-old_value="'+Flag_Selection_Origine+'" id="choix_application-'+ID_Application+'"'+Selection_Actuelle+'>' +
		   '<label class="form-check-label" for="choix_application-'+ID_Application+'">'+Nom_Application+'</label>' +
		  '</div> <!-- .form-check -->' +
		 '</div> <!-- .col-3 -->' +
	
		 '<div class="col-2">' +
		  '<select id="ete_id_dima-'+ID_Application+'" class="form-select" data-old="'+ID_DIMA+'"'+Champ_Obligatoire+'>' +
		    '<option value="">'+Libelle_Aucun+'</option>';

	for (let EchelleTemps of Liste_EchellesTemps) {
		if (EchelleTemps.ete_id == ID_DIMA) {
			Selectionne = ' selected';
		} else {
			Selectionne = '';
		}
		
		Corps += '<option value="'+EchelleTemps.ete_id+'"'+Selectionne+'>'+EchelleTemps.ete_nom_code+'</option>';
	}

	Corps += '</select>';

	ete_libelle = ' ';

	if (ID_DIMA_DSI != '' && ID_DIMA_DSI != null) {
		for (let EchelleTemps of Liste_EchellesTemps) {
			if (EchelleTemps.ete_id == ID_DIMA_DSI) {
				ete_libelle = EchelleTemps.ete_nom_code;
			}
		}
	}

	Corps += '<div class="form-floating mt-2">' +
		'  <input type="text" class="form-control" id="ID_DIMA_DSI" placeholder="DIMA" value="' + ete_libelle + '" disabled>' +
		'  <label for="floatingInput">' + Libelle_DMIA_SI + '</label>' +
		'</div>';

	Corps += '</div> <!-- .col-2 -->' +
		 '<div class="col-2">' +
		  '<select id="ete_id_pdma-'+ID_Application+'" class="form-select" data-old="'+ID_PDMA+'"'+Champ_Obligatoire+'>' +
		  '<option value="">'+Libelle_Aucun+'</option>';

	for (let EchelleTemps of Liste_EchellesTemps) {
		if (EchelleTemps.ete_id == ID_PDMA) {
			Selectionne = ' selected';
		} else {
			Selectionne = '';
		}
	
		Corps += '<option value="'+EchelleTemps.ete_id+'"'+Selectionne+'>'+EchelleTemps.ete_nom_code+'</option>';
	}
	
	Corps += '</select>';

	ete_libelle = ' ';

	if (ID_PDMA_DSI != '' && ID_PDMA_DSI != null) {
		for (let EchelleTemps of Liste_EchellesTemps) {
			if (EchelleTemps.ete_id == ID_PDMA_DSI) {
				ete_libelle = EchelleTemps.ete_nom_code;
			}
		}
	}

	Corps += '<div class="form-floating mt-2">' +
		'  <input type="text" class="form-control" id="ID_PDMA_DSI" placeholder="PDMA" value="' + ete_libelle + '" disabled>' +
		'  <label for="floatingInput">' + Libelle_PDMA_SI + '</label>' +
		'</div>' +
		 '</div> <!-- .col-2 -->' +
		 '<div class="col-3">' +
		  '<textarea class="form-control" type="checkbox" id="acap_donnees-'+ID_Application+'" rows="3">' +
		   Donnees+'</textarea>' +
		 '</div> <!-- .col-3 -->' +
		 '<div class="col-2">' +
		  '<textarea class="form-control" type="checkbox" id="acap_palliatif-'+ID_Application+'" rows="3">' +
		   Palliatif+'</textarea>' +
		 '</div> <!-- .col-2 -->' +
		'</div> <!-- .row -->';

	return Corps;
}



function creerOccurrenceFournisseurDansListe(ID_Fournisseur, Nom_Fournisseur, Flag_Selection_Origine,
	Selection_Actuelle, Liste_EchellesTemps, Libelle_DMIA, ID_DIMA,
	Libelle_Consequence_Indisponibilite, acfr_consequence_indisponibilite,
	Libelle_Palliatif, acfr_palliatif_tiers,
	Libelle_Aucun) {
	if (Selection_Actuelle != '') {
		var Champ_Obligatoire = 'required';
	} else {
		var Champ_Obligatoire = '';
	}

	Corps = '<div class="row liste">' +
		 '<div class="col-4">' +
		  '<div class="form-check">' + 
		   '<input class="form-check-input" type="checkbox" value="" data-old_value="'+Flag_Selection_Origine+'" id="choix_fournisseur-'+ID_Fournisseur+'"'+Selection_Actuelle+'>' +
		   '<label class="form-check-label" for="choix_fournisseur-'+ID_Fournisseur+'">'+Nom_Fournisseur+'</label>' +
		  '</div> <!-- .form-check -->' +
		 '</div> <!-- .col-4 -->' +
	
		 '<div class="col-2">' +
		  '<select id="ete_id-fournisseur-'+ID_Fournisseur+'" class="form-select" data-old="'+ID_DIMA+'"'+Champ_Obligatoire+' title="'+Libelle_DMIA+'">' +
		    '<option value="">'+Libelle_Aucun+'</option>';

	for (let EchelleTemps of Liste_EchellesTemps) {
		if (EchelleTemps.ete_id == ID_DIMA) {
			Selectionne = ' selected';
		} else {
			Selectionne = '';
		}
		
		Corps += '<option value="'+EchelleTemps.ete_id+'"'+Selectionne+'>'+EchelleTemps.ete_nom_code+'</option>';
	}
	
	Corps += '</select>' +
		 '</div> <!-- .col-2 -->';


	Corps += '<div class="col-3">' +
		  '<textarea class="form-control" id="acfr_consequence_indisponibilite-'+ID_Fournisseur+'" rows="3">' +
		   acfr_consequence_indisponibilite+'</textarea>' +
		 '</div> <!-- .col-3 -->' +
		 
		 '<div class="col-3">' +
		  '<textarea class="form-control" id="acfr_palliatif_tiers-'+ID_Fournisseur+'" rows="3">' +
		   acfr_palliatif_tiers+'</textarea>' +
		 '</div> <!-- .col-3 -->' +
		'</div> <!-- .row -->';

	return Corps;
}



function creerOccurrenceSiteDansListe( sts_id, Nom_Complet, Old_Value, Checked, L_Aucun, L_Site_Nominal, L_Site_Secours, Select_Nominal = '',
	Select_Secours = '' ) {
	Corps = '<div class="row liste mt-1">' +
		'<div class="col-6">' +
		'<div class="form-check">' +
		'<input type="checkbox" class="form-check-input" id="sts-' + sts_id + '" data-old_value="' + Old_Value + '" ' + Checked + '>' +
		'<label class="form-check-label" for="sts-' + sts_id + '">' + Nom_Complet + '</label>' +
		'</div> <!-- .form-check -->' +
		'</div> <!-- .col-6 -->' +
		'<div class="col-2">' +
		'<select class="form-select" id="acst_type_site-' + sts_id + '">' +
		'<option value="">' + L_Aucun + '</option>' +
		'<option value="0"' + Select_Nominal + '>' + L_Site_Nominal + '</option>' +
		'<option value="1"' + Select_Secours + '>' + L_Site_Secours + '</option>' +
		'</select>' +
		'</div> <!-- .col-2 -->' +
		'</div> <!-- .row -->';

	return Corps;
}
