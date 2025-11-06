$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first' ), true );


	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});


	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change( function() {
		var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'trier': sens_recherche, 'sct_id': sct_id}),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ){
				var statut = reponse['statut'];
	
				if( statut == 'success' ){
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );

					trier( $( 'div#entete_tableau div.row div:first'), true );
				} else {
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );
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

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ]) );


				// Vérifie s'il y a une limitation à la création des Entités.
				if ( reponse['total'] >= reponse['limitation'] && reponse['limitation'] != 0 ) {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');

					$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']).attr('data-old_title', old_title);
				}

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');
						ModalMAJCampagne( Id );
					});

					// Assigne l'événement "click" sur tous les boutons de Duplication
					$('.btn-dupliquer').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalDupliquerCampagne( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var Libelle = $('#CMP_'+Id).find('div[data-src="cmp_date"]').find('span').text();

						ModalSupprimer( Id, Libelle );
					});
				}

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


// ============================================

function ModalMAJCampagne( cmp_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'cmp_id': cmp_id}),
		dataType: 'json',
		success: function( reponse ) {
			date_courante = new Date();
			var v_cmp_date = '';
			
			var Droits_Entites = reponse['Droits_Entites']['MySecDash-Entites.php']['rights'];
			var Droits_Sites = reponse['Droits_Sites']['MyContinuity-Sites.php']['rights'];

			var Droit_Ajouter_Entite = 0;
			var Droit_Ajouter_Site = 0;

			if (Droits_Entites.find((element) => element == 'RGH_2') == 'RGH_2') {
				Droit_Ajouter_Entite = 1;
			}

			if (Droits_Sites.find((element) => element == 'RGH_2') == 'RGH_2') {
				Droit_Ajouter_Site = 1;
			}

			if ( cmp_id != '' ) {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ];
				if ( reponse['objCampagne']['cmp_date'] != null ) {
					v_cmp_date = reponse['objCampagne']['cmp_date'];
				}
			} else {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ];
				
				Annee = date_courante.getFullYear();
				Mois = date_courante.getMonth()+1;
				if (Mois < 10) Mois = '0'+Mois;
				Jour = date_courante.getDate();
				if (Jour < 10) Jour = '0'+Jour;
				
				v_cmp_date = Annee+'-'+(Mois)+'-'+Jour;
			}


			function rechercherObjetsDansOnglet( occurrence_a_traiter, parent='' ) {
				chp_rechercher_objet = new RegExp($('#chp-rechercher-objet').val(), 'i');
				
				if ( $('#chp-rechercher-objet').val() == '' ) {
					$('#titre-tout-cocher-sites').show();
					$('#titre-tout-cocher-entites').show();
				} else {
					$('#titre-tout-cocher-sites').hide();
					$('#titre-tout-cocher-entites').hide();
				}

				$(occurrence_a_traiter).each( function( index ){
					if (parent == '') {
						var monObjet = this;
					} else {
						var monObjet = $(this).parent().parent().parent();
					}

					Valeur = $( this ).text();

					if (chp_rechercher_objet == '') {
						$( monObjet ).show();
					} else {
						if (Valeur.search(chp_rechercher_objet) >= 0) {
							$( monObjet ).show();
						} else {
							$( monObjet ).hide();
						}
					}
				});
			}


			Corps = '<div class="row g-3">' +
				'<div class="col-lg-2">' +
				'<label class="form-label" for="cmp_date">' + reponse[ 'L_Date' ] + '</label>' +
				'<input id="cmp_date" class="form-control" type="date" required autofocus value="' + v_cmp_date + '">' +
				'</div> <!-- .col-lg-2 -->';
			if (cmp_id == '') {
				Corps += '<div class="col-lg-4">' +
					'<label class="form-label" for="p_cmp_id">' + reponse[ 'L_A_Partir_Precedente_Campagne' ] + '</label>' +
					'<select id="p_cmp_id" class="form-select" disabled title="Bientôt disponible">' +
					'<option value="">---</option>';
					for (let Campagne of reponse['Liste_Campagnes']) {
						Corps += '<option value="' + Campagne.cmp_id + '">' + Campagne.cmp_date + '</option>';
					}
				Corps += '</select>' +
					'</div> <!-- .col-lg-4 -->';
			} else {
				Corps += '<div class="col-lg-2">' +
					'<label class="form-label" for="cmp_flag_validation">' + reponse[ 'L_Validation' ] + '</label>' +
					'<select id="cmp_flag_validation" class="form-select" required>' +
					'<option value="0">' + reponse['L_Non'] + '</option>' +
					'<option value="1">' + reponse['L_Oui'] + '</option>' +
					'</select>' +
					'</div> <!-- .col-lg-2 -->';
			}
			Corps += '</div> <!-- .row -->';

			Corps += '<ul class="nav nav-tabs">' +
				'<li><a id="lister_chk_entites" class="nav-link" href="#">' + reponse[ 'L_Entites'] + '</a></li>' +
				'<li><a id="lister_chk_sites" class="nav-link" href="#">' + reponse[ 'L_Sites'] + '</a></li>' +
				//'<li><a id="lister_chk_echelle_temps" class="nav-link" href="#">' + reponse[ 'L_Echelle_Temps'] + '</a></li>' +
				'<li><a id="lister_chk_matrice_impacts" class="nav-link" href="#">' + reponse[ 'L_Matrice_Impacts'] + '</a></li>' +
				'</ul>' +

				'<div id="zone_action">' +
				 '<div class="row d-none" id="ZoneRecherche">' +
				 '<div id="zone-btn-creer-objet" class="col-1 d-none">' +
				 '<button id="btn-creer-objet" class="btn btn-outline-secondary" title="' + reponse['L_Ajouter'] + '"><i class="bi-plus"></i></button>' +
				 '</div> <!-- .col-1 -->' +
				 '<div class="col-6">' +
				 '<div class="input-group mb-3">' +
				 '<input id="chp-rechercher-objet" class="form-control" type="text" placeholder="' + reponse['L_Rechercher'] + '" aria-label="Chercher" aria-describedby="button-addon2">' +
				 '<label id="btn-rechercher-objets" class="btn btn-outline-secondary" title="' + reponse['L_Rechercher'] + '"><i class="bi-search"></i></label>' +
				 '</div> <!-- .input-group -->' +
				 '</div> <!-- .col-6 -->' +
				 '</div> <!-- #ZoneRecherche -->' +

				 '<div class="input-group mb-3 d-none" id="ZoneCreerEntite">' +
				 '<input id="ent_nom" class="form-control" type="text" placeholder="' + reponse['L_Nom'] + '">' +
				 '<input id="ent_description" class="form-control" type="text" placeholder="' + reponse['L_Description'] + '">' +
				 '<button id="btn-creer-entite" class="btn btn-outline-secondary" type="button">' + reponse['L_Ajouter'] + '</button>' +
				 '<button id="btn-fermer-entite" class="btn btn-outline-secondary" type="button">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerEntite -->' +

				 '<div class="input-group mb-3 d-none" id="ZoneCreerSite">' +
				 '<input id="sts_nom" class="form-control" type="text" placeholder="' + reponse['L_Nom'] + '">' +
				 '<input id="sts_description" class="form-control" type="text" placeholder="' + reponse['L_Description'] + '">' +
				 '<button id="btn-creer-site" class="btn btn-outline-secondary" type="button">' + reponse['L_Ajouter'] + '</button>' +
				 '<button id="btn-fermer-site" class="btn btn-outline-secondary" type="button">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerSite -->' +

				 '<div class="input-group d-none" id="ZoneCreerPartiePrenante">' +
				 '<input type="text" class="form-control text-uppercase" placeholder="' + reponse['L_Nom'] + '" id="ppr_nom">' +
				 '<input type="text" class="form-control text-capitalize" placeholder="' + reponse['L_Prenom'] + '" id="ppr_prenom">' +
				 '<span class="input-group-text"><div class="form-check form-check-reverse">' +
				 '<input class="form-check-input" type="checkbox" value="" id="ppr_interne" checked>' +
				 '<label class="form-check-label" for="ppr_interne_supp">' +reponse['L_Interne'] + '</label>' +
				 '</div></span>' +
				 '<button type="button" class="btn btn-outline-secondary" id="btn-creer-partie_prenante">' + reponse['L_Ajouter'] + '</button>' +
				 '<button type="button" class="btn btn-outline-secondary" id="btn-fermer-partie_prenante">' + reponse['L_Fermer'] + '</button>' +
				 '</div> <!-- #ZoneCreerPartiePrenante -->' +

				'</div> <!-- #zone_action -->' +

				'<div id="onglets_utilisateur" style="height: 520px;">' +
				
				'<div id="zone-x-select-entites" class="d-none">' +
					'<div class="row liste" id="titre-tout-cocher-entites">' +
					'<div class="col-12">' +
					'<div class="form-check">' +
					'<input class="form-check-input" type="checkbox" id="tout-cocher-ent">' +
					'<label class="form-check-label fw-bold fg_bleu" for="tout-cocher-ent">' + reponse['L_Tout_Cocher_Decocher'] + '</label>' +
					'</div>' +
					'</div> <!-- .col-12 -->' +
					'</div> <!-- .row -->' +
					'<div class="row" id="titre-colonne-entites">' +
					'<div class="col-5 fw-bold bg_vert">' + reponse['L_Entite'] + '</div> <!-- .col-5 -->' +
					'<div class="col-1 fw-bold bg_vert">' + reponse['L_Effectif'] + '</div> <!-- .col-1 -->' +
					'<div class="col-4 fw-bold bg_vert">' + reponse['L_CPCA'] + '</div> <!-- .col-4 -->' +
					'<div class="col-2 fw-bold bg_vert">' + reponse['L_Date_Entretien'] + '</div> <!-- .col-2 -->' +
					'</div> <!-- .row -->';

				for (let Entite of reponse['Liste_Entites']) {
					if (Entite.associe != null) {
						Associe = ' checked';
						Old_Value = 1;
					} else {
						Associe = '';
						Old_Value = 0;
					}

					Nom_Complet_Entite = Entite.ent_nom
					if (Entite.ent_description != '' && Entite.ent_description != null) Nom_Complet_Entite += ' ('+Entite.ent_description + ')'

					var PartiesPrenantes = '<option value="">' + reponse['L_Aucun'] + '</option>';
					var Option_Selectionnee = '';
					for (let PartiePrenante of reponse['Liste_Parties_Prenantes']) {
						if ( Entite.ppr_id_cpca != null) {
							if (Entite.ppr_id_cpca == PartiePrenante.ppr_id) {
								Option_Selectionnee = ' selected';
							}
						} else {
							Entite.ppr_id_cpca = '';
						}

						if (PartiePrenante.ppr_interne == true) {
							var Type_Partie_Prenante = reponse['L_Interne'];
						} else {
							var Type_Partie_Prenante = reponse['L_Externe'];
						}

						PartiesPrenantes += '<option value="'+PartiePrenante.ppr_id+'"'+Option_Selectionnee+'>'+PartiePrenante.ppr_nom+' '+PartiePrenante.ppr_prenom+' ('+Type_Partie_Prenante+')</option>';
						Option_Selectionnee = '';
					}

					if ( Entite.cmen_date_entretien_cpca != null ) {
						v2_cmp_date = Entite.cmen_date_entretien_cpca;
					} else {
						v2_cmp_date = '';
					}

					if ( Entite.cmen_effectif_total != null ) {
						v_cmen_effectif_total = Entite.cmen_effectif_total;
					} else {
						v_cmen_effectif_total = '';
					}

					Corps += '<div class="row liste occurrence">' +
						'<div class="col-5">' +
						'<div class="form-check">' + 
						'<input class="form-check-input" type="checkbox" value="" data-old_value="'+Old_Value+'" id="entite-'+Entite.ent_id+'"'+Associe+'>' +
						'<label class="form-check-label" for="entite-'+Entite.ent_id+'">'+Nom_Complet_Entite+'</label>' +
						'</div> <!-- .form-check -->' + 
						'</div> <!-- .col-5 -->' +
						'<div class="col-1">' +
						'<input class="form-control" type="text" placeholder="'+reponse['L_Effectif_Total']+'" value="'+v_cmen_effectif_total+'" data-old_value="'+v_cmen_effectif_total+'" id="cmen_effectif_total-'+Entite.ent_id+'">' +
						'</div> <!-- .col-1 -->' +
						'<div class="col-4">' +
						'<div id="x-select-ppr_id_cpca-'+Entite.ent_id+'" class="input-group">' +
						'<select id="ppr_id_cpca-'+Entite.ent_id+'" class="form-select" data-old_value="'+Entite.ppr_id_cpca+'">' +
						PartiesPrenantes +
						'</select>' +
						'<button class="btn btn-outline-secondary" id="btn-section-ajouter-partie_prenante-'+Entite.ent_id+'" type="button" title="Créer"><i class="bi-plus"></i></button>' +
						'</div> <!-- .input-group -->' +
						'</div> <!-- .col-4 -->' +
						'<div class="col-2">' +
						'<input id="cmen_date_entretien_cpca-'+Entite.ent_id+'" class="form-control" type="date" data-old_value="'+v2_cmp_date+'" value="' + v2_cmp_date + '">' +
						'</div> <!-- .col-2 -->' +
						'</div> <!-- .row -->';
				}
				Corps += '</div> <!-- #zone-x-select-entites -->' +


				'<div id="zone-x-select-sites" class="d-none">' +
					'<div class="form-check" id="titre-tout-cocher-sites">' +
					'<input class="form-check-input" type="checkbox" id="tout-cocher-sts">' +
					'<label class="form-check-label fw-bold fg_bleu" for="tout-cocher-sts">' + reponse['L_Tout_Cocher_Decocher'] + '</label>' +
					'</div>';
				for (let Site of reponse['Liste_Sites']) {
					if (Site.associe != null) {
						Associe = ' checked';
						Old_Value = 1;
					} else {
						Associe = '';
						Old_Value = 0;
					}

					Nom_Complet_Site = Site.sts_nom
					if (Site.sts_description != '' && Site.sts_description != null) Nom_Complet_Site += ' ('+Site.sts_description + ')'

					Corps += '<div class="form-check liste">' + 
						'<input class="form-check-input" type="checkbox" value="" data-old_value="'+Old_Value+'" id="site-'+Site.sts_id+'"'+Associe+'>' +
						'<label class="form-check-label" for="site-'+Site.sts_id+'">'+Nom_Complet_Site+'</label>' +
						'</div> <!-- .form-check -->';
				}
				Corps += '</div> <!-- #zone-x-select-sites -->' +


				'<div id="zone-x-select-echelles" class="d-none">';
				if ( reponse['Liste_Echelle_Temps'] != undefined ) {
					Corps += '<div class="row titre">' +
					'<div class="col-2">' + reponse['L_Poids'] + '</div>' +
					'<div class="col-10">' + reponse['L_Nom'] + '</div>' +
					'</div> <!-- .row -->';
					for (let Echelle_Temps of reponse['Liste_Echelle_Temps']) {
						Corps += '<div class="row liste">' + 
							'<div class="col-2">' + Echelle_Temps.ete_poids + '</div>' +
							'<div class="col-10">' + Echelle_Temps.ete_nom_code + '</div>' +
							'</div> <!-- .row -->';
					}
				}

				if ( reponse['Liste_Echelle_Temps'] != undefined ) {
					Corps += '<button id="go-echelle-temps" class="btn btn-primary">'+reponse['L_Go_Echelle_Temps']+'</button>';
				} else {
					Corps += '<h3 class="mt-3 text-center">' + reponse['L_Creer_Campagne_Avant_Echelle'] + '</h3>';
				}

				Corps += '</div> <!-- #zone-x-select-echelles -->' +


				'<div id="zone-x-select-matrices" class="d-none">';
				if ( reponse['Liste_Niveaux_Impact'] != undefined ) {
					Corps += chargerSimpleMatrice(reponse['Liste_Niveaux_Impact'], reponse['Liste_Types_Impact'], reponse['Liste_Matrice_Impacts'], reponse['L_Type'], reponse['L_Niveau']);
				}

				if ( reponse['Liste_Niveaux_Impact'] != undefined ) {
					Corps += '<button id="go-matrices" class="btn btn-primary mt-2">'+reponse['L_Go_Matrice_Impacts']+'</button>';
				} else {
					Corps += '<h3 class="mt-3 text-center">' + reponse['L_Creer_Campagne_Avant_Matrice'] + '</h3>';
				}

				Corps += '</div> <!-- #zone-x-select-matrices -->' +
					'</div> <!-- #onglets_utilisateur -->';


			construireModal( 'idModalCampagne',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJCampagne', 'modal-xxl' );

			// Affiche la modale qui vient d'être créée
			$('#idModalCampagne').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalCampagne').on('shown.bs.modal', function() {
				$('#cmp_date').focus();
				
				$('ul.nav.nav-tabs li:first-child a').trigger('click');

				$('#chp-rechercher-objet').off('keypress').on('keypress', function( eventKey){
					if ( eventKey.which == 13) return false;
				});

/*				$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
					rechercherObjetsDansOnglet();
				}); */

				$('#btn-fermer-entite').off('click').on('click', function(){
					$('#ZoneCreerEntite').addClass('d-none');
					$('#ZoneRecherche').removeClass('d-none');

					$('#ent_nom').val('');
					$('#ent_description').val('');
				});

				$('#btn-fermer-site').off('click').on('click', function(){
					$('#ZoneCreerSite').addClass('d-none');
					$('#ZoneRecherche').removeClass('d-none');

					$('#sts_nom').val('');
					$('#sts_description').val('');
				});

				$('#btn-fermer-partie_prenante').off('click').on('click', function(){
					$('#ZoneCreerPartiePrenante').addClass('d-none');
					$('#ZoneRecherche').removeClass('d-none');

					$('#ppr_nom').val('');
					$('#ppr_prenom').val('');
					$('#ppr_interne').val('');
				});
	
				$('#tout-cocher-ent').on( 'click', function() {
					var Checked = $('#tout-cocher-ent').is(':checked');
					if ( Checked ) {
						$('#zone-x-select-entites input[id^="entite-"]').prop('checked', true);
					} else {
						$('#zone-x-select-entites input[id^="entite-"]').prop('checked', false);
					}
				} );

				$('#tout-cocher-sts').on( 'click', function() {
					var Checked = $('#tout-cocher-sts').is(':checked');
					if ( Checked ) {
						$('#zone-x-select-sites input[id^="site-"]').prop('checked', true);
					} else {
						$('#zone-x-select-sites input[id^="site-"]').prop('checked', false);
					}
				} );
			});

			$('#idModalCampagne').on('hidden.bs.modal', function() {
				$('#idModalCampagne').remove(); // Supprime la modale d'ajout.
			});


			// ===============================

			// Gestion de l'onglet "Entites".
			$('#lister_chk_entites').on('click', function() {
				$('ul.nav.nav-tabs li a').removeClass('active');
				$('#lister_chk_entites').addClass('active');

				$('div[id^="ZoneCreer"]').addClass('d-none');
				$('#ZoneRecherche').removeClass('d-none');

				if ( Droit_Ajouter_Entite == 1 ) {
					$('#zone-btn-creer-objet').removeClass('d-none');
				} else {
					$('#zone-btn-creer-objet').addClass('d-none');
				}

				$('#chp-rechercher-objet').val('');
				rechercherObjetsDansOnglet('.row.liste.occurrence label', 'parent');

				$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
					rechercherObjetsDansOnglet('.row.liste.occurrence label', 'parent');
				});

				$('div[id^="zone-x-select-"]').addClass('d-none');
				$('#zone-x-select-entites').removeClass('d-none');

				$('#btn-creer-objet').off('click').on('click', function(){
					$('#ZoneCreerEntite').removeClass('d-none');
					$('#ZoneRecherche').addClass('d-none');

					$('div#ZoneCreerEntite input:first').focus();

					return false;
				});

				$('button[id^="btn-section-ajouter-partie_prenante"]').off('click').on('click', function(){
					$('#ZoneCreerPartiePrenante').removeClass('d-none');
					$('#ZoneRecherche').addClass('d-none');

					$('div#ZoneCreerPartiePrenante input:first').focus();

					return false;
				});


				$('#btn-creer-entite').off('click').on('click', function(){
					ent_nom = $('#ent_nom').val();
					ent_description = $('#ent_description').val();

					$.ajax({
						url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Entite',
						type: 'POST',
						data: $.param({'ent_nom': ent_nom, 'ent_description': ent_description}),
						dataType: 'json',
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								ent_id = reponse['ent_id'];

								Nom_Complet_Entite = ent_nom;
								if (ent_description != '' && ent_description != null) {
									Nom_Complet_Entite += ' ('+ ent_description + ')';
								}

								var PartiesPrenantes = '<option value="">' + reponse['L_Aucun'] + '</option>';
								for (let PartiePrenante of reponse['Liste_Parties_Prenantes']) {
									if (PartiePrenante.ppr_interne == true) {
										var Type_Partie_Prenante = reponse['L_Interne'];
									} else {
										var Type_Partie_Prenante = reponse['L_Externe'];
									}

									PartiesPrenantes += '<option value="'+PartiePrenante.ppr_id+'"'+'>'+PartiePrenante.ppr_nom+' '+PartiePrenante.ppr_prenom+' ('+Type_Partie_Prenante+')</option>';
								}

								Corps = '<div class="row liste occurrence">' +
									 '<div class="col-5">' +
									  '<div class="form-check">' + 
									   '<input class="form-check-input" type="checkbox" value="" data-old_value="0" id="entite-'+ent_id+'" checked>' +
									   '<label class="form-check-label" for="entite-'+ent_id+'">'+Nom_Complet_Entite+'</label>' +
									  '</div> <!-- .form-check -->' +
									 '</div> <!-- .col-5 -->' +
									 '<div class="col-1">' +
									  '<input id="cmen_effectif_total-'+ent_id+'" class="form-control" type="text" placeholder="'+reponse['L_Effectif_Total']+'" data-old_value="">' +
									 '</div> <!-- .col-1 -->' +
									 '<div class="col-4">' +
									  '<div id="x-select-ppr_id_cpca-'+ent_id+'" class="input-group">' +
									   '<select id="ppr_id_cpca-'+ent_id+'" class="form-select" data-old_value="">' +
									   PartiesPrenantes +
									   '</select>' +
									   '<button id="btn-section-ajouter-partie_prenante-'+ent_id+'" class="btn btn-outline-secondary" type="button" title="'+reponse['L_Creer']+'">' +
									    '<i class="bi-plus"></i>' +
									   '</button>' +
									  '</div> <!-- .input-group -->' +
									 '</div> <!-- .col-4 -->' +
									 '<div class="col-2">' +
									  '<input id="cmen_date_entretien_cpca-'+ent_id+'" class="form-control" type="date" data-old_value="" value="">' +
									 '</div> <!-- .col-2 -->' +
									'</div> <!-- .row .liste -->';
	
								$('#titre-colonne-entites').after( Corps );
								//alert(Corps);

								afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
								$('#btn-fermer-entite').trigger('click');
							} else {
								afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalCampagne', 0, 'n' );
							}
						}
					});
				});


				$('#btn-creer-partie_prenante').off('click').on('click', function(){
					var ppr_nom = $('#ppr_nom').val();
					var ppr_prenom = $('#ppr_prenom').val();
					var ppr_interne = $('#ppr_interne').is(':checked');

					$.ajax({
						url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Partie_Prenante',
						type: 'POST',
						data: $.param({'ppr_nom': ppr_nom, 'ppr_prenom': ppr_prenom, 'ppr_interne': ppr_interne}),
						dataType: 'json',
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								ppr_id = reponse['ppr_id'];
								
								Nom_Complet_Entite = ppr_nom + ' ' + ppr_prenom;
								if (ppr_interne != 1) {
									Nom_Complet_Entite += ' ('+ reponse['L_Externe'] + ')';
								} else {
									Nom_Complet_Entite += ' ('+ reponse['L_Interne'] + ')';
								}

								$('select[id^="ppr_id_cpca-"]').append('<option value="' + reponse['ppr_id'] + '">' + Nom_Complet_Entite + '</option>');

								afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
								$('#btn-fermer-partie_prenante').trigger('click');
							} else {
								afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalCampagne', 0, 'n' );
							}
						}
					});
				});
			});


			// Gestion de l'onglet "Sites".
			$('#lister_chk_sites').on('click', function() {
				$('ul.nav.nav-tabs li a').removeClass('active');
				$('#lister_chk_sites').addClass('active');

				$('div[id^="ZoneCreer"]').addClass('d-none');
				$('#ZoneRecherche').removeClass('d-none');

				if ( Droit_Ajouter_Site == 1 ) {
					$('#zone-btn-creer-objet').removeClass('d-none');
				} else {
					$('#zone-btn-creer-objet').addClass('d-none');
				}

				$('#chp-rechercher-objet').val('');
				rechercherObjetsDansOnglet('.form-check.liste');

				$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
					rechercherObjetsDansOnglet('.form-check.liste');
				});

				$('div[id^="zone-x-select-"]').addClass('d-none');
				$('#zone-x-select-sites').removeClass('d-none');

				$('#btn-creer-objet').off('click').on('click', function(){
					$('#ZoneCreerSite').removeClass('d-none');
					$('#ZoneRecherche').addClass('d-none');

					$('div#ZoneCreerSite input:first').focus();

					return false;
				});


				$('#btn-creer-site').off('click').on('click', function(){
					sts_nom = $('#sts_nom').val();
					sts_description = $('#sts_description').val();

					$.ajax({
						url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Site',
						type: 'POST',
						data: $.param({'sts_nom': sts_nom, 'sts_description': sts_description}),
						dataType: 'json',
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								sts_id = reponse['sts_id'];
								
								Nom_Complet_Site = sts_nom;
								if (sts_description != '' && sts_description != null) {
									Nom_Complet_Site += ' ('+ sts_description + ')';
								}
			
								Corps = '<div class="form-check liste">' + 
									'<input class="form-check-input" type="checkbox" value="" data-old_value="0" id="site-'+sts_id+'" checked>' +
									'<label class="form-check-label" for="site-'+sts_id+'">'+Nom_Complet_Site+'</label>' +
									'</div> <!-- .form-check -->';

								$('#titre-tout-cocher-sites').after( Corps );

								afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
								$('#btn-fermer-site').trigger('click');
							} else {
								afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalCampagne', 0, 'n' );
							}
						}
					});
				});
			});


			// Gestion de l'onglet "Echelles de temps".
			$('#lister_chk_echelle_temps').on('click', function() {
				$('ul.nav.nav-tabs li a').removeClass('active');
				$('#lister_chk_echelle_temps').addClass('active');

				$('div[id^="ZoneCreer"]').addClass('d-none');
				$('#ZoneRecherche').addClass('d-none');

				$('div[id^="zone-x-select-"]').addClass('d-none');
				$('#zone-x-select-echelles').removeClass('d-none');

				$('#go-echelle-temps').on('click', function() {
					document.location = Parameters['URL_BASE'] + '/MyContinuity-EchellesTemps.php';
					return false;
				});
			});


			// Gestion de l'onglet "Matrice des impacts".
			$('#lister_chk_matrice_impacts').on('click', function() {
				$('ul.nav.nav-tabs li a').removeClass('active');
				$('#lister_chk_matrice_impacts').addClass('active');

				$('div[id^="ZoneCreer"]').addClass('d-none');
				$('#ZoneRecherche').addClass('d-none');

				$('div[id^="zone-x-select-"]').addClass('d-none');
				$('#zone-x-select-matrices').removeClass('d-none');

				$('#go-matrices').on('click', function() {
					document.location = Parameters['URL_BASE'] + '/MyContinuity-MatriceImpacts.php';
					return false;
				});
			});


			$('#formMAJCampagne').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( cmp_id == '' ) {
					p_cmp_id = $('#p_cmp_id').val();

					if ( p_cmp_id == '' ) {
						AjouterCampagne();
					} else {
						DupliquerCampagne( p_cmp_id, $('#cmp_date').val() );
					}
				} else {
					ModifierCampagne( cmp_id );
				}
			});

		}
	});

}


