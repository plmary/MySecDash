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

							$('#s_ent_id').append('<option value="' + element.ent_id + '"' + _Selected + '>' + element.ent_nom + (element.ent_description != '' ? ' - ' + element.ent_description : '') + ' (' + element.total_activites + ' ' + (element.total_activites > 1 ? reponse['L_Activites'] : reponse['L_Activite']) + ')</option>');
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
							//alert(element.ent_id+' == '+reponse['ent_id']);
							if (element.ent_id == reponse['ent_id']) {
								__Selected = ' selected';
							} else {
								__Selected = '';
							}

							$('#s_ent_id').append('<option value="' + element.ent_id + '"' + __Selected + '>' + element.ent_nom + (element.ent_description != '' ? ' - ' + element.ent_description : '') + ' (' + element.total_activites + ' ' + (element.total_activites > 1 ? reponse['L_Activites'] : reponse['L_Activite']) + ')</option>');
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
	
	
					if ( reponse[ 'droit_ajouter' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Modification
						$('.btn-dupliquer').click( function( event ){
							var Id = $(this).attr('data-id');

							ModalDupliquer( Id );
						});
					}
	
					if ( reponse[ 'droit_modifier' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Modification
						$('.btn-modifier').click( function( event ){
							var Id = $(this).attr('data-id');

							ModalAjouterModifier( Id );
						});
					}

					if ( reponse[ 'droit_supprimer' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Suactession
						$('.btn-supprimer').click(function(){
							var Id = $(this).attr('data-id');
							var Libelle = $('#ACT_'+Id).find('div[data-src="act_nom"]').find('span').text();
	
							ModalSupprimer( Id, Libelle );
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


function ModalDupliquer( act_id ) {
	var act_nom = $('#ACT_'+act_id+' div[data-src="act_nom"] span').text();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'act_id': act_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var Corps =
				'<div class="row g-3 mb-3">' +
				 '<label class="col-form-label col-lg-2" for="act_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				 '<div class="col-lg-10">' +
				  '<input id="act_nom" class="form-control" type="text" value="'+ act_nom + '" maxlength="100" disabled>' +
				 '</div>' +
				'</div>' +
				'<div class="row g-3 mb-3">' +
				 '<label class="col-form-label col-lg-2" for="n_act_nom">' + reponse[ 'L_Nouveau_Nom' ] + '</label>' +
				 '<div class="col-lg-10">' +
				  '<input id="n_act_nom" class="form-control" type="text" value="" maxlength="100" required>' +
				 '</div>' +
				'</div>' +
				'<div class="row g-3 mt-3">' +
				 '<div class="col-lg-12 fs-5">' +
				 reponse['L_Informations_Complementaires_A_Dupliquer'] +
				 '</div>' +
				'</div>' +
				'<div class="row g-3 mb-3">' +
				 '<div class="col-lg-3">' +
				  '<div class="input-group">' +
				   '<div class="input-group-text">' +
				    '<input id="tout_flag" class="form-check-input" type="checkbox">' +
				   '</div>' +
				   '<label class="input-group-text" for="tout_flag">' + reponse[ 'L_Tout_Cocher_Decocher' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				'</div>' +
				'<div class="row g-3 mb-3">' +
				 '<div class="col-lg-2">' +
				  '<div class="form-check">' +
				   '<input id="flag_dmia" class="form-check-input" type="checkbox" checked>' +
				   '<label class="form-check-label" for="flag_dmia">' + reponse[ 'L_DMIA' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				 '<div class="col-lg-2">' +
				  '<div class="form-check">' +
				   '<input id="flag_sites" class="form-check-input" type="checkbox" >' +
				   '<label class="form-check-label" for="flag_sites">' + reponse[ 'L_Sites' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				 '<div class="col-lg-2">' +
				  '<div class="form-check">' +
				   '<input id="flag_fournisseurs" class="form-check-input" type="checkbox" >' +
				   '<label class="form-check-label" for="flag_fournisseurs">' + reponse[ 'L_Fournisseurs' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				 '<div class="col-lg-2">' +
				  '<div class="form-check">' +
				   '<input id="flag_applications" class="form-check-input" type="checkbox" >' +
				   '<label class="form-check-label" for="flag_applications">' + reponse[ 'L_Applications' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				 '<div class="col-lg-2">' +
				  '<div class="form-check">' +
				   '<input id="flag_personnes_cles" class="form-check-input" type="checkbox">' +
				   '<label class="form-check-label" for="flag_personnes_cles">' + reponse[ 'L_Personnes_Cles' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				 '<div class="col-lg-2">' +
				  '<div class="form-check">' +
				   '<input id="flag_personnes_prioritaires" class="form-check-input" type="checkbox">' +
				   '<label class="form-check-label" for="flag_personnes_prioritaires">' + reponse[ 'L_Personnes_Prioritaires' ] + '</label>' +
				  '</div>' +
				 '</div>' +
				'</div>';


			construireModal( 'idModal',
				reponse['L_Dupliquer_Activite'],
				Corps,
				'idBoutonAjouter', reponse['L_Dupliquer'],
				true, reponse[ 'L_Fermer' ],
				'formDupliquer', 'modal-xxl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#n_act_nom').focus();
				
				$('#tout_flag').off('click').on('click', function() {
					if ( $('#tout_flag').is(':checked') ) {
						$('input[id^="flag_"]').attr('checked', true);
					} else {
						$('input[id^="flag_"]').attr('checked', false);
					}
				});
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});


			$('#formDupliquer').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				DupliquerActivite( act_id );
			} );
		}
	});
}

function ModalAjouterModifier( act_id = '' ) {
	$.ajax({
	url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
	type: 'POST',
	data: $.param({'act_id': act_id}), // les paramètres sont protégés avant envoi
	dataType: 'json',
	success: function( reponse ) {
		if ( act_id == '' ) {
			var Titre = reponse['L_Titre_Ajouter'];
			var Bouton = reponse[ 'L_Ajouter' ];

			var act_nom = '';
			var act_description = '';
			var act_teletravail = '';
			var act_dmia = '';
			var act_dmia_poids = 0;
			var act_dmia_libelle = '';
			var act_dmia_couleur = '';
			var act_niveau_impact = '';
			var act_niveau_impact_libelle = '';
			var act_niveau_impact_couleur = '';
			var act_justification_dmia = '';

			var ppr_id_responsable = '';
			var ppr_id_suppleant = '';
			var act_dependances_internes_amont = '';
			var act_dependances_internes_aval = '';
			
			var act_effectifs_en_nominal = '';
			var act_effectifs_a_distance = '';
			
			var cmen_effectif_total = '';
			var act_taux_occupation = '';

			var act_description_entraides = '';
			var act_strategie_montee_en_charge = '';
		} else {
			var Titre = reponse['L_Titre_Modifier'];
			var Bouton = reponse[ 'L_Modifier' ];

			var act_nom = reponse['Activite'][0].act_nom;

			if ( reponse['Activite'][0].act_description != null ) {
				var act_description = reponse['Activite'][0].act_description;
			} else {
				var act_description = '';
			}

			var act_teletravail = reponse['Activite'][0].act_teletravail;
			var act_dmia = 100;
			var act_dmia_libelle = '';
			var act_niveau_impact_poids = 0;
			var act_niveau_impact_couleur = '';
			var act_niveau_impact = 0;
			var act_niveau_impact_libelle = '';
			var act_justification_dmia = reponse['Activite'][0].act_justification_dmia;

			if (act_justification_dmia == null) {
				act_justification_dmia = '';
			}

			for (let Temp of reponse['Liste_DMIA']) {
				if (Temp.nim_poids > act_niveau_impact_poids) {
					act_niveau_impact_poids = Temp.nim_poids;
					act_niveau_impact = Temp.nim_numero;
					act_niveau_impact_libelle = Temp.nim_nom_code;
					act_niveau_impact_couleur = ' style="background-color: #' + Temp.nim_couleur + '"';
				}

				if (Temp.nim_poids >= 3) {
					if (act_dmia >= Temp.ete_poids) {
						act_dmia = Temp.ete_poids;
						act_dmia_libelle = Temp.ete_nom_code;
					}
				}
			}

			if (act_dmia == 0) {
				act_dmia = '';
			}

			var ppr_id_responsable = reponse['Activite'][0].ppr_id_responsable;
			var ppr_id_suppleant = reponse['Activite'][0].ppr_id_suppleant;

			if (reponse['Activite'][0].act_dependances_internes_amont == null) {
				var act_dependances_internes_amont = '';
			} else {
				var act_dependances_internes_amont = reponse['Activite'][0].act_dependances_internes_amont;
			}

			if (reponse['Activite'][0].act_dependances_internes_aval == null) {
				var act_dependances_internes_aval = '';
			} else {
				var act_dependances_internes_aval = reponse['Activite'][0].act_dependances_internes_aval;
			}

			if (reponse['Activite'][0].act_effectifs_en_nominal == null) {
				var act_effectifs_en_nominal = '';
			} else {
				var act_effectifs_en_nominal = reponse['Activite'][0].act_effectifs_en_nominal;
			}

			if (reponse['Activite'][0].act_effectifs_a_distance == null) {
				var act_effectifs_a_distance = '';
			} else {
				var act_effectifs_a_distance = reponse['Activite'][0].act_effectifs_a_distance;
			}

			if (reponse['Activite'][0].cmen_effectif_total == null) {
				var cmen_effectif_total = '';
			} else {
				var cmen_effectif_total = reponse['Activite'][0].cmen_effectif_total;
			}

			if (reponse['Activite'][0].act_taux_occupation == null) {
				var act_taux_occupation = '';
			} else {
				var act_taux_occupation = reponse['Activite'][0].act_taux_occupation;
			}

			if (reponse['Activite'][0].act_description_entraides == null) {
				var act_description_entraides = '';
			} else {
				var act_description_entraides = reponse['Activite'][0].act_description_entraides;
			}

			if (reponse['Activite'][0].act_strategie_montee_en_charge == null) {
				var act_strategie_montee_en_charge = '';
			} else {
				var act_strategie_montee_en_charge = reponse['Activite'][0].act_strategie_montee_en_charge;
			}
		}


		function rechercherObjetsDansOnglet(Id_Zone = '') {
			chp_rechercher_objet = new RegExp($('#chp-rechercher-objet').val(), 'i');

			$(Id_Zone+'.liste').each( function( index, valeur ){
				Valeur = $( this ).text();

				if (chp_rechercher_objet == '') {
					$( this ).show();
				} else {
					if (Valeur.search(chp_rechercher_objet) >= 0) {
						$( this ).show();
					} else {
						if ( $(this).find(':checkbox').is(':checked') == false ) {
							$( this ).hide();
						}
					}
				}
			});
		}


		var Corps =
			// =====================================
			// *************************************
			// Zone entête de la fenêtre de d'Ajout/Modification
			'<div class="row g-3 mb-3">' +
			 '<div class="col-lg-8">' +
			  '<label class="form-label" for="act_nom">' + reponse[ 'L_Nom' ] + '</label>' +
			  '<input id="act_nom" class="form-control" type="text" value="'+ act_nom + '" maxlength="100" required>' +
			 '</div>' +

			 '<div class="col-lg-2">' +
			  '<label class="form-label">' + reponse[ 'L_Niveau_Impact' ] + '</label>' +
			  '<input id="act_niveau_impact_max" class="form-control text-center fw-bold" type="text" value="'+ act_niveau_impact + '" title="' + act_niveau_impact_libelle + '"' + act_niveau_impact_couleur + ' disabled>' +
			 '</div>' +

			 '<div class="col-lg-2">' +
			  '<label class="form-label" title="' + reponse[ 'L_Libelle_DMIA' ] + '">' + reponse[ 'L_DMIA' ] + '</label>' +
			  '<input id="act_dmia_max" class="form-control text-center fw-bold" type="text" value="'+ act_dmia_libelle + '" disabled>' +
			 '</div>' +
			'</div> <!-- .row -->';

			// =====================================
			// *************************************
			// Zone des onglets de la fenêtre de d'Ajout/Modification
			Corps +='<ul class="nav nav-tabs">' +
				'<li><a id="afficher_cartouche" class="nav-link" href="#">' + reponse[ 'L_Cartouche'] + '</a></li>' +
				'<li><a id="afficher_dima" class="nav-link" href="#" title="' + reponse[ 'L_Libelle_DMIA' ] + '">' + reponse[ 'L_DMIA'] + '</a></li>' +
				'<li><a id="afficher_sites" class="nav-link" href="#">' + reponse[ 'L_Sites'] + '</a></li>' +
				'<li><a id="afficher_personnes_cles" class="nav-link" href="#">' + reponse[ 'L_Personnes_Cles'] + '</a></li>' +
				'<li><a id="afficher_interdependances" class="nav-link" href="#">' + reponse[ 'L_Interdependances'] + '</a></li>' +
				'<li><a id="afficher_applications" class="nav-link" href="#">' + reponse[ 'L_Applications'] + '</a></li>' +
				'<li><a id="afficher_fournisseurs" class="nav-link" href="#">' + reponse[ 'L_Fournisseurs'] + '</a></li>' +
				'<li><a id="afficher_personnes_prioritaires" class="nav-link" href="#">' + reponse[ 'L_Personnes_Prioritaires'] + '</a></li>' +
				'</ul>';

			Corps += '<div id="zone-action" class="d-none">' +
				 '<div class="row" id="ZoneRecherche">' +
				  '<div class="col-1" id="zone-btn-creer-objet">' +
				   '<button id="btn-creer-objet" class="btn btn-outline-secondary" title="' + reponse['L_Ajouter'] + '"><i class="bi-plus"></i></button>' +
				  '</div> <!-- .col-1 -->' +
				  '<div class="col-6">' +
				   '<div class="input-group mb-3">' +
				    '<input id="chp-rechercher-objet" class="form-control" type="text" placeholder="' + reponse['L_Rechercher'] + '" aria-label="Chercher" aria-describedby="button-addon2">' +
				    '<label id="btn-rechercher-objets" class="btn btn-outline-secondary" title="' + reponse['L_Rechercher'] + '"><i class="bi-search"></i></label>' +
				   '</div> <!-- .input-group -->' +
				  '</div> <!-- .col-6 -->' +
				 '</div> <!-- #ZoneRecherche -->' +

				 '<div id="ZoneCreerPersonneCle" class="input-group mb-3 d-none">' +
				  '<input type="text" class="form-control text-uppercase" placeholder="' + reponse['L_Nom'] + '" id="ppr_nom_cle">' +
				  '<input type="text" class="form-control text-capitalize" placeholder="' + reponse['L_Prenom'] + '" id="ppr_prenom_cle">' +
				  '<span class="input-group-text"><div class="form-check form-check-reverse">' +
				  '<input class="form-check-input" type="checkbox" value="" id="ppr_interne_cle" checked>' +
				  '<label class="form-check-label" for="ppr_interne_cle">' +reponse['L_Interne'] + '</label>' +
				  '</div></span>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-creer-personne_cle">' + reponse['L_Creer'] + '</button>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-personne_cle">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerPersonneCle -->' +

				 '<div id="ZoneCreerSite" class="input-group mb-3 d-none">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Nom'] + '" id="sts_nom">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Description'] + '" id="sts_description">' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-creer-site">' + reponse['L_Creer'] + '</button>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-site">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerSite -->' +

				 '<div id="ZoneCreerApplication" class="input-group mb-3 d-none">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Nom'] + '" id="app_nom">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Description'] + '" id="app_description">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Hebergement'] + '" id="app_hebergement">' +
				  	  '<select class="form-select" id="frn_id">' +
				  	  ' <option value="">' + reponse['L_Aucun'] + '</option>';
				  var _Description;
				  for (let Fournisseur of reponse['Liste_Fournisseurs']) {
				  	if ( Fournisseur.frn_description != '' ) {
				  		_Description = ' (' + Fournisseur.frn_description + ')';
				  	} else {
				  		_Description = '';
				  	}
				  	Corps += '<option value="' + Fournisseur.frn_id + '">' + Fournisseur.frn_nom + _Description + '</option>';
				  }
				  Corps += '</select>' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Niveau_Service'] + '" id="app_niveau_service">' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-creer-application">' + reponse['L_Creer'] + '</button>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-application">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerApplication -->' +

				 '<div id="ZoneCreerFournisseur" class="input-group mb-3 d-none">' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Nom'] + '" id="frn_nom">' +
				  '<select type="text" class="form-select" placeholder="' + reponse['L_Hebergement'] + '" id="tfr_id">' +
				  ' <option value="">' + reponse['L_Aucun'] + '</option>';
			for (let Type_Fournisseur of reponse['Liste_Types_Fournisseur']) {
				Corps += '<option value="' + Type_Fournisseur.tfr_id + '">' + Type_Fournisseur.tfr_nom_code + '</option>';
			}
			Corps += '</select>' +
				  '<input type="text" class="form-control" placeholder="' + reponse['L_Description'] + '" id="frn_description">' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-creer-fournisseur">' + reponse['L_Creer'] + '</button>' +
				  '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-fournisseur">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerFournisseur -->' +
				'</div> <!-- #zone-action -->';

			// =====================================
			// *************************************
			Corps += '<div id="zone-cartouche">' +
				// ------------------------------------
				// Définition du Responsable d'Activité
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ppr_id_responsable">' + reponse[ 'L_Responsable_Activite' ] + '</label>' +
				'<div class="col-lg-8">' +
				'<div id="section-selectionner-responsable" class="input-group">' + 
				'<select id="ppr_id_responsable" class="form-select" required>' +
				'<option value="">' + reponse['L_Aucun'] + '</option>';
			for (let Partie_Prenante of reponse['Liste_Parties_Prenantes']) {
				if (reponse['Activite'] !== undefined) {
					if (Partie_Prenante.ppr_id == ppr_id_responsable) {
						Selected = ' selected';
					} else {
						Selected = '';
					}
				} else {
					Selected = '';
				}

				if (Partie_Prenante.ppr_interne == true) {
					Flag_Interne = ' (' + reponse['L_Interne'] + ')';
				} else {
					Flag_Interne = '';
				}

				Corps += '<option value="' + Partie_Prenante.ppr_id + '"' + Selected + '>' + Partie_Prenante.ppr_nom + ' ' + Partie_Prenante.ppr_prenom + Flag_Interne + '</option>';
			}
			Corps += '</select>';
			
			if ( reponse['Droit_Ajouter_Personnes_Cles'] == true ) {
				Corps += '<button class="btn btn-outline-secondary" id="btn-section-ajouter-responsable" type="button" title="'+reponse['L_Creer']+'"><i class="bi-plus"></i></button>';
			}

			Corps += '</div> <!-- #section-selectionner-responsable -->' +

				'<div id="section-ajouter-responsable" class="input-group d-none">' +
				'<input type="text" class="form-control text-uppercase" placeholder="' + reponse['L_Nom'] + '" id="ppr_nom_resp">' +
				'<input type="text" class="form-control text-capitalize" placeholder="' + reponse['L_Prenom'] + '" id="ppr_prenom_resp">' +
				'<span class="input-group-text"><div class="form-check form-check-reverse">' +
				'<input class="form-check-input" type="checkbox" value="" id="ppr_interne_resp" checked>' +
				'<label class="form-check-label" for="ppr_interne_resp">' +reponse['L_Interne'] + '</label>' +
				'</div></span>' +
				'<button type="button" class="btn btn-outline-secondary" id="btn-ajouter-responsable">' + reponse['L_Creer'] + '</button>' +
				'<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-responsable">' + reponse['L_Fermer'] + '</button>' +
				'</div> <!-- #section-ajouter-responsable -->' +
				'</div> <!-- .col-lg-8 -->' +
				'</div> <!-- .row -->' +

				// -------------------------------------------------
				// Définition du Suppléant au Responsable d'Activité
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ppr_id_suppleant">' + reponse[ 'L_Suppleant' ] + '</label>' +
				'<div class="col-lg-8">' +
				'<div id="section-selectionner-suppleant" class="input-group">' + 
				'<select id="ppr_id_suppleant" class="form-select">' +
				'<option value="">' + reponse['L_Aucun'] + '</option>';
			for (let Partie_Prenante of reponse['Liste_Parties_Prenantes']) {
				if (reponse['Activite'] !== undefined) {
					if (Partie_Prenante.ppr_id == ppr_id_suppleant) {
						Selected = ' selected';
					} else {
						Selected = '';
					}
				} else {
					Selected = '';
				}

				if (Partie_Prenante.ppr_interne == true) {
					Flag_Interne = ' (' + reponse['L_Interne'] + ')';
				} else {
					Flag_Interne = '';
				}

				Corps += '<option value="' + Partie_Prenante.ppr_id + '"' + Selected + '>' + Partie_Prenante.ppr_nom + ' ' + Partie_Prenante.ppr_prenom + Flag_Interne + '</option>';
			}
			Corps += '</select>';

			if ( reponse['Droit_Ajouter_Personnes_Cles'] == true ) {
				Corps += '<button class="btn btn-outline-secondary" id="btn-section-ajouter-suppleant" type="button" title="'+reponse['L_Creer']+'"><i class="bi-plus"></i></button>';
			}

			Corps += '</div> <!-- #section-selectionner-suppleant -->' +

				'<div id="section-ajouter-suppleant" class="input-group d-none">' +
				'<input type="text" class="form-control text-uppercase" placeholder="' + reponse['L_Nom'] + '" id="ppr_nom_supp">' +
				'<input type="text" class="form-control text-capitalize" placeholder="' + reponse['L_Prenom'] + '" id="ppr_prenom_supp">' +
				'<span class="input-group-text"><div class="form-check form-check-reverse">' +
				'<input class="form-check-input" type="checkbox" value="" id="ppr_interne_supp" checked>' +
				'<label class="form-check-label" for="ppr_interne_supp">' +reponse['L_Interne'] + '</label>' +
				'</div></span>' +
				'<button type="button" class="btn btn-outline-secondary" id="btn-ajouter-suppleant">' + reponse['L_Creer'] + '</button>' +
				'<button type="button" class="btn btn-outline-secondary" id="btn-fermer-zone-suppleant">' + reponse['L_Fermer'] + '</button>' +
				'</div> <!-- #section-ajouter-suppleant -->' +

				'</div> <!-- .col-lg-8 -->' +
				'</div> <!-- .row -->' +

				// Effectif total dans l'Entité
				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="act_effectifs_en_nominal">' + reponse[ 'L_Effectif_Total_Entite' ] + '</label>' +
				 '<div class="col-lg-1">' +
				  '<input id="cmen_effectif_total" type="number" class="form-control bg-gris-clair" data-old_value="' + reponse['Total_Effectif_Entite'] + '" value="' + reponse['Total_Effectif_Entite'] + '">' +
				 '</div>' +
				'</div>' +

				// Effectif en nominal
				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="act_effectifs_en_nominal">' + reponse[ 'L_Effectifs_En_Nominal' ] + '</label>' +
				 '<div class="col-lg-1">' +
				  '<input id="act_effectifs_en_nominal" type="number" class="form-control" value="' + act_effectifs_en_nominal + '">' +
				 '</div>' +
				 '<label class="col-lg-2 col-form-label text-end" for="act_taux_occupation">' + reponse[ 'L_Taux_Occupation' ] + '</label>' +
				 '<div class="col-lg-1">' +
				  '<input id="act_taux_occupation" type="number" class="form-control" value="' + act_taux_occupation + '">' +
				 '</div>' +
				 '<label class="col-lg-1 col-form-label" for="act_taux_occupation">%</label>' +
				'</div>' +

				// Effectif à distance
				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="act_effectifs_a_distance">' + reponse[ 'L_Effectifs_A_Distance' ] + '</label>' +
				 '<div class="col-lg-1">' +
				  '<input id="act_effectifs_a_distance" type="number" class="form-control" value="' + act_effectifs_a_distance + '">' +
				 '</div>' +
				'</div>' +

				// Définition si l'activité est télévraillable. 
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="act_teletravail">' + reponse[ 'L_Activite_Teletravaillable' ] + '</label>' +
				'<div class="col-lg-2">' +
				'<select id="act_teletravail" class="form-select">';
			Selection_Oui = '';
			Selection_Non = '';
			if (reponse['Activite'] !== undefined) {
				if (act_teletravail == 1 || act_teletravail == true ) Selection_Oui = 'selected';
				
				if (act_teletravail == 0 || act_teletravail == false ) Selection_Non = 'selected';
			}
			Corps += '<option value="1" ' + Selection_Oui + '>' + reponse['L_Oui'] + '</option>' +
				'<option value="0" ' + Selection_Non + '>' + reponse['L_Non'] + '</option>' +
				'</select>' +
				'</div>' +
				'</div>';

			Corps += '<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="act_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="act_description" class="form-control" rows="2">'+ act_description + '</textarea>' +
				'</div> <!-- .col-lg-10 -->' +
				'</div> <!-- .row -->' +
				'</div> <!-- #zone-cartouche -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-dima" class="d-none">';
			var _Row_1 = '';
			var _Row_2 = '';
			var _Row_3 = '';
			var _Row_4 = '';
			var _largeur_colonne = 100 / reponse['Liste_EchellesTemps'].length;
			for (let EchelleTemps of reponse['Liste_EchellesTemps']) {
				// Gestion de l'entête du tableau
				_Row_1 += '<th class="titre-fond-bleu border border-secondary-subtle" ' +
				 'width="'+_largeur_colonne+'%" id="t_ete_id_'+EchelleTemps.ete_id+'">'+EchelleTemps.ete_nom_code+'</th>';
				Numero_Echelle = '0';
				Couleur_Echelle = '';
				Association_Matrice = '';
				Poids_Niveau_Impact = '';
				Nom_Type_Impact = '';
				if (reponse['Liste_DMIA'] != undefined && reponse['Liste_DMIA'] != []) {
					for (let DetailEchelle of reponse['Liste_DMIA']) {
						//alert(DetailEchelle.ete_id+' == '+EchelleTemps.ete_id);
						if (DetailEchelle.ete_id == EchelleTemps.ete_id) {
							Numero_Echelle = DetailEchelle.nim_numero;
							Couleur_Echelle = 'style="background-color: #'+DetailEchelle.nim_couleur+';" ';
							Association_Matrice = 'data-mim_id="'+DetailEchelle.mim_id+'" data-mim_id-old="'+DetailEchelle.mim_id+'" ';
							Poids_Niveau_Impact = 'data-nim_poids="'+DetailEchelle.nim_poids+'" ';
							Nom_Type_Impact = DetailEchelle.tim_nom_code;
						}
					}
				}
				// Affichage du niveau retenu.
				_Row_2 += '<td class="border border-secondary-subtle cellule-echelle" id="echelle-1-'+EchelleTemps.ete_id+'" ' +
					'data-ete_id="'+EchelleTemps.ete_id+'" ' +
					//'data-cmp_id="'+EchelleTemps.cmp_id+'" ' +
					'data-ete_poids="'+EchelleTemps.ete_poids+'" ' +
					Poids_Niveau_Impact+
					Couleur_Echelle +
					Association_Matrice +
					'>'+Numero_Echelle+'</td>';
				// Affichage du type d'impact retenu
				_Row_3 += '<td id="echelle-2-'+EchelleTemps.ete_id+'" style="background-color: silver;">'+Nom_Type_Impact+'</td>';
				// Affichage du type d'impact retenu
				_Row_4 += '<td id="echelle-3-'+EchelleTemps.ete_id+'"></td>';
			}
			Corps += '<table class="table-100">' +
				'<thead class="text-center"><tr>' + _Row_1 + '</tr></thead>' +
				'<tbody class="text-center">' +
				'<tr>' + _Row_2 + '</tr>' +
				'<tr>' + _Row_3 + '</tr>' +
				'<tr>' + _Row_4 + '</tr>' +
				'</tbody>' +
				'</table>' +
				 '<div id="tableau-matrice" class="d-none">' +
				 chargerTableauMatrice(reponse['Liste_Niveaux_Impact'], reponse['Liste_Types_Impact'], reponse['Liste_Matrice_Impacts'], reponse['L_Type'], reponse['L_Niveau']) +
				 '</div> <!-- #tableau-matrice -->' +
				 '<div class="row mt-3">' +
				  '<label for="act_justification_dmia" class="col-3 col-form-label text-end">'+reponse['L_Justification_DMIA']+'</label>' +
				  '<div class="col-8">' +
				   '<textarea class="form-control" id="act_justification_dmia">' +
				   act_justification_dmia +
				   '</textarea>' +
				  '</div>' +
				 '</div> <!-- .row -->' +
				'</div> <!-- zone-dima -->';


				// =====================================
				// *************************************
				Corps += '<div id="zone-personnes_prioritaires" class="d-none">';
				var _Row_1 = '';
				var _Row_2 = '';
				var _largeur_colonne = 100 / reponse['Liste_EchellesTemps'].length;
				for (let EchelleTemps of reponse['Liste_EchellesTemps']) {
					// Gestion de l'entête du tableau
					_Row_1 += '<th class="titre-fond-bleu border border-secondary-subtle" ' +
					 'width="'+_largeur_colonne+'%" id="t_ete_id_'+EchelleTemps.ete_id+'">'+EchelleTemps.ete_nom_code+'</th>';
					Nbr_Personnes_Prioritaires = '';
					if (reponse['Liste_Personnes_Prioritaires'] != undefined && reponse['Liste_Personnes_Prioritaires'] != []) {
						for (let Personnes_Prioritaires of reponse['Liste_Personnes_Prioritaires']) {
							//alert(DetailEchelle.ete_id+' == '+EchelleTemps.ete_id);
							if (Personnes_Prioritaires.ete_id == EchelleTemps.ete_id) {
								Nbr_Personnes_Prioritaires = Personnes_Prioritaires.rut_nbr_utilisateurs_a_redemarrer;
							}
						}
					}
					// Affichage du niveau retenu.
					_Row_2 += '<td class="border border-secondary-subtle cellule-echelle">' +
						'<input  id="personnes_prioritaires-'+EchelleTemps.ete_id+'" type="number" step="0.05" class="form-control" ' +
						'data-old_value="'+Nbr_Personnes_Prioritaires+'" value="'+Nbr_Personnes_Prioritaires+'">' +
						'</td>';
				}
				Corps += '<table class="table-100">' +
					'<thead class="text-center"><tr>' + _Row_1 + '</tr></thead>' +
					'<tbody class="text-center">' +
					'<tr>' + _Row_2 + '</tr>' +
					'</tbody>' +
					'</table>' +
					 '<div class="row mt-3">' +
					 '<div class="col-6">' +
					  '<label for="act_strategie_montee_en_charge" class="form-label">'+reponse['L_Description_Strategie_Montee_Charge']+'</label>' +
					   '<textarea class="form-control" id="act_strategie_montee_en_charge" row="3">' +
					   act_strategie_montee_en_charge +
					   '</textarea>' +
					  '</div> <!-- .col-6 -->' +
					  '<div class="col-6">' +
					   '<label for="act_description_entraides" class="form-label">'+reponse['L_Description_Entraides']+'</label>' +
					    '<textarea class="form-control" id="act_description_entraides" row="3">' +
					    act_description_entraides +
					    '</textarea>' +
					   '</div> <!-- .col-6 -->' +
					 '</div> <!-- .row -->' +
					'</div> <!-- zone-personnes_prioritaires -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-sites" class="overflow-y-scroll d-none" style="height: 550px">';

			for (let Site of reponse['Liste_Sites']) {
				if (Site.associe !== null) {
					var Checked = ' checked';
					var Old_Value = 1;
				} else {
					var Checked = '';
					var Old_Value = 0;
				}

				if ( Site.sts_description != '' && Site.sts_description != null ) {
					var Description_Site = ' (' + Site.sts_description + ')';
				} else {
					var Description_Site = '';
				}

				var Select_Nominal = '';
				var Select_Secours = '';

				if ( Site.acst_type_site == 0 ) {
					var Select_Nominal = ' selected';
				} else if ( Site.acst_type_site == 1 ) {
					var Select_Secours = ' selected';
				}

				Corps += '<div class="row liste mt-1">' +
					'<div class="col-6">' +
					'<div class="form-check">' +
					'<input type="checkbox" class="form-check-input" id="sts-' + Site.sts_id + '" data-old_value="' + Old_Value + '" ' + Checked + '>' +
					'<label class="form-check-label" for="sts-' + Site.sts_id + '">' + Site.sts_nom + Description_Site + '</label>' +
					'</div> <!-- .form-check -->' +
					'</div> <!-- .col-6 -->' +
					'<div class="col-2">' +
					'<select class="form-select" id="acst_type_site-' + Site.sts_id + '" data-old_value="' + Site.acst_type_site + '">' +
					'<option value="">' + reponse["L_Aucun"] + '</option>' +
					'<option value="0"' + Select_Nominal + '>' + reponse["L_Site_Nominal"] + '</option>' +
					'<option value="1"' + Select_Secours + '>' + reponse["L_Site_Secours"] + '</option>' +
					'</select>' +
					'</div> <!-- .col-2 -->' +
					'</div> <!-- .row -->';
			}

			Corps += '</div> <!-- zone-sites -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-personnes_cles" class="d-none overflow-y-scroll" style="height: 550px;">';
			if (reponse['Liste_Personnes_Cles'] != []) {
				for (let Personne_Cle of reponse['Liste_Personnes_Cles']) {
					if (Personne_Cle.associe != null) {
						Associe = ' checked';
						Old_Value = 1;
					} else {
						Associe = '';
						Old_Value = 0;
					}

					if (Personne_Cle.ppr_interne == true) {
						ppr_interne = ' (' + reponse['L_Interne'] + ')';
					} else {
						ppr_interne = '';
					}

					Nom_Complet = Personne_Cle.ppr_nom + ' ' + Personne_Cle.ppr_prenom + ppr_interne;
					
					if (Personne_Cle.ppac_description != '' && Personne_Cle.ppac_description != null) {
						t_ppac_description = Personne_Cle.ppac_description;
					} else {
						t_ppac_description = '';
					}
					
					if (Personne_Cle.ppr_description != '' && Personne_Cle.ppr_description != null) {
						t_ppr_description = Personne_Cle.ppr_description;
					} else {
						t_ppr_description = '';
					}

					Corps += '<div class="row liste">' +
						 '<div class="col-5">' +
						  '<div class="form-check">' + 
						   '<input class="form-check-input" type="checkbox" value="" data-old_value="'+Old_Value+'" id="cle-'+Personne_Cle.ppr_id+'"'+Associe+'>' +
						   '<label class="form-check-label" for="cle-'+Personne_Cle.ppr_id+'">'+Nom_Complet+'</label>' +
						  '</div> <!-- .form-check -->' +
						 '</div> <!-- .col-5 -->' +
						 '<label for="ppac_description-'+Personne_Cle.ppr_id+'" class="form-label col-2">'+reponse['L_Description']+'</label>' +
						 '<div class="col-5">' +
						  '<textarea id="ppac_description-'+Personne_Cle.ppr_id+'" type="text" class="form-control" placeholder="'+t_ppr_description+'">'+t_ppac_description+'</textarea>' +
						 '</div> <!-- .col-5 -->' +
						'</div> <!-- .row -->';
				}
			}
			Corps += '</div> <!-- #zone-personnes_cles -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-interdependances" class="d-none">' +
				 '<div class="row">' +
				  '<div class="col-6">' +
				   '<label for="act_dependances_internes_amont" class="form-label">'+reponse['L_Dependances_Internes_Amont']+'</label>' +
				   '<textarea id="act_dependances_internes_amont" type="text" class="form-control" rows="3">'+act_dependances_internes_amont+'</textarea>' +
				  '</div> <!-- .col-6 -->' +
				  '<div class="col-6">' +
				   '<label for="act_dependances_internes_aval" class="form-label">'+reponse['L_Dependances_Internes_Aval']+'</label>' +
				   '<textarea id="act_dependances_internes_aval" type="text" class="form-control" rows="3">'+act_dependances_internes_aval+'</textarea>' +
				  '</div> <!-- .col-6 -->' +
				 '</div> <!-- .row -->' +
				'</div> <!-- #zone-interdependances -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-applications" class="d-none">';
			if (reponse['Liste_Applications'] != []) {
				Corps += '<div class="row titre">' +
				 '<div class="col-3">'+reponse['L_Nom']+'</div>' +
				 '<div class="col-2" title="'+reponse['L_Libelle_DMIA']+'">'+reponse['L_DMIA']+'</div>' +
				 '<div class="col-2" title="'+reponse['L_Libelle_PDMA']+'">'+reponse['L_PDMA']+'</div>' +
				 '<div class="col-3">'+reponse['L_Donnees']+'</div>' +
				 '<div class="col-2">'+reponse['L_Palliatif']+'</div>' +
				'</div> <!-- .row .titre -->' +
				'<div id="liste-donnees" class="overflow-y-scroll" style="height: 530px;">';

				for (let Application of reponse['Liste_Applications']) {
					if (Application.associe != null) {
						Associe = ' checked';
						Old_Value = 1;
					} else {
						Associe = '';
						Old_Value = 0;
					}

					var Nom_Complet = Application.app_nom;

					if (Application.frn_id != '' && Application.frn_id != null) {
						Nom_Complet += ' [' + Application.frn_nom + ']';
					}

					if (Application.app_description != '' && Application.app_description != null) {
						Nom_Complet += ' [' + Application.app_description + ']';
					}


					if (Application.app_hebergement == null) Application.app_hebergement = '';
					if (Application.acap_hebergement == null) Application.acap_hebergement = '';
					if (Application.app_hebergement == '') {
						Application.app_hebergement = reponse['L_Hebergement'];
					}
					Nom_Complet += '<input type="text" class="form-control" id="acap_hebergement-'+Application.app_id+'" ' +
						'placeholder="' + Application.app_hebergement + '" value="' + Application.acap_hebergement + '">';

					if (Application.app_niveau_service == null) Application.app_niveau_service = '';
					if (Application.acap_niveau_service == null) Application.acap_niveau_service = '';
					if (Application.app_niveau_service == '') {
						Application.app_niveau_service = reponse['L_Niveau_Service'];
					}
					Nom_Complet += '<input type="text" class="form-control" id="acap_niveau_service-'+Application.app_id+'" ' + 
						'placeholder="' + Application.app_niveau_service + '" value="' + Application.acap_niveau_service + '">';


					var t_ete_id_dima = Application.ete_id_dima;
					var t_ete_id_pdma = Application.ete_id_pdma;

					if ( Application.acap_palliatif == null) {
						Application.acap_palliatif = '';
					}

					if ( Application.acap_donnees == null) {
						Application.acap_donnees = '';
					}

					Corps += creerOccurrenceApplicationDansListe(Application.app_id, Nom_Complet, Old_Value,
						Associe, reponse['Liste_EchellesTemps'], reponse['L_DMIA'], t_ete_id_dima,
						reponse['L_PDMA'], t_ete_id_pdma, reponse['L_Palliatif'], Application.acap_donnees,
						Application.acap_palliatif,
						reponse['L_Aucun'], Application.ete_id_dima_dsi, Application.ete_id_pdma_dsi,
						reponse['L_DMIA_SI'], reponse['L_PDMA_SI'], Application.associe, reponse['L_Nom'], reponse['L_Gerer_Donnees'],
						reponse['L_Integrite'], reponse['L_Confidentialite'], reponse['L_Tracabilite'], reponse['L_Creer']);
				}
				Corps += '</div> <!-- #liste-donnees -->';
			}
			Corps += '</div> <!-- #zone-applications -->';


			// =====================================
			// *************************************
			Corps += '<div id="zone-fournisseurs" class="d-none">';
			if (reponse['Liste_Fournisseurs'] != []) {
				Corps += '<div class="row titre">' +
				 '<div class="col-4">'+reponse['L_Nom']+'</div>' +
				 '<div class="col-2" title="'+reponse['L_Libelle_DMIA']+'">'+reponse['L_DMIA']+'</div>' +
				 '<div class="col-3">'+reponse['L_Consequence_Indisponibilite']+'</div>' +
				 '<div class="col-3">'+reponse['L_Palliatif']+'</div>' +
				'</div> <!-- .row .titre -->' +
				'<div id="liste-donnees" class="overflow-y-scroll" style="height: 530px;">';

				for (let Fournisseur of reponse['Liste_Fournisseurs']) {
					if (Fournisseur.associe != null) {
						Associe = ' checked';
						Old_Value = 1;
					} else {
						Associe = '';
						Old_Value = 0;
					}

					var Nom_Complet = Fournisseur.frn_nom;

					if (Fournisseur.frn_description != '' && Fournisseur.frn_description != null) {
						Nom_Complet += ' [' + Fournisseur.frn_description + ']';
					}

					var t_ete_id = Fournisseur.ete_id;

					if ( Fournisseur.acfr_consequence_indisponibilite == null) {
						Fournisseur.acfr_consequence_indisponibilite = '';
					}

					if ( Fournisseur.acfr_palliatif_tiers == null) {
						Fournisseur.acfr_palliatif_tiers = '';
					}

					Corps += creerOccurrenceFournisseurDansListe(Fournisseur.frn_id, Nom_Complet, Old_Value,
						Associe, reponse['Liste_EchellesTemps'], reponse['L_DMIA'], t_ete_id,
						reponse['L_Consequence_Indisponibilite'], Fournisseur.acfr_consequence_indisponibilite,
						reponse['L_Palliatif'], Fournisseur.acfr_palliatif_tiers,
						reponse['L_Aucun']);
				}
				Corps += '</div> <!-- #liste-donnees -->';
			}
			Corps += '</div> <!-- #zone-fournisseurs -->';


			construireModal( 'idModal',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formAjouterModifier', 'modal-xxl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				function recupererDMIA() {
					MaxNumPoids = 0;
					MaxEchPoids = 100;
					NumeroNiveauImpact = 0;
					CouleurNiveauImpact = '';
					LibelleEchelleTemps = '';

					$('.cellule-echelle').each(function(index, element) {
						NumPoidsCourant = Number($(element).attr('data-nim_poids'));
						EchPoidsCourant = Number($(element).attr('data-ete_poids'));

						if (NumPoidsCourant == NaN) {
							return [NumeroNiveauImpact , CouleurNiveauImpact, LibelleEchelleTemps];
						}

						if (NumPoidsCourant == 3) {
							if (MaxEchPoids > EchPoidsCourant) {
								MaxEchPoids = EchPoidsCourant;
								LibelleEchelleTemps = $('#t_ete_id_'+$(element).attr('data-ete_id')).text();
							}
						}

						if (MaxNumPoids < NumPoidsCourant) {
							MaxNumPoids = NumPoidsCourant;
							NumeroNiveauImpact = $(element).text();
							CouleurNiveauImpact = $(element).css('background-color');
						}
					});

					if (MaxNumPoids < 3) {
						LibelleEchelleTemps = '';
					}

					return [NumeroNiveauImpact , CouleurNiveauImpact, LibelleEchelleTemps];
				}

				document.getElementById('act_nom').selectionStart = act_nom.length;
				$('#act_nom').focus();

				$('#chp-rechercher-objet').off('keypress').on('keypress', function( eventKey){
					if ( eventKey.which == 13) return false;
				});


				$('[id^="echelle-1-"]').on('click', function(){
					var objID = $(this).attr('id');
					var tName = objID.split('-');

					$('[id^="mim_id-"]').removeClass('active');

					if ($('#'+objID).hasClass('active') == false) {
						$('[id^="echelle-1-"]').removeClass('active');
						$('#'+objID).addClass('active');

						$('#tableau-matrice').removeClass('d-none');

						$('[id^="echelle-3-"]').text('');
						$('#echelle-3-'+tName[2]).html('<i class="bi bi-triangle-fill"></i>');
						
						if ($('#'+objID).attr('data-mim_id') != undefined) {
							mim_id = $('#'+objID).attr('data-mim_id');
//							$('[id^="mim_id-"]').removeClass('active');
							$('#mim_id-'+mim_id).addClass('active');
						}
					} else {
						$('[id^="echelle-1-"]').removeClass('active');
						$('#tableau-matrice').addClass('d-none');
						$('[id^="echelle-3-"]').text('');
					}

					return -1;
				});

				$(".cellule-impact").on('click', function() {
					var mim_id = $(this).attr('data-mim_id');
					var nim_numero = $(this).attr('data-nim_numero');
					var nim_couleur = $(this).attr('data-nim_couleur');
					var nim_poids = $(this).attr('data-nim_poids');
					var tim_nom = $(this).attr('data-tim_nom');
					var cellule_id = $('.cellule-echelle.active').attr('id').split('-')[2];

					var __id = $('.cellule-echelle.active').attr('id');

					$('.cellule-echelle.active').attr('data-mim_id', mim_id)
						.attr('data-nim_poids', nim_poids).text(nim_numero)
						.css('background-color', '#'+nim_couleur)
						.trigger('click');

					$('#echelle-2-'+cellule_id).text(tim_nom);


					// Vérifie s'il faut aligner les données avec la nouvelle valeur saisie.
					// Les autres cellules sont modifiées, si leurs valeurs sont inférieures à la valeur qui vient d'être saisie.
//					alert(__id);
					__elem = __id.split('-');
					__base1 = __elem[0] + '-1-';
					__base2 = __elem[0] + '-2-';
					__number = Number(__elem[2]);
//					alert(__id+' => '+__number);

					for(let i = __number+1;i< __number+30;i++){
						__id1 = __base1 + i;
						__id2 = __base2 + i;
						__found = $('td#'+__id1).attr('id');
//						alert(__id1+' => '+__number);

						if (__found != undefined) {
							__next_poids = $('td#'+__id1).attr('data-nim_poids');
							if (__next_poids == undefined) {
								__next_poids = 0;
							}
							if (__next_poids < nim_poids) {
								$('td#'+__id1).attr('data-mim_id', mim_id)
									.attr('data-nim_poids', nim_poids).text(nim_numero)
									.css('background-color', '#'+nim_couleur);
	
								$('td#'+__id2).text(tim_nom);
							}
						} else {
							break;
						}
					}

					$('[id^="mim_id-"]').removeClass('active');
					$(this).addClass('active');
					
					var tAnalyse = recupererDMIA();
					if (tAnalyse[0] != 0) {
						$('#act_dmia_max').val(tAnalyse[2]);
						$('#act_niveau_impact_max').val(tAnalyse[0]).css('background-color', tAnalyse[1]);
					} else {
						$('#act_dmia_max').val('').css('background-color', '');
					}
				});

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
				// Gestion de l'onglet "Cartouche"
				$('#afficher_cartouche').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_cartouche').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-cartouche').removeClass();

					$('[id="act_teletravail"').off('change').on('change', function(){
						if ( $('#act_teletravail').val() == 0 ) {
							$('#sts_id_secours').attr('required', 'required');
						} else {
							$('#sts_id_secours').removeAttr('required');
						}
					});

					if ( act_id == '' ) {
						$('#act_nom').focus();
					} else {
						$('div#zone-cartouche select:first').focus();
					}
				});


				// =========
				// Gestion de l'onglet "DIMA"
				$('#afficher_dima').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_dima').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-dima').removeClass('d-none');
				});


				// =========
				// Gestion de l'onglet "Personnes Prioritaires"
				$('#afficher_personnes_prioritaires').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_personnes_prioritaires').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-personnes_prioritaires').removeClass('d-none');
				});


				// =========
				// Gestion de l'onglet "Sites"
				$('#afficher_sites').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_sites').addClass('active');
	
					$('div[id^=zone-]').addClass('d-none');
					$('#zone-sites').removeClass('d-none');

					$('#zone-action').removeClass('d-none');
					$('div[id^="ZoneCreer"]').addClass('d-none');
					$('div[id^="ZoneRecherche"]').removeClass('d-none');

					$('#act_teletravail_2').val($('#act_teletravail option:selected').text());

					if ( reponse['Droit_Ajouter_Sites'] == true ) {
						$('#zone-btn-creer-objet').removeClass('d-none');
					} else {
						$('#zone-btn-creer-objet').addClass('d-none');
					}

					$('#chp-rechercher-objet').val('');
					rechercherObjetsDansOnglet('div#zone-sites ');

					// ---------
					// Gestion de la zone "Action" de l'Onglet
					$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
						rechercherObjetsDansOnglet('div#zone-sites ');
					});

					$('#btn-fermer-zone-site').off('click').on('click', function(){
						$('#ZoneCreerSite').addClass('d-none');
						$('#ZoneRecherche').removeClass('d-none');

						$('#sts_nom').val('');
						$('#sts_description').val('');
					});

					$('#btn-creer-objet').off('click').on('click', function(){
						$('#ZoneCreerSite').removeClass('d-none');
						$('#ZoneRecherche').addClass('d-none');

						$('div#ZoneCreerSite input:first').focus();

						return false;
					});

					$('#btn-creer-site').off('click').on('click', function(){
						creerSite();
					});

					$('div#zone-sites select:first').focus();
				});


				// =========
				// Gestion de l'onglet "Personnes Clés"
				$('#afficher_personnes_cles').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_personnes_cles').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-personnes_cles').removeClass('d-none');

					$('#zone-action').removeClass('d-none');
					$('div[id^="ZoneCreer"]').addClass('d-none');
					$('div[id^="ZoneRecherche"]').removeClass('d-none');

					if ( reponse['Droit_Ajouter_Personnes_Cles'] == true ) {
						$('#zone-btn-creer-objet').removeClass('d-none');
					} else {
						$('#zone-btn-creer-objet').addClass('d-none');
					}

					$('#chp-rechercher-objet').val('');
					rechercherObjetsDansOnglet('div#zone-personnes_cles ');

					$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
						rechercherObjetsDansOnglet('div#zone-personnes_cles ');
					});

					$('#btn-fermer-zone-personne_cle').off('click').on('click', function(){
						$('#ZoneCreerPersonneCle').addClass('d-none');
						$('#ZoneRecherche').removeClass('d-none');

						$('#ppr_nom_cle').val('');
						$('#ppr_prenom_cle').val('');
						$('#ppr_interne_cle').val('');
					});

					$('#btn-creer-objet').off('click').on('click', function(){
						$('#ZoneCreerPersonneCle').removeClass('d-none');
						$('#ZoneRecherche').addClass('d-none');

						$('div#ZoneCreerPersonneCle input:first').focus();

						return false;
					});

					$('#btn-creer-personne_cle').off('click').on('click', function(){
						creerPersonneCle(reponse['L_ERR_Champs_Obligatoires'], reponse['L_Interne'] );
					});

					$('div#zone-personnes_cles input:first').focus();
				});


				// =========
				// Gestion de l'onglet "Interdependances"
				$('#afficher_interdependances').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_interdependances').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-interdependances').removeClass('d-none');

					$('div#zone-interdependances textarea:first').focus();
				});


				// =========
				// Gestion de l'onglet "Applications"
				$('#afficher_applications').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#afficher_applications').addClass('active');

					$('div[id^=zone-]').addClass('d-none');
					$('#zone-applications').removeClass('d-none');

					$('#zone-action').removeClass('d-none');
					$('div[id^="ZoneCreer"]').addClass('d-none');
					$('div[id^="ZoneRecherche"]').removeClass('d-none');

					if ( reponse['Droit_Ajouter_Applications'] == true ) {
						$('#zone-btn-creer-objet').removeClass('d-none');
					} else {
						$('#zone-btn-creer-objet').addClass('d-none');
					}

					$('#chp-rechercher-objet').val('');
					rechercherObjetsDansOnglet('div#zone-applications ');

					$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
						rechercherObjetsDansOnglet('div#zone-applications ');
					});

					$('#btn-fermer-zone-application').off('click').on('click', function(){
						$('#ZoneCreerApplication').addClass('d-none');
						$('#ZoneRecherche').removeClass('d-none');

						$('#app_nom').val('');
						$('#app_hebergement').val('');
						$('#app_niveau_service').val('');
						$('#app_description').val('');
					});

					$('#btn-creer-objet').off('click').on('click', function(){
						$('#ZoneCreerApplication').removeClass('d-none');
						$('#ZoneRecherche').addClass('d-none');

						$('div#ZoneCreerApplication input:first').focus();

						return false;
					});

					$('#btn-creer-application').off('click').on('click', function(){
						creerApplication();
					});

					$('[id^="choix_application-"').on('change', function(){
						var _Id = $(this).attr('id').split('-')[1];

						if ($('#choix_application-'+_Id).is(':checked')) {
							$('#ete_id_dima-'+_Id).attr('required', 'required');
							$('#ete_id_pdma-'+_Id).attr('required', 'required');
						} else {
							$('#ete_id_dima-'+_Id).removeAttr('required');
							$('#ete_id_pdma-'+_Id).removeAttr('required');
						}
					});

					$('div#zone-applications select:first').focus();
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


			$('#idBoutonAjouter').on('click', function( event ) {
				//event.preventDefault(); // Laisse le contrôle au Javascript.

				if ($('#ppr_id_responsable').val() == '') {
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
				}
			});


			$('#formAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( act_id == '' ) {
					AjouterActivite();
				} else {
					ModifierActivite( act_id );
				}
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



function creerOccurrenceApplicationDansListe(ID_Application, Nom_Application, Flag_Selection_Origine, Selection_Actuelle, Liste_EchellesTemps,
	Libelle_DMIA, ID_DIMA, Libelle_PDMA, ID_PDMA, Libelle_Palliatif, Donnees, Palliatif, Libelle_Aucun, ID_DIMA_DSI, ID_PDMA_DSI, Libelle_DMIA_SI,
	Libelle_PDMA_SI, ID_Activite, L_Nom, L_Gerer_Donnees, L_Integrite, L_Confidentialite, L_Tracabilite, L_Creer) {
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
//		  '<textarea class="form-control" type="checkbox" id="acap_donnees-'+ID_Application+'" rows="3">' +
//		   Donnees+'</textarea>' +
		  '<table id="tabapp-' + ID_Application + '-' + ID_Activite + '" class="table table-bordered table-striped">' + 
		   '<thead>' +
		   '<tr>' +
		    '<th>' + L_Nom + '</th>' +
			'<th title="' + L_Integrite + '" class="text-center">I</th>' +
			'<th title="' + L_Confidentialite + '" class="text-center">C</th>' +
			'<th title="' + L_Tracabilite + '" class="text-center">T</th>' +
		   '</tr>' +
		  '</thead>' +
		  '<tbody>';

	if ( Donnees != null && Donnees != '' ) {
		T_Occurrences = Donnees.split('###');
		for (let Occurrences of T_Occurrences) {
			Colonnes = Occurrences.split('---');
	
			Corps += '<tr id="dap_id-' + Colonnes[4] + '">' + 
				'<td>' + Colonnes[0] + '</td>' +
				'<td class="text-center">' + Colonnes[1] + '</td>' +
				'<td class="text-center">' + Colonnes[2] + '</td>' +
				'<td class="text-center">' + Colonnes[3] + '</td>' +
				'</tr>';
		}
	}

	Corps += '</tbody>' +
		 '</table>' +
		 '<a id="creer_donnees_application" class="btn btn-outline-secondary" ' +
		  'href="javascript:creerSuperModal(' + ID_Application + ', ' + ID_Activite + ', \'' + L_Creer + '\')">' + L_Gerer_Donnees + '</a>' + 
		 '</div> <!-- .col-3 -->' +
		 '<div class="col-2">' +
		  '<textarea class="form-control" type="checkbox" id="acap_palliatif-'+ID_Application+'" rows="3">' +
		   Palliatif+'</textarea>' +
		 '</div> <!-- .col-2 -->' +
		'</div> <!-- .row -->';

	return Corps;
}