function ModalDupliquerCampagne( cmp_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'cmp_id': cmp_id}),
		dataType: 'json',
		success: function( reponse ) {
			date_courante = new Date();

			Titre = reponse['L_Titre_Dupliquer'];
			Bouton = reponse[ 'L_Dupliquer' ];

			// Rappel de la campagne Source
			p_cmp_date = reponse['objCampagne']['cmp_date'];

			// Préparation de la campagne Cible
			Annee = date_courante.getFullYear();
			Mois = date_courante.getMonth()+1;
			if (Mois < 10) Mois = '0'+Mois;
			Jour = date_courante.getDate();
			if (Jour < 10) Jour = '0'+Jour;
			cmp_date = Annee+'-'+(Mois)+'-'+Jour;


			Corps = '<div class="row g-3">' +
				'<div class="col-lg-6">' +
				'<label class="form-label" for="p_cmp_date">' + reponse[ 'L_Source' ] + '</label>' +
				'<input id="p_cmp_date" class="form-control" type="date" disabled value="' + p_cmp_date + '">' +
				'</div> <!-- .col-lg-6 -->' +
				'<div class="col-lg-6">' +
				'<label class="form-label" for="cmp_date">' + reponse[ 'L_Cible' ] + '</label>' +
				'<input id="cmp_date" class="form-control" type="date" required autofocus value="' + cmp_date + '">' +
				'</div> <!-- .col-lg-6 -->' +
				'</div> <!-- .row -->';


			construireModal( 'idModalCampagne',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formDupliquerCampagne' );

			// Affiche la modale qui vient d'être créée
			$('#idModalCampagne').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalCampagne').on('shown.bs.modal', function() {
				$('#cmp_date').focus();

				//document.getElementById('cmp_date').selectionStart = v_cmp_date.length;
			});

			$('#idModalCampagne').on('hidden.bs.modal', function() {
				$('#idModalCampagne').remove(); // Supprime la modale d'ajout.
			});

			$('#formDupliquerCampagne').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				DupliquerCampagne( cmp_id );
			} );

		}
	});

}