function creerSuperModal( ID_Application, ID_Activite, L_Creer ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller_Donnees',
		type: 'POST',
		data: $.param({'app_id': ID_Application, 'act_id': ID_Activite}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var Titre = reponse["L_Gerer_Donnees"];
			var Bouton = reponse["L_Modifier"];
			var ancienZindex;

			var Corps = '<table id="tabapp-' + ID_Application + '-' + ID_Activite + '" class="table table-bordered table-striped">' + 
			 '<thead>' +
			  '<tr>' +
			   '<th class="col-8">' + reponse["L_Nom"] + '</th>' +
			   '<th class="text-center col-1">' + reponse['L_Integrite'] + '</th>' +
			   '<th class="text-center col-1">' + reponse['L_Confidentialite'] + '</th>' +
			   '<th class="text-center col-1">' + reponse['L_Tracabilite'] + '</th>' +
			   '<th class="text-center col-1">' + reponse['L_Supprimer'] + '</th>' +
			  '</tr>' +
			 '</thead>' +
			 '<tbody>';

			for (let Donnees of reponse['Liste_Donnees']) {
				if ( Donnees != '') {
					Corps += '<tr class="data-modification" id="dap-' + Donnees.dap_id + '">' +
						'<td><input id="dap-nom-' + Donnees.dap_id + '" type="text" class="form-control" data-old_value="' + Donnees.dap_nom_donnee_application + '" value="' + Donnees.dap_nom_donnee_application + '"></td>' +
						'<td class="text-center"><input id="dap-i-' + Donnees.dap_id + '" type="number" class="form-control" data-old_value="' + Donnees.dap_integrite_donnee_application + '" value="' + Donnees.dap_integrite_donnee_application + '" min="1" max="4"></td>' +
						'<td class="text-center"><input id="dap-c-' + Donnees.dap_id + '" type="number" class="form-control" data-old_value="' + Donnees.dap_confidentialite_donnee_application + '" value="' + Donnees.dap_confidentialite_donnee_application + '" min="1" max="4"></td>' +
						'<td class="text-center"><input id="dap-t-' + Donnees.dap_id + '" type="number" class="form-control" data-old_value="' + Donnees.dap_tracabilite_donnee_application + '" value="' + Donnees.dap_tracabilite_donnee_application + '" min="1" max="4"></td>' +
						'<td class="text-center"><input id="dap-delete-' + Donnees.dap_id + '" type="checkbox" class="form-check-input"></td>' +
						'</tr>';
				}
			}

			Corps += '</tbody>' +
			 '</table>' +
			 '<a id="creer-nouvelle-donnee" class="btn btn-outline-secondary">' + L_Creer + '</a>';

			construireModal( 'superModal',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formAjouterModifierData', 'modal-xxl' );
		
			$('#superModal').modal('show'); // Affiche la modale qui vient d'être créée
		
			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#superModal').on('shown.bs.modal', function() {
				$('#superModal').css('z-index', 1100);
				ancienZindex = $('.modal-backdrop').css('z-index');
				$('.modal-backdrop').css('z-index', 1099);
			});
		
			// Supprime la modale après l'avoir caché.
			$('#superModal').on('hidden.bs.modal', function() {
				$('#superModal').remove();
		
				$('.modal-backdrop').css('z-index', ancienZindex);
			});

			// Création d'une nouvelle occurrence de données dans le tableau
			var Index_Data_Creation = 0;
			$('#creer-nouvelle-donnee').on('click', function() {
				Index_Data_Creation += 1;

				New_Occurrence = '<tr class="data-creation" id="new-data-' + Index_Data_Creation + '">' +
					'<td><input type="text" class="form-control" id="dap-nom-new-' + Index_Data_Creation + '"></td>' +
					'<td class="text-center"><input type="number" class="form-control" id="dap-i-new-' + Index_Data_Creation + '" value="1" min="1" max="4"></td>' +
					'<td class="text-center"><input type="number" class="form-control" id="dap-c-new-' + Index_Data_Creation + '" value="1" min="1" max="4"></td>' +
					'<td class="text-center"><input type="number" class="form-control" id="dap-t-new-' + Index_Data_Creation + '" value="1" min="1" max="4"></td>' +
					'<td class="text-center">&nbsp;</td>' +
					'</tr>';

				$('#superModal tbody').append( New_Occurrence );
			});



			$('#formAjouterModifierData').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				// Gestion de la suppression des données
				var Liste_ID_A_Supprimer = [];

				$('input[id^=dap-delete-]').each( function( index, valeur ){
					if ( $( this ).is(":checked") == true ) {
						Liste_ID_A_Supprimer.push($(this).attr('id').split('-')[2]);
					}
				});

				if ( Liste_ID_A_Supprimer != [] && Liste_ID_A_Supprimer != '' ) {
					$.ajax({
						url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer_Donnees',
						type: 'POST',
						data: $.param({'app_id': ID_Application, 'act_id': ID_Activite, 'liste_a_supprimer': Liste_ID_A_Supprimer}), // les paramètres sont protégés avant envoi
						dataType: 'json',
						success: function( reponse ) {
							statut = reponse['statut'];
							texteMsg = reponse['texteMsg'];

							if (statut == 'success') {
								for( let supp_id of Liste_ID_A_Supprimer) {
									$('tr#dap-' + supp_id).remove();
									$('table#tabapp-' + ID_Application + '-' + ID_Activite + ' tbody tr#dap_id-' + supp_id).remove();
								}
							}

							afficherMessage( texteMsg, statut, 'body' );
						}
					});
				}


				// Gestion de la création des données
				$('#superModal table tbody tr.data-creation').each( function( index, valeur ){
					ID_New = $(this).attr('id').split('-')[2];
					Nom_New = $('input#dap-nom-new-'+ID_New).val();

					if ( Nom_New != '' ) {
						Integrite_New = $('input#dap-i-new-'+ID_New).val();
						Confidentialite_New = $('input#dap-c-new-'+ID_New).val();
						Tracabilite_New = $('input#dap-t-new-'+ID_New).val();

						$.ajax({
							url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Creer_Donnees',
							type: 'POST',
							data: $.param({'app_id': ID_Application, 'act_id': ID_Activite,
								'dap_nom_donnee_application': Nom_New, 'dap_integrite_donnee_application': Integrite_New,
								'dap_confidentialite_donnee_application': Confidentialite_New,
								'dap_tracabilite_donnee_application': Tracabilite_New}), // les paramètres sont protégés avant envoi
							dataType: 'json',
							success: function( reponse ) {
								statut = reponse['statut'];
								texteMsg = reponse['texteMsg'];

								if (statut == 'success') {
									$('table#tabapp-' + ID_Application + '-' + ID_Activite + ' tbody').append( '<tr id="dap_id-' + reponse['dap_id'] + '">' + 
										'<td>' + reponse['dap_nom'] + '</td>' +
										'<td class="text-center">' + reponse['dap_integrite'] + '</td>' +
										'<td class="text-center">' + reponse['dap_confidentialite'] + '</td>' +
										'<td class="text-center">' + reponse['dap_tracabilite'] + '</td>' +
										'</tr>' );
								}

								afficherMessage( texteMsg, statut, 'body' );
							}
						});
					} else {
						$('tr#new-data-'+ID_New).remove();
					}
				});


				// Gestion de la modification des données
				$("#superModal table tbody tr.data-modification").each( function( index, valeur ){
					ID_Modification = $(this).attr('id').split('-')[1];

					Nom_New = $('input#dap-nom-'+ID_Modification).val();
					Nom_Old = $('input#dap-nom-'+ID_Modification).attr('data-old_value');
					Integrite_New = $('input#dap-i-'+ID_Modification).val();
					Integrite_Old = $('input#dap-i-'+ID_Modification).attr('data-old_value');
					Confidentialite_New = $('input#dap-c-'+ID_Modification).val();
					Confidentialite_Old = $('input#dap-c-'+ID_Modification).attr('data-old_value');
					Tracabilite_New = $('input#dap-t-'+ID_Modification).val();
					Tracabilite_Old = $('input#dap-t-'+ID_Modification).attr('data-old_value');

					if ( (Nom_New != Nom_Old) || (Integrite_New != Integrite_Old) || (Confidentialite_New != Confidentialite_Old)
						|| (Tracabilite_New != Tracabilite_Old) ) {
						S_ID_Modification = ID_Modification
						S_Nom_New = Nom_New;
						S_Integrite_New = Integrite_New;
						S_Confidentialite_New = Confidentialite_New;
						S_Tracabilite_New = Tracabilite_New;

						$.ajax({
							url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier_Donnees',
							type: 'POST',
							data: $.param({'app_id': ID_Application, 'act_id': ID_Activite, 'dap_id': S_ID_Modification, 'dap_nom': S_Nom_New,
								'dap_integrite': S_Integrite_New, 'dap_confidentialite': S_Confidentialite_New, 'dap_tracabilite': S_Tracabilite_New}), // les paramètres sont protégés avant envoi
							dataType: 'json',
							success: function( reponse ) {
								statut = reponse['statut'];
								texteMsg = reponse['texteMsg'];

								if (statut == 'success') {
									//alert('tr#dap_id-' + S_ID_Modification+' '+S_Nom_New);
									$('tr#dap_id-' + S_ID_Modification).replaceWith(
										'<tr id="dap_id-' + S_ID_Modification + '">' +
										'<td>' + S_Nom_New + '</td>' +
										'<td class="text-center">' + S_Integrite_New + '</td>' +
										'<td class="text-center">' + S_Confidentialite_New + '</td>' +
										'<td class="text-center">' + S_Tracabilite_New + '</td>' +
										'</tr>'
										);
								}

								afficherMessage( texteMsg, statut, 'body' );
							}
						});
					}
				});

				$('#superModal').modal('hide'); // Cache la modale d'ajout.
	
				//afficherMessage( texteMsg, statut, 'body' );
			});
		}
	});
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
